<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\AI\FraudDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAITask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $ip;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     * We use this for heavy, non-blocking AI security scans.
     */
    public function __construct(int $orderId, ?string $ip = null)
    {
        $this->orderId = $orderId;
        $this->ip = $ip;
    }

    /**
     * Execute the job.
     */
    public function handle(FraudDetectionService $fraudService): void
    {
        $order = Order::find($this->orderId);

        if (!$order) {
            return;
        }

        Log::info("Starting background AI security audit for Order #{$this->orderId}");

        // Perform the analysis (this might take several seconds)
        $result = $fraudService->analyzeOrder($order, $this->ip);

        // Update order status if AI detects high risk
        if ($result['score'] > 70 || $result['category'] === 'High') {
            $order->update(['status' => 'flagged']);
            Log::warning("Order #{$this->orderId} has been FLAGGED for fraud review. Score: {$result['score']}%");
        }

        Log::info("Background AI audit completed for Order #{$this->orderId}. Result: " . $result['category']);
    }
}
