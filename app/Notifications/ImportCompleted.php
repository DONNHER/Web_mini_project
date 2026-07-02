<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $failures;

    public function __construct($message, $failures = [])
    {
        $this->message = $message;
        $this->failures = $failures;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Data Import Completed')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->message);
            
        if (count($this->failures) > 0) {
            $mail->line('Some rows could not be imported:');
            foreach (array_slice($this->failures, 0, 10) as $failure) {
                $mail->line('Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()));
            }
        }
        
        return $mail->action('View Dashboard', url('/admin/dashboard'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'import_completed',
            'message' => $this->message,
            'failure_count' => count($this->failures),
        ];
    }
}