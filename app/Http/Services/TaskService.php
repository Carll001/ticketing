<?php

namespace App\Http\Services;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    /**
     * @param array<string, mixed> $validated
     */
    public function post(array $validated, User $creator): Task
    {
        return DB::transaction(function () use ($validated, $creator): Task {
            $payload = $this->buildTaskPayload($validated);
            $payload['creator_id'] = $creator->id;

            $task = $this->taskRepository->create($payload);

            if (array_key_exists('steps', $validated)) {
                $this->syncTaskSteps($task, is_array($validated['steps']) ? $validated['steps'] : []);
            }

            $this->syncTaskCostTotal($task);

            return $this->taskRepository->getByIdForShow($task->id);
        });
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function update(Task $task, array $validated): Task
    {
        return DB::transaction(function () use ($task, $validated): Task {
            $task = $this->taskRepository->update($task, $this->buildTaskPayload($validated));

            if (array_key_exists('steps', $validated)) {
                $this->syncTaskSteps($task, is_array($validated['steps']) ? $validated['steps'] : []);
            }

            $this->syncTaskCostTotal($task);

            return $this->taskRepository->getByIdForShow($task->id);
        });
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildTaskPayload(array $validated): array
    {
        $taskType = is_string($validated['task_type'] ?? null) ? $validated['task_type'] : null;

        $taskPresetId = $validated['task_preset_id'] ?? null;
        if ($taskType === 'custom') {
            $taskPresetId = null;
        }

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'step_order' => $validated['step_order'],
            'department_assigned_id' => $validated['department_assigned_id'] ?? null,
            'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
            'task_preset_id' => $taskPresetId,
        ];
    }

    /**
     * @param list<array<string, mixed>> $steps
     */
    private function syncTaskSteps(Task $task, array $steps): void
    {
        $keepStepIds = [];

        foreach ($steps as $index => $step) {
            $details = is_array($step['step_details'] ?? null) ? $step['step_details'] : [];
            $additional = is_array($step['additional_details'] ?? null) ? $step['additional_details'] : [];

            $hasCost = (bool) ($additional['has_cost'] ?? false);
            $costAmount = $hasCost ? ($additional['cost_amount'] ?? null) : null;

            $savedStep = $this->taskRepository->upsertStep(
                $task,
                [
                    'title' => $details['title'] ?? '',
                    'description' => $details['description'] ?? null,
                    'position' => $details['position'] ?? $index,
                    'status' => $details['status'] ?? 'pending',
                    'has_cost' => $hasCost,
                    'cost_amount' => $costAmount,
                ],
                is_string($step['id'] ?? null) ? $step['id'] : null
            );

            $keepStepIds[] = $savedStep->id;

            $fields = is_array($step['fields'] ?? null) ? $step['fields'] : [];
            $this->taskRepository->replaceStepFields(
                $savedStep,
                $this->buildFieldPayloads($fields)
            );
        }

        $this->taskRepository->deleteStepsNotIn($task, $keepStepIds);
    }

    /**
     * @param list<array<string, mixed>> $fields
     * @return list<array<string, mixed>>
     */
    private function buildFieldPayloads(array $fields): array
    {
        $result = [];
        $usedKeys = [];

        foreach ($fields as $index => $field) {
            $label = trim((string) ($field['label'] ?? ''));
            $key = trim((string) ($field['key'] ?? ''));
            $normalizedKey = $key !== ''
                ? Str::snake($key)
                : Str::snake(Str::slug($label !== '' ? $label : 'field_'.$index));

            if ($normalizedKey === '') {
                $normalizedKey = 'field_'.$index;
            }

            $baseKey = $normalizedKey;
            $suffix = 1;
            while (in_array($normalizedKey, $usedKeys, true)) {
                $normalizedKey = $baseKey.'_'.$suffix;
                $suffix++;
            }
            $usedKeys[] = $normalizedKey;

            $result[] = [
                'label' => $label !== '' ? $label : 'Field '.($index + 1),
                'key' => $normalizedKey,
                'type' => $this->normalizeFieldType((string) ($field['type'] ?? 'text')),
                'required' => (bool) ($field['required'] ?? false),
                'options' => $field['options'] ?? null,
                'position' => $field['position'] ?? $index,
            ];
        }

        return $result;
    }

    private function normalizeFieldType(string $type): string
    {
        return match (strtolower($type)) {
            'smallinput' => 'text',
            'textarea' => 'textarea',
            'checkbox' => 'checkbox',
            default => in_array($type, ['text', 'textarea', 'number', 'date', 'select', 'checkbox', 'file'], true)
                ? $type
                : 'text',
        };
    }

    private function syncTaskCostTotal(Task $task): void
    {
        $hasCostSteps = $task->steps()->where('has_cost', true);
        $count = $hasCostSteps->count();
        $total = (float) $hasCostSteps->sum('cost_amount');

        $task->update([
            'cost_total' => $count > 0 ? $total : null,
        ]);
    }
}
