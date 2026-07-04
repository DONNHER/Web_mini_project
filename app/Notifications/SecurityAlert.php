<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Audit;

class SecurityAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $audit;

    public function __construct(Audit $audit)
    {
        $this->audit = $audit;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Security Alert - Critical Action Detected')
            ->greeting('Hello Administrator,')
            ->line('A critical security event has been recorded in the system.')
            ->line('Event: ' . strtoupper($this->audit->event))
            ->line('Model: ' . class_basename($this->audit->auditable_type) . ' (#' . $this->audit->auditable_id . ')')
            ->line('Performed by: ' . ($this->audit->performer->name ?? 'System'))
            ->line('Time: ' . $this->audit->created_at->toDateTimeString())
            ->action('View Audit Details', route('admin.audit-logs.show', ['audit' => $this->audit->id]))
            ->error()
            ->line('If this action was unexpected, please investigate immediately.');
    }

    public function toArray($notifiable)
    {
        return [
            'audit_id' => $this->audit->id,
            'event' => $this->audit->event,
            'message' => 'Critical security event: ' . $this->audit->event . ' on ' . class_basename($this->audit->auditable_type),
        ];
    }
}
