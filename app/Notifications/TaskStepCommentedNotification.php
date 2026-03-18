<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TaskStepCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly TaskStep $taskStep,
        private readonly User $actor,
        private readonly string $message,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New comment on step: {$this->taskStep->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->actor->name} commented on a task step.")
            ->line("Task: {$this->task->title}")
            ->line("Step: {$this->taskStep->title}")
            ->line('Comment: ' . Str::limit(trim($this->message), 200))
            ->action('View Task', route('task.show', $this->task));
    }
}
