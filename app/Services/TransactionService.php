<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskStep;
use App\Models\Transaction;
use App\Models\User;

class TransactionService
{
    public function log(string $action, array $payload = []): Transaction
    {
        /** @var Task|string|null $task */
        $task = $payload['task'] ?? null;
        /** @var TaskStep|string|null $taskStep */
        $taskStep = $payload['taskStep'] ?? null;
        /** @var User|string|null $actor */
        $actor = $payload['actor'] ?? null;

        return Transaction::create([
            'task_id' => $task instanceof Task ? $task->id : $task,
            'task_step_id' => $taskStep instanceof TaskStep ? $taskStep->id : $taskStep,
            'actor_user_id' => $actor instanceof User ? $actor->id : $actor,
            'action' => $action,
            'summary' => (string)($payload['summary'] ?? ''),
            'meta' => $payload['meta'] ?? null,
        ]);
    }
}
