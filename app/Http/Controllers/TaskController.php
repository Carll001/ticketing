<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\TaskPresetResource;
use App\Http\Resources\TaskResource;
use App\Models\Department;
use App\Models\Task;
use App\Models\TaskFieldResponse;
use App\Models\TaskPreset;
use App\Models\TaskStep;
use App\Models\TaskStepComment;
use App\Models\TaskStepField;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $departmentIds = $user->departments()->pluck('departments.id');

        $tasks = Task::with(['departmentAssigned', 'creator', 'lastStep.claimedBy'])
            ->withCount('steps')
            ->where(function ($query) use ($user, $departmentIds) {
                $query->whereNull('department_assigned_id')
                    ->orWhere('creator_id', $user->id);

                if ($departmentIds->isNotEmpty()) {
                    $query->orWhereIn('department_assigned_id', $departmentIds);
                }
            })
            ->get();

        return Inertia::render('task/Index', [
            'tasks' => TaskResource::collection($tasks),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $presets = TaskPreset::with(['steps.fields'])->get();
        $departments = Department::all();
        return Inertia::render('task/Create', [
            'departments' => DepartmentResource::collection($departments),
            'presets' => TaskPresetResource::collection($presets),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateTaskPayload($request);

        DB::transaction(function () use ($validated, $request) {
            $costTotal = collect($validated['steps'])
                ->filter(fn($step) => (bool)($step['has_cost'] ?? false))
                ->sum(fn($step) => (float)($step['cost'] ?? 0));

            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'cost_total' => $costTotal > 0 ? $costTotal : null,
                'task_preset_id' => $validated['task_preset_id'] ?: null,
                'department_assigned_id' => $validated['department_id'] ?: null,
                'creator_id' => $request->user()->id,
            ]);

            $this->syncTaskSteps($task, $validated['steps']);
        });

        return redirect()->route('task.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if (!$this->canUserViewTask($task, request()->user())) {
            abort(403, 'You are not allowed to view this task.');
        }

        $task->load(['departmentAssigned', 'preset', 'steps.claimedBy', 'steps.comments.user', 'steps.fields.response']);

        return Inertia::render('task/Show', [
            'task' => TaskResource::make($task),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $task->load(['departmentAssigned', 'preset', 'steps.fields']);
        $presets = TaskPreset::with(['steps.fields'])->get();
        $departments = Department::all();

        return Inertia::render('task/Edit', [
            'task' => TaskResource::make($task),
            'departments' => DepartmentResource::collection($departments),
            'presets' => TaskPresetResource::collection($presets),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $this->validateTaskPayload($request);

        DB::transaction(function () use ($task, $validated) {
            $costTotal = collect($validated['steps'])
                ->filter(fn($step) => (bool)($step['has_cost'] ?? false))
                ->sum(fn($step) => (float)($step['cost'] ?? 0));

            $task->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'cost_total' => $costTotal > 0 ? $costTotal : null,
                'task_preset_id' => $validated['task_preset_id'] ?: null,
                'department_assigned_id' => $validated['department_id'] ?: null,
            ]);

            $task->steps()->delete();
            $this->syncTaskSteps($task, $validated['steps']);
        });

        return redirect()->route('task.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('task.index');
    }

    public function claimStep(Task $task, TaskStep $taskStep)
    {
        if ($taskStep->task_id !== $task->id) {
            abort(404);
        }

        $currentUserId = request()->user()->id;

        if ($taskStep->claimed_by_user_id && $taskStep->claimed_by_user_id !== $currentUserId) {
            throw ValidationException::withMessages([
                'step' => 'This step is already taken by another user.',
            ]);
        }

        if (!$taskStep->claimed_by_user_id) {
            $taskStep->update([
                'claimed_by_user_id' => $currentUserId,
                'claimed_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        return back();
    }

    public function respondStep(Request $request, Task $task, TaskStep $taskStep)
    {
        if ($taskStep->task_id !== $task->id) {
            abort(404);
        }

        $currentUserId = $request->user()->id;
        if ($taskStep->claimed_by_user_id !== $currentUserId) {
            abort(403, 'Only the user who claimed this step can submit responses.');
        }

        $validated = $request->validate([
            'responses' => ['nullable', 'array'],
            'responses.*.field_id' => ['required', 'uuid'],
            'responses.*.value' => ['nullable'],
            'submitted_cost' => ['nullable', 'numeric', 'min:0'],
            'proof_type' => ['nullable', 'in:text,image,file'],
            'proof_text' => ['nullable', 'string'],
            'proof_files' => ['nullable', 'array'],
            'proof_files.*' => ['file', 'max:10240'],
        ]);

        if ($taskStep->has_cost && ($validated['submitted_cost'] ?? null) === null) {
            throw ValidationException::withMessages([
                'submitted_cost' => 'Cost is required for this step.',
            ]);
        }

        $responses = $validated['responses'] ?? [];
        $allowedFieldIds = $taskStep->fields()->pluck('id')->all();
        foreach ($responses as $item) {
            if (!in_array($item['field_id'], $allowedFieldIds, true)) {
                throw ValidationException::withMessages([
                    'responses' => 'One or more fields are invalid for this step.',
                ]);
            }
        }

        foreach ($responses as $item) {
            TaskFieldResponse::updateOrCreate(
                ['task_step_field_id' => $item['field_id']],
                ['value' => ['value' => $item['value']]]
            );
        }

        $proofType = null;
        $proofText = null;
        $proofFiles = null;

        if ($taskStep->allow_proof) {
            $proofType = $validated['proof_type'] ?? null;

            if ($proofType === 'text') {
                if (!isset($validated['proof_text']) || trim((string)$validated['proof_text']) === '') {
                    throw ValidationException::withMessages([
                        'proof_text' => 'Proof text is required when proof type is text.',
                    ]);
                }
                if ($taskStep->proof_file_path) {
                    Storage::disk('public')->delete($taskStep->proof_file_path);
                }
                if (is_array($taskStep->proof_files ?? null)) {
                    foreach ($taskStep->proof_files as $fileMeta) {
                        $path = data_get($fileMeta, 'path');
                        if ($path) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }
                $proofText = trim((string)$validated['proof_text']);
            } elseif (in_array($proofType, ['image', 'file'], true)) {
                $uploadedFiles = $request->file('proof_files', []);
                if (empty($uploadedFiles)) {
                    if ($taskStep->proof_type === $proofType && !empty($taskStep->proof_files)) {
                        $proofFiles = $taskStep->proof_files;
                    } else {
                        throw ValidationException::withMessages([
                            'proof_files' => 'At least one proof file is required for image/file proof type.',
                        ]);
                    }
                } else {
                    if ($taskStep->proof_file_path) {
                        Storage::disk('public')->delete($taskStep->proof_file_path);
                    }
                    if (is_array($taskStep->proof_files ?? null)) {
                        foreach ($taskStep->proof_files as $fileMeta) {
                            $path = data_get($fileMeta, 'path');
                            if ($path) {
                                Storage::disk('public')->delete($path);
                            }
                        }
                    }

                    $proofFiles = [];
                    foreach ($uploadedFiles as $uploaded) {
                        if ($proofType === 'image' && !str_starts_with((string)$uploaded->getMimeType(), 'image/')) {
                            throw ValidationException::withMessages([
                                'proof_files' => 'Proof type image requires image files only.',
                            ]);
                        }

                        $path = $uploaded->store("task-proofs/{$task->id}/{$taskStep->id}", 'public');
                        $proofFiles[] = [
                            'path' => $path,
                            'name' => $uploaded->getClientOriginalName(),
                            'mime' => $uploaded->getClientMimeType(),
                            'size' => $uploaded->getSize(),
                        ];
                    }
                }
            }
        }

        $taskStep->update([
            'submitted_cost' => $taskStep->has_cost ? ($validated['submitted_cost'] ?? null) : null,
            'proof_type' => $taskStep->allow_proof ? $proofType : null,
            'proof_text' => $taskStep->allow_proof ? $proofText : null,
            'proof_files' => $taskStep->allow_proof ? $proofFiles : null,
            'proof_file_path' => null,
            'proof_file_name' => null,
            'proof_file_mime' => null,
            'status' => 'done',
        ]);

        return back();
    }

    public function commentStep(Request $request, Task $task, TaskStep $taskStep)
    {
        if ($taskStep->task_id !== $task->id) {
            abort(404);
        }

        if (!$taskStep->allow_comments) {
            throw ValidationException::withMessages([
                'comment' => 'Discussion is disabled for this step.',
            ]);
        }

        $currentUserId = $request->user()->id;
        $isCreator = $task->creator_id === $currentUserId;
        $isTaker = $taskStep->claimed_by_user_id === $currentUserId;

        if (!$isCreator && !$isTaker) {
            abort(403, 'Only the creator or step taker can comment on this step.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        TaskStepComment::create([
            'task_step_id' => $taskStep->id,
            'user_id' => $currentUserId,
            'message' => $validated['message'],
        ]);

        return back();
    }

    private function validateTaskPayload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'task_preset_id' => ['nullable', 'exists:task_presets,id'],
            'step_order' => ['nullable', 'in:sequential,free'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.title' => ['required', 'string', 'max:255'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.allow_proof' => ['nullable', 'boolean'],
            'steps.*.allowComments' => ['nullable', 'boolean'],
            'steps.*.has_cost' => ['nullable', 'boolean'],
            'steps.*.cost' => ['nullable', 'numeric', 'min:0'],
            'steps.*.fields' => ['required', 'array'],
            'steps.*.fields.*.label' => ['required', 'string', 'max:255'],
            'steps.*.fields.*.type' => ['required', 'in:input,textarea,checkbox'],
            'steps.*.fields.*.required' => ['nullable', 'boolean'],
            'steps.*.fields.*.placeholder' => ['nullable', 'string'],
        ]);
    }

    private function syncTaskSteps(Task $task, array $steps): void
    {
        foreach ($steps as $stepIndex => $stepData) {
            $taskStep = TaskStep::create([
                'task_id' => $task->id,
                'preset_step_id' => null,
                'title' => $stepData['title'],
                'description' => $stepData['description'] ?? null,
                'allow_proof' => (bool)($stepData['allow_proof'] ?? false),
                'allow_comments' => (bool)($stepData['allowComments'] ?? true),
                'has_cost' => (bool)($stepData['has_cost'] ?? false),
                'expected_cost' => (bool)($stepData['has_cost'] ?? false) ? ($stepData['cost'] ?? null) : null,
                'position' => $stepIndex + 1,
                'status' => 'pending',
            ]);

            $existingKeys = [];
            foreach ($stepData['fields'] as $fieldIndex => $fieldData) {
                $baseKey = Str::slug($fieldData['label'], '_');
                if ($baseKey === '') {
                    $baseKey = 'field_' . ($fieldIndex + 1);
                }

                $key = $baseKey;
                $suffix = 1;
                while (in_array($key, $existingKeys, true)) {
                    $suffix++;
                    $key = "{$baseKey}_{$suffix}";
                }
                $existingKeys[] = $key;

                TaskStepField::create([
                    'task_step_id' => $taskStep->id,
                    'preset_field_id' => null,
                    'label' => $fieldData['label'],
                    'key' => $key,
                    'type' => $fieldData['type'] === 'input' ? 'text' : $fieldData['type'],
                    'required' => (bool)($fieldData['required'] ?? false),
                    'options' => null,
                    'position' => $fieldIndex + 1,
                ]);
            }
        }
    }

    private function canUserViewTask(Task $task, User $user): bool
    {
        if ($task->creator_id === $user->id) {
            return true;
        }

        if (!$task->department_assigned_id) {
            return true;
        }

        return $user->departments()
            ->where('departments.id', $task->department_assigned_id)
            ->exists();
    }
}
