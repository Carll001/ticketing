<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStepClaimedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly TaskStep $taskStep,
        private readonly User $actor,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $departmentName = $this->task->departmentAssigned?->name ?? 'Open for anyone';

        return (new MailMessage)
            ->subject("Step claimed: {$this->taskStep->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your task \"{$this->task->title}\" is now being handled by {$departmentName}.")
            ->line("Claimed step: {$this->taskStep->title}")
            ->line("Claimed by: {$this->actor->name}")
            ->action('View Task', route('task.show', $this->task));
    }
}
