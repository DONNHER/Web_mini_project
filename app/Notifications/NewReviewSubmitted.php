<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Review Submitted')
            ->greeting('Hello Admin,')
            ->line('A new review has been submitted by ' . $this->review->user->name)
            ->line('Book: ' . $this->review->book->title)
            ->line('Rating: ' . $this->review->rating . ' stars')
            ->line('Comment: ' . $this->review->comment)
            ->action('View Review', route('books.show', $this->review->book))
            ->line('Please review and moderate if necessary.');
    }

    public function toArray($notifiable)
    {
        return [
            'review_id' => $this->review->id,
            'user_name' => $this->review->user->name,
            'book_title' => $this->review->book->title,
            'rating' => $this->review->rating,
            'message' => 'New review submitted for ' . $this->review->book->title
        ];
    }
}