<?php

namespace Tests\Performance;

use App\Models\Order;
use App\Models\User;
use App\Services\AI\FraudDetectionService;
use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement: AI response time is acceptable (< 5 seconds for user-facing features)
     */
    #[Test]
    public function it_completes_fraud_analysis_within_acceptable_time()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 150.00,
            'shipping_address' => 'Test Address',
            'status' => 'pending'
        ]);

        // Mock a realistic response time
        Http::fake([
            'generativelanguage.googleapis.com/*' => function () {
                usleep(500000); // Simulate 500ms network/AI delay
                return Http::response([
                    'candidates' => [['content' => ['parts' => [['text' => '{"score": 10, "category": "Low", "reason": "Verified"}']]]]]
                ], 200);
            }
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');

        $service = new FraudDetectionService(new AIServiceManager());

        $start = microtime(true);
        $service->analyzeOrder($order);
        $duration = (microtime(true) - $start);

        dump("AI Fraud Analysis Time: " . round($duration, 2) . "s");

        $this->assertLessThan(5, $duration, "AI analysis took too long ({$duration}s)");
    }

    /**
     * Requirement: Database queries remain optimized (< 100ms)
     */
    #[Test]
    public function it_verifies_security_context_queries_are_optimized()
    {
        $user = User::factory()->create();

        // Seed 100 orders for this user to make queries non-trivial
        Order::factory(100)->create(['user_id' => $user->id, 'status' => 'completed']);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $service = new FraudDetectionService(new AIServiceManager());

        // Enable query log to check timings
        DB::enableQueryLog();

        // Trigger the context gathering (most query-intensive part)
        $reflection = new \ReflectionClass(FraudDetectionService::class);
        $method = $reflection->getMethod('gatherSecurityContext');
        $method->setAccessible(true);

        $start = microtime(true);
        $method->invoke($service, $order, $user, '127.0.0.1');
        $durationMs = (microtime(true) - $start) * 1000;

        $queries = DB::getQueryLog();

        dump("Security Context DB Time: " . round($durationMs, 2) . "ms");
        dump("Total Queries for Context: " . count($queries));

        foreach ($queries as $query) {
            $this->assertLessThan(100, $query['time'], "Query exceeded 100ms threshold: " . $query['query']);
        }

        $this->assertLessThan(200, $durationMs, "Context gathering overall took too long.");
    }

    /**
     * Requirement: Queue doesn't back up under normal load
     */
    #[Test]
    public function it_verifies_queue_processing_efficiency()
    {
        $user = User::factory()->create();
        $orders = Order::factory(10)->create(['user_id' => $user->id]);

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => '{"score":10,"category":"Low","reason":"OK"}']]]]]], 200)]);
        Config::set('ai.providers.gemini.key', 'valid_key');

        $start = microtime(true);

        foreach ($orders as $order) {
            $job = new \App\Jobs\ProcessAITask($order->id);
            $job->handle(app(FraudDetectionService::class));
        }

        $duration = (microtime(true) - $start);
        $avgPerJob = ($duration / 10) * 1000;

        dump("Avg Queue Task Execution Time: " . round($avgPerJob, 2) . "ms");

        // Normal load (mocked) should process each job in under 1 second of total logic time
        $this->assertLessThan(1000, $avgPerJob, "Queue task processing is too slow.");
    }
}
