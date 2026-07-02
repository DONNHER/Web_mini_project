<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::factory()->create();
        Book::factory()->count(5)->create([
            'category_id' => $category->id,
            'stock_quantity' => 10,
        ]);

        // Clear rate limiter to avoid 429 during tests
        RateLimiter::clear('api:'.request()->ip());
    }

    public function test_it_transforms_snake_case_to_camel_case()
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200);

        $data = $response->json('data.data.0');

        $this->assertArrayHasKey('stockQuantity', $data);
        $this->assertArrayHasKey('categoryId', $data);
        $this->assertArrayHasKey('createdAt', $data);

        $this->assertArrayNotHasKey('stock_quantity', $data);
        $this->assertArrayNotHasKey('category_id', $data);
    }

    public function test_it_filters_fields_based_on_query_parameter()
    {
        $response = $this->getJson('/api/books?fields=title,author');

        $response->assertStatus(200);

        $data = $response->json('data.data.0');

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('author', $data);

        $this->assertArrayNotHasKey('price', $data);
        $this->assertArrayNotHasKey('stockQuantity', $data);
    }

    public function test_it_uses_cursor_based_pagination()
    {
        $response = $this->getJson('/api/books?per_page=2');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'data',
                'nextCursor',
            ]
        ]);

        $this->assertCount(2, $response->json('data.data'));
        $this->assertNotNull($response->json('data.nextCursor'));
    }

    public function test_it_supports_etag_caching()
    {
        // Bypass throttling for this specific test to avoid 429
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $response = $this->getJson('/api/books');
        $response->assertStatus(200);

        $etag = $response->headers->get('ETag');

        $this->assertNotNull($etag);

        $response2 = $this->withHeaders([
            'If-None-Match' => $etag,
        ])->getJson('/api/books');

        $response2->assertStatus(304);
    }

    public function test_it_includes_api_metadata()
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'meta' => [
                'version',
                'timestamp'
            ]
        ]);

        $this->assertEquals('1.1', $response->json('meta.version'));
    }
}
