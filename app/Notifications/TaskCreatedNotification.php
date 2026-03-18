<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $taskUrl = route('task.show', $this->task);
        $creatorName = $this->task->creator?->name ?? 'A team member';
        $description = $this->task->description ? trim((string) $this->task->description) : null;

        if ($this->task->departmentAssigned) {
            $mail = (new MailMessage)
                ->subject("New task for {$this->task->departmentAssigned->name}: {$this->task->title}")
                ->greeting("Hello {$notifiable->name},")
                ->line("Your department, {$this->task->departmentAssigned->name}, has been assigned a new task.")
                ->line("Task: {$this->task->title}")
                ->line("Created by: {$creatorName}");

            if ($description !== null) {
                $mail->line("Description: {$description}");
            }

            return $mail->action('View Task', $taskUrl);
        }

        $mail = (new MailMessage)
            ->subject("New open task: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line('A new task is open for everyone.')
            ->line("Task: {$this->task->title}")
            ->line("Created by: {$creatorName}");

        if ($description !== null) {
            $mail->line("Description: {$description}");
        }

        return $mail->action('View Task', $taskUrl);
    }
}
