<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Loan Confirmation - Loan #' . $this->loan->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for borrowing. Your loan has been confirmed successfully.')
            ->line('Due Date: ' . ($this->loan->due_date ? $this->loan->due_date->format('M d, Y') : 'N/A'))
            ->action('View Loan', route('loans.show', $this->loan))
            ->line('Please remember to return the books on or before the due date.');
    }

    public function toArray($notifiable)
    {
        return [
            'loan_id' => $this->loan->id,
            'due_date' => $this->loan->due_date,
            'message' => 'Your loan #' . $this->loan->id . ' has been confirmed successfully.'
        ];
    }
}
