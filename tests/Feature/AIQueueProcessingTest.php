<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Jobs\ProcessAITask;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIQueueProcessingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_dispatches_background_audit_job_via_command()
    {
        Queue::fake();

        // 1. Setup: Create orders that haven't been audited
        $user = User::factory()->create();
        Order::create([
            'user_id' => $user->id,
            'total_amount' => 500,
            'shipping_address' => '123 St',
            'status' => 'pending'
        ]);

        // 2. Run the batch command
        $this->artisan('app:batch-security-audit')->assertExitCode(0);

        // 3. Assert job was pushed to the correct queue
        Queue::assertPushed(ProcessAITask::class, function ($job) {
            return $job->queue === 'ai-tasks';
        });
    }

    #[Test]
    public function it_executes_the_ai_audit_job_successfully()
    {
        // Mock AI Response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => '{"score": 10, "category": "Low", "reason": "Background check passed"}']]]]]
            ], 200),
        ]);

        Config::set('ai.providers.gemini.key', 'test_key');

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 500,
            'shipping_address' => '123 St',
            'status' => 'pending'
        ]);

        // Execute the job directly
        $job = new ProcessAITask($order->id);
        $job->handle(app(\App\Services\AI\FraudDetectionService::class));

        // Assert audit log was created
        $this->assertDatabaseHas('ai_security_logs', [
            'resource_id' => $order->id,
            'risk_category' => 'Low'
        ]);
    }
}
