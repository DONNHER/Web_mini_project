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
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIRecommendationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_general_books_if_user_has_no_history()
    {
        $user = User::factory()->create();
        Category::factory()->create();
        Book::factory(5)->create(['is_active' => true]);

        $service = new RecommendationService(new AIServiceManager());
        $results = $service->getRecommendations($user, 3);

        $this->assertCount(3, $results);
    }

    #[Test]
    public function it_uses_ai_logic_to_suggest_books_based_on_history()
    {
        // 1. Setup History
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Sci-Fi']);
        $book = Book::factory()->create(['category_id' => $category->id, 'title' => 'Star Wars', 'is_active' => true]);

        $order = Order::create(['user_id' => $user->id, 'total_amount' => 50, 'status' => 'completed', 'shipping_address' => 'Test']);
        OrderItem::create(['order_id' => $order->id, 'book_id' => $book->id, 'quantity' => 1, 'unit_price' => 50]);

        // 2. Setup Mock AI
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => '{"keywords": ["space", "future"], "categories": ["Sci-Fi"]}']]]]]
            ], 200),
        ]);

        Config::set('ai.providers.gemini.key', 'valid_key');

        // 3. Create potential matches
        Book::factory()->create(['title' => 'Space Odyssey', 'is_active' => true, 'category_id' => $category->id]);
        Book::factory()->create(['title' => 'Future World', 'is_active' => true, 'category_id' => $category->id]);

        $service = new RecommendationService(new AIServiceManager());
        $results = $service->getRecommendations($user, 2);

        $this->assertNotEmpty($results);
        $this->assertTrue(collect($results)->contains(fn($b) => str_contains($b['title'], 'Space') || str_contains($b['title'], 'Future')));
    }

    #[Test]
    public function it_integrates_recommendations_into_the_frontend()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.recommendations'));

        $response->assertStatus(200);
        $response->assertSee('AI-Powered Recommendations');
    }
}
