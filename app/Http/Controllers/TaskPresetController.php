<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\TaskPresetResource;
use App\Models\Department;
use App\Models\TaskPreset;
use App\Models\TaskPresetField;
use App\Models\TaskPresetStep;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskPresetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presets = TaskPreset::with('department')->get();
        return Inertia::render('task-preset/Index', [
            'presets' => TaskPresetResource::collection($presets),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();

        return Inertia::render('task-preset/Create', [
            'departments' => DepartmentResource::collection($departments),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePresetPayload($request);

        DB::transaction(function () use ($validated, $request) {
            $preset = TaskPreset::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'department_id' => $validated['department_id'] ?: null,
                'creator_id' => $request->user()->id,
            ]);

            $this->syncPresetSteps($preset, $validated['steps']);
        });

        return redirect()->route('taskPreset.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskPreset $taskPreset)
    {
        $taskPreset->load(['department', 'steps.fields']);

        return Inertia::render('task-preset/Show', [
            'preset' => TaskPresetResource::make($taskPreset),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskPreset $taskPreset)
    {
        $taskPreset->load(['department', 'steps.fields']);
        $departments = Department::all();

        return Inertia::render('task-preset/Edit', [
            'preset' => TaskPresetResource::make($taskPreset),
            'departments' => DepartmentResource::collection($departments),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskPreset $taskPreset)
    {
        $validated = $this->validatePresetPayload($request);

        DB::transaction(function () use ($taskPreset, $validated) {
            $taskPreset->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'department_id' => $validated['department_id'] ?: null,
            ]);

            $taskPreset->steps()->delete();
            $this->syncPresetSteps($taskPreset, $validated['steps']);
        });

        return redirect()->route('taskPreset.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskPreset $taskPreset)
    {
        $taskPreset->delete();

        return redirect()->route('taskPreset.index');
    }

    private function validatePresetPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
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

    private function syncPresetSteps(TaskPreset $preset, array $steps): void
    {
        foreach ($steps as $stepIndex => $stepData) {
            $presetStep = TaskPresetStep::create([
                'preset_id' => $preset->id,
                'title' => $stepData['title'],
                'description' => $stepData['description'] ?? null,
                'allow_proof' => (bool)($stepData['allow_proof'] ?? false),
                'allow_comments' => (bool)($stepData['allowComments'] ?? true),
                'has_cost' => (bool)($stepData['has_cost'] ?? false),
                'expected_cost' => (bool)($stepData['has_cost'] ?? false) ? ($stepData['cost'] ?? null) : null,
                'position' => $stepIndex + 1,
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

                TaskPresetField::create([
                    'preset_step_id' => $presetStep->id,
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
}
