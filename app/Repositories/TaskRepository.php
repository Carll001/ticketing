<?php

namespace App\Repositories;

use App\Models\Department;
use App\Models\Task;
use App\Models\TaskPreset;
use App\Models\TaskStep;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getForIndex(): Collection
    {
        return Task::query()
            ->latest()
            ->with(['departmentAssigned:id,name', 'creator:id,name', 'assignedTo:id,name'])
            ->get();
    }

    public function getDepartmentOptions(): Collection
    {
        return Department::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getUserOptions(): Collection
    {
        return User::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getTaskPresetOptions(): Collection
    {
        return TaskPreset::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getByIdForShow(string $taskId): Task
    {
        return Task::query()
            ->with([
                'departmentAssigned:id,name',
                'creator:id,name',
                'assignedTo:id,name',
                'steps' => fn ($query) => $query->orderBy('position'),
                'steps.fields' => fn ($query) => $query->orderBy('position'),
            ])
            ->findOrFail($taskId);
    }

    public function create(array $payload): Task
    {
        return Task::query()->create($payload);
    }

    public function update(Task $task, array $payload): Task
    {
        $task->update($payload);

        return $task->refresh();
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function upsertStep(Task $task, array $payload, ?string $stepId = null): TaskStep
    {
        if ($stepId !== null) {
            $existing = $task->steps()->whereKey($stepId)->first();

            if ($existing !== null) {
                $existing->update($payload);

                return $existing->refresh();
            }
        }

        /** @var TaskStep $step */
        $step = $task->steps()->create($payload);

        return $step;
    }

    /**
     * @param list<string> $keepStepIds
     */
    public function deleteStepsNotIn(Task $task, array $keepStepIds): void
    {
        $query = $task->steps();

        if ($keepStepIds !== []) {
            $query->whereNotIn('id', $keepStepIds);
        }

        $query->delete();
    }

    /**
     * @param list<array<string, mixed>> $fields
     */
    public function replaceStepFields(TaskStep $step, array $fields): void
    {
        $step->fields()->delete();

        if ($fields === []) {
            return;
        }

        $step->fields()->createMany($fields);
    }
}
