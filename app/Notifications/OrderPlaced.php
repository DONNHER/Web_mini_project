<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Confirmation - Order #' . $this->order->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for your order. Your order has been placed successfully.')
            ->line('Order Total: ₱' . number_format($this->order->total_amount, 2))
            ->action('View Order', route('orders.show', $this->order))
            ->line('We will notify you when your order status changes.');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'message' => 'Your order #' . $this->order->id . ' has been placed successfully.'
        ];
    }
}
