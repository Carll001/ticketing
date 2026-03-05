<?php

namespace App\Http\Services;

use App\Models\Task;
use App\Models\User;

class TaskService
{
    /**
     * @param array<string, mixed> $validated
     */
    public function create(array $validated, User $creator): Task
    {
        $payload = $this->buildPayload($validated);
        $payload['creator_id'] = $creator->id;

        return Task::create($payload);
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function update(Task $task, array $validated): Task
    {
        $task->update($this->buildPayload($validated));

        return $task->refresh();
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildPayload(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'step_order' => $validated['step_order'],
            'department_assigned_id' => $validated['department_assigned_id'] ?? null,
            'task_preset_id' => $validated['task_preset_id'] ?? null,
        ];
    }
}
