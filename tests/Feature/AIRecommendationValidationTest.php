<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Category;
use App\Services\AI\RecommendationService;
use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIRecommendationValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * [FUNCTIONAL] Feature handles edge cases: New user with no history.
     */
    #[Test]
    public function it_handles_new_users_gracefully_without_ai()
    {
        $user = User::factory()->create();
        Category::factory()->create();
        Book::factory(10)->create(['is_active' => true]);

        $service = new RecommendationService(new AIServiceManager());

        // Should not call AI if history is empty, but return latest books
        Http::fake(['*' => Http::response([], 500)]);

        $results = $service->getRecommendations($user, 5);

        $this->assertCount(5, $results);
        $this->assertNotEmpty($results[0]['title']);
    }

    /**
     * [PERFORMANCE] AI response time is acceptable (< 5 seconds)
     */
    #[Test]
    public function it_generates_recommendations_within_performance_targets()
    {
        $user = User::factory()->create();
        $cat = Category::factory()->create(['name' => 'History']);
        $book = Book::factory()->create(['category_id' => $cat->id, 'is_active' => true]);

        $order = Order::create(['user_id' => $user->id, 'total_amount' => 50, 'status' => 'completed', 'shipping_address' => 'Test']);
        OrderItem::create(['order_id' => $order->id, 'book_id' => $book->id, 'quantity' => 1, 'unit_price' => 50]);

        // Mock 1s delay
        Http::fake([
            'generativelanguage.googleapis.com/*' => function () {
                usleep(1000000);
                return Http::response(['candidates' => [['content' => ['parts' => [['text' => '{"keywords": ["ancient"], "categories": ["History"]}']]]]]], 200);
            }
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');

        $start = microtime(true);
        $service = new RecommendationService(new AIServiceManager());
        $service->getRecommendations($user);
        $duration = microtime(true) - $start;

        dump("Recommendation Latency: " . round($duration, 2) . "s");
        $this->assertLessThan(5, $duration);
    }

    /**
     * [SECURITY] AI outputs are escaped before rendering
     */
    #[Test]
    public function it_escapes_malicious_ai_recommendation_content()
    {
        $user = User::factory()->create();
        $cat = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $cat->id, 'is_active' => true]);
        $order = Order::create(['user_id' => $user->id, 'total_amount' => 50, 'status' => 'completed', 'shipping_address' => 'Test']);
        OrderItem::create(['order_id' => $order->id, 'book_id' => $book->id, 'quantity' => 1, 'unit_price' => 50]);

        // AI returns a malicious keyword
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => '{"keywords": ["<script>alert(1)</script>"], "categories": ["' . $cat->name . '"]}']]]]]
            ], 200),
        ]);

        // Create a book with a "malicious" title that matches the keyword
        Book::factory()->create(['title' => '<script>alert(1)</script>', 'is_active' => true, 'category_id' => $cat->id]);

        Config::set('ai.providers.gemini.key', 'valid_key');

        $response = $this->actingAs($user)->get(route('user.recommendations'));

        $response->assertStatus(200);
        // Verify the tag is escaped and not rendered as executable code
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    /**
     * [RESILIENCE] Fallback activates when primary provider fails
     */
    #[Test]
    public function it_uses_ollama_fallback_for_recommendations()
    {
        $user = User::factory()->create();
        $cat = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $cat->id, 'is_active' => true]);
        $order = Order::create(['user_id' => $user->id, 'total_amount' => 50, 'status' => 'completed', 'shipping_address' => 'Test']);
        OrderItem::create(['order_id' => $order->id, 'book_id' => $book->id, 'quantity' => 1, 'unit_price' => 50]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
            'localhost:11434/*' => Http::response(['response' => '{"keywords": ["local"], "categories": ["Fallback"]}'], 200)
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');
        Config::set('ai.fallback_enabled', true);

        $service = new RecommendationService(new AIServiceManager());
        $service->getRecommendations($user);

        // Verify audit log shows ollama was used for recommendation
        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'ollama',
            'feature' => 'recommendations'
        ]);
    }
}
