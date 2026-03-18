<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStepCompletedNotification extends Notification implements ShouldQueue
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
        $mail = (new MailMessage)
            ->subject("Step completed: {$this->taskStep->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->actor->name} completed a task step.")
            ->line("Task: {$this->task->title}")
            ->line("Step: {$this->taskStep->title}");

        if ($this->taskStep->submitted_cost !== null) {
            $mail->line('Submitted cost: ' . number_format((float) $this->taskStep->submitted_cost, 2, '.', ''));
        }

        if ($this->taskStep->proof_type !== null) {
            $proofCount = is_array($this->taskStep->proof_files) ? count($this->taskStep->proof_files) : 0;
            $mail->line(
                $proofCount > 0
                    ? "Proof submitted: {$this->taskStep->proof_type} ({$proofCount} file(s))"
                    : "Proof submitted: {$this->taskStep->proof_type}"
            );
        }

        return $mail->action('View Task', route('task.show', $this->task));
    }
}
