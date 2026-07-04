<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AISecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement: User inputs are sanitized before AI processing.
     */
    #[Test]
    public function it_sanitizes_malicious_user_input_before_ai_dispatch()
    {
        $user = User::factory()->create();

        $maliciousInput = '"; DROP TABLE users; -- <script>alert("xss")</script>';

        Http::fake([
            'generativelanguage.googleapis.com/*' => function ($request) use ($maliciousInput) {
                // Verify the input is sent as a string within the JSON context, not executable code
                $this->assertStringContainsString(json_encode($maliciousInput), $request->body());
                return Http::response([
                    'candidates' => [['content' => ['parts' => [['text' => '{"score": 50, "category": "Medium", "reason": "Suspicious input"}']]]]]
                ], 200);
            }
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'total_amount' => 100,
            'shipping_address' => $maliciousInput,
            'items' => [['book_id' => 1, 'quantity' => 1]]
        ]);

        $response->assertStatus(201);
    }

    /**
     * Requirement: AI outputs are escaped before rendering.
     */
    #[Test]
    public function it_escapes_ai_generated_content_in_the_dashboard()
    {
        $admin = User::factory()->create();
        $admin->update(['role' => 'admin']); // Avoid truncated column error by updating existing

        $order = Order::create([
            'user_id' => $admin->id,
            'total_amount' => 100,
            'status' => 'flagged',
            'shipping_address' => 'Test'
        ]);

        \Illuminate\Support\Facades\DB::table('ai_security_logs')->insert([
            'feature' => 'fraud_detection',
            'resource_id' => $order->id,
            'resource_type' => 'Order',
            'risk_category' => 'High',
            'provider' => 'gemini',
            'response_time_ms' => 100,
            'input_context' => '{}',
            'reason' => '<script>alert("XSS")</script>',
            'created_at' => now()
        ]);

        $response = $this->actingAs($admin)->get(route('admin.ai-security.index'));

        $response->assertStatus(200);
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
    }

    /**
     * Requirement: Rate limiting prevents abuse.
     */
    #[Test]
    public function it_enforces_rate_limits_on_api()
    {
        $user = User::factory()->create();

        // Tier 'public' is 30/min. We'll simulate 31 requests.
        for ($i = 0; $i < 31; $i++) {
            $response = $this->getJson('/api/books');
        }

        $response->assertStatus(429);
        $response->assertJsonPath('error', 'Rate limit exceeded');
    }
}
