<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ?User $actor = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your account has been added')
            ->greeting("Hello {$notifiable->name},")
            ->line('An account has been created for you in the system.');

        if ($this->actor?->name) {
            $mail->line("Added by: {$this->actor->name}");
        }

        return $mail
            ->line('You can sign in using the email address assigned to your account.')
            ->action('Go to Login', route('login'));
    }
}
