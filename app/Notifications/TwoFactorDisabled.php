<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorDisabled extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Two-Factor Authentication Disabled')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Two-factor authentication has been disabled on your account.')
            ->line('If you did not request this, please contact support immediately.')
            ->action('View Security Settings', route('profile.two-factor'));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Two-factor authentication disabled on your account.'
        ];
    }
}