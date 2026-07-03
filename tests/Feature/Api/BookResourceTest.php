<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user for authentication if needed, though routes are public for GET
    }

    /** @test */
    public function index_api_does_not_include_description_to_save_bandwidth()
    {
        $category = Category::factory()->create();
        Book::factory()->create([
            'category_id' => $category->id,
            'description' => 'Detailed book description that should be hidden.',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200);
        $response->assertJsonMissing(['description' => 'Detailed book description that should be hidden.']);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'isbn', 'title', 'author', 'price', 'category'
                ]
            ]
        ]);
    }

    /** @test */
    public function show_api_includes_description_for_detail_view()
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'description' => 'Detailed book description that should be shown.',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.description', 'Detailed book description that should be shown.');
    }

    /** @test */
    public function api_prevents_n_plus_one_by_eager_loading_category()
    {
        // Clear cache to ensure we actually hit the database and log queries
        \Illuminate\Support\Facades\Cache::flush();

        $category = Category::factory()->create();
        Book::factory(5)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Enable query logging
        DB::enableQueryLog();

        $this->getJson('/api/books');

        $queries = DB::getQueryLog();

        // We expect:
        // 1. One query for books (with cursor pagination)
        // 2. One query for categories (eager loading)
        // If it were N+1, there would be 1 query for books + 5 queries for categories

        $this->assertLessThan(6, count($queries), "Too many queries detected! Potential N+1 issue.");

        // Find the category query - more robust matching for different database drivers
        $categoryQueries = array_filter($queries, function($query) {
            $sql = strtolower($query['query']);
            return str_contains($sql, 'from "categories"') ||
                   str_contains($sql, 'from `categories`') ||
                   str_contains($sql, 'from categories');
        });

        $this->assertGreaterThanOrEqual(1, count($categoryQueries), "Categories should be eager-loaded.");
        $this->assertCount(1, $categoryQueries, "Categories should be eager-loaded in a single query.");
    }
}
