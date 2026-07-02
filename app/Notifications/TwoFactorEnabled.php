<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEnabled extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Two-Factor Authentication Enabled')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Two-factor authentication has been successfully enabled on your account.')
            ->line('Your account is now more secure.')
            ->action('View Security Settings', route('profile.two-factor'))
            ->line('If you did not enable this, please contact support immediately.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Two-factor authentication enabled on your account.'
        ];
    }
}