<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\AI\AIServiceManager;
use App\Services\AI\FraudDetectionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIResilienceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement: Feature works with Ollama only (simulate Cloud API outage)
     */
    #[Test]
    public function it_works_with_ollama_only_during_cloud_outage()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100,
            'status' => 'pending',
            'shipping_address' => 'Test Address'
        ]);

        // Mock Gemini failure and Ollama success
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 503),
            'localhost:11434/*' => Http::response([
                'response' => '{"score": 20, "category": "Low", "reason": "Local check passed"}'
            ], 200)
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');
        Config::set('ai.fallback_enabled', true);

        $service = new FraudDetectionService(new AIServiceManager());
        $result = $service->analyzeOrder($order);

        $this->assertEquals('Low', $result['category']);
        $this->assertDatabaseHas('ai_security_logs', [
            'resource_id' => $order->id,
            'provider' => 'ollama'
        ]);
    }

    /**
     * Requirement: Feature degrades gracefully when ALL AI is unavailable
     */
    #[Test]
    public function it_degrades_gracefully_when_all_ai_services_fail()
    {
        $user = User::factory()->create();
        $order = Order::create(['user_id' => $user->id, 'total_amount' => 100, 'status' => 'pending', 'shipping_address' => 'Addr']);

        // Mock complete failure
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
            'api.openai.com/*' => Http::response([], 500),
            'localhost:11434/*' => Http::response([], 500)
        ]);

        $service = new FraudDetectionService(new AIServiceManager());
        $result = $service->analyzeOrder($order);

        // Should return a safe default that requires human intervention
        $this->assertEquals('Manual Review', $result['category']);
        $this->assertStringContainsString('review required', $result['reason']);
    }

    /**
     * Requirement: Data integrity is maintained during failures
     */
    #[Test]
    public function it_maintains_order_integrity_even_if_ai_fails()
    {
        $user = User::factory()->create();

        // Simulate AI crash
        Http::fake(['*' => Http::response([], 500)]);
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'total_amount' => 150.00,
            'shipping_address' => '123 Safe St',
            'items' => [['book_id' => 1, 'quantity' => 1]]
        ]);

        // The API should still succeed with 201 because the order was created
        // even if the AI scan returned a "Manual Review" default.
        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['total_amount' => 150.00]);
        $response->assertJsonPath('security.rating', 'Manual Review');
    }
}
