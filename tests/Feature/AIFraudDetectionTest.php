<?php

namespace Tests\Feature;

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

class AIFraudDetectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_successfully_detects_fraud_and_logs_to_database()
    {
        // 1. Setup
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 5000,
            'shipping_address' => 'Test Address',
            'status' => 'pending'
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => '{"score": 95, "category": "High", "reason": "High value order detected"}']]]]]
            ], 200),
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');

        $service = new FraudDetectionService(new AIServiceManager());

        // 2. Execute
        $result = $service->analyzeOrder($order);

        // 3. Assert Response
        $this->assertEquals('High', $result['category']);

        // 4. Assert Audit Logging
        $this->assertDatabaseHas('ai_security_logs', [
            'resource_id' => $order->id,
            'risk_category' => 'High',
            'provider' => 'gemini'
        ]);
    }

    #[Test]
    public function it_falls_back_to_ollama_when_gemini_fails()
    {
        // 1. Mock Gemini failure and Ollama success
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
            'localhost:11434/*' => Http::response(['response' => '{"score": 45, "category": "Medium", "reason": "Ollama fallback"}'], 200),
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');
        Config::set('ai.fallback_enabled', true);

        $service = new FraudDetectionService(new AIServiceManager());

        // 2. Setup a dummy order
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 150,
            'shipping_address' => 'Test Address',
            'status' => 'pending'
        ]);

        // 3. Run analysis
        $result = $service->analyzeOrder($order);

        // 4. Verify fallback provider was used (recorded in audit log)
        $this->assertEquals(45, $result['score']);
        $this->assertEquals('Medium', $result['category']);

        $this->assertDatabaseHas('ai_security_logs', [
            'resource_id' => $order->id,
            'provider' => 'ollama'
        ]);
    }

    #[Test]
    public function it_integrates_ai_logic_into_the_api_order_flow()
    {
        $user = User::factory()->create();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => '{"score": 10, "category": "Low", "reason": "Safe order"}']]]]]
            ], 200),
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'total_amount' => 100.00,
            'shipping_address' => '123 Test St',
            'items' => [['book_id' => 1, 'quantity' => 1]]
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('security.rating', 'Low')
            ->assertJsonPath('status', 'pending');

        $this->assertDatabaseHas('ai_security_logs', [
            'risk_category' => 'Low'
        ]);
    }

    #[Test]
    public function it_flags_order_status_to_flagged_for_high_risk()
    {
        $user = User::factory()->create();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => '{"score": 99, "category": "High", "reason": "Severe fraud risk"}']]]]]
            ], 200),
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'total_amount' => 99999.00,
            'shipping_address' => 'Fraud Lane 1',
            'items' => [['book_id' => 1, 'quantity' => 100]]
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'flagged');

        // Middleware transforms response keys to camelCase
        $orderId = $response->json('orderId');

        // Disable sharding for the test check
        $order = DB::table('orders')->where('id', $orderId)->first();
        $this->assertNotNull($order);
        $this->assertEquals('flagged', $order->status);
    }
}
