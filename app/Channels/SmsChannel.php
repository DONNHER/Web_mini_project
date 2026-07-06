<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification using Semaphore API.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);
        $phone = $notifiable->phone;

        if (!$phone) {
            Log::warning("Skipping SMS: No phone number for user ID {$notifiable->id}");
            return;
        }

        // Configuration from .env
        $apiKey = env('SEMAPHORE_API_KEY');
        $sender = env('SEMAPHORE_SENDER_NAME', 'PIL-System');

        try {
            $client = new Client();

            // hitting Semaphore API
            $response = $client->post('https://semaphore.co/api/v4/messages', [
                'form_params' => [
                    'apikey' => $apiKey,
                    'number' => $phone,
                    'message' => $message,
                    'sendername' => $sender,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                Log::info("OTP SMS successfully sent via Semaphore to {$phone}");
            } else {
                Log::error("Semaphore Gateway error for {$phone}: Status " . $response->getStatusCode());
            }

        } catch (\Exception $e) {
            Log::error("Failed to send SMS via Semaphore to {$phone}: " . $e->getMessage());
        }
    }
}
