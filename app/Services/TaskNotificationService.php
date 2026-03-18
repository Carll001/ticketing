<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;
use App\Notifications\TaskCreatedNotification;
use App\Notifications\TaskStepClaimedNotification;
use App\Notifications\TaskStepCommentedNotification;
use App\Notifications\TaskStepCompletedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class TaskNotificationService
{
    public function notifyTaskCreated(Task $task): void
    {
        $task->loadMissing(['departmentAssigned', 'creator']);

        $recipients = $task->department_assigned_id
            ? User::query()
                ->whereHas('departments', fn ($query) => $query->where('departments.id', $task->department_assigned_id))
                ->get()
            : User::query()->get();

        $this->send($recipients, new TaskCreatedNotification($task));
    }

    public function notifyStepClaimed(Task $task, TaskStep $taskStep, User $actor): void
    {
        $task->loadMissing(['creator', 'departmentAssigned']);
        $taskStep->loadMissing('claimedBy');

        $this->send(
            [$task->creator],
            new TaskStepClaimedNotification($task, $taskStep, $actor),
        );
    }

    public function notifyStepCommented(Task $task, TaskStep $taskStep, User $actor, string $message): void
    {
        $task->loadMissing(['creator', 'departmentAssigned']);
        $taskStep->loadMissing('claimedBy');

        $this->send(
            [$task->creator, $taskStep->claimedBy],
            new TaskStepCommentedNotification($task, $taskStep, $actor, $message),
            $actor->id,
        );
    }

    public function notifyStepCompleted(Task $task, TaskStep $taskStep, User $actor): void
    {
        $task->loadMissing(['creator', 'departmentAssigned']);
        $taskStep->loadMissing('claimedBy');

        $this->send(
            [$task->creator, $taskStep->claimedBy],
            new TaskStepCompletedNotification($task, $taskStep, $actor),
            $actor->id,
        );
    }

    /**
     * @param  iterable<User|null>  $users
     */
    private function send(iterable $users, object $notification, ?string $excludeUserId = null): void
    {
        $recipients = $this->filterRecipients($users, $excludeUserId);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, $notification);
    }

    /**
     * @param  iterable<User|null>  $users
     * @return Collection<int, User>
     */
    private function filterRecipients(iterable $users, ?string $excludeUserId = null): Collection
    {
        return collect($users)
            ->filter(fn ($user) => $user instanceof User)
            ->filter(fn (User $user) => filled($user->email))
            ->reject(fn (User $user) => $excludeUserId !== null && $user->id === $excludeUserId)
            ->unique('id')
            ->values();
    }
}
