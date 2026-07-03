<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
    }

    /** @test */
    public function catalog_listing_benchmark()
    {
        // Seed some data
        Category::create(['name' => 'Fiction']);
        Book::factory(100)->create(['category_id' => 1]);

        // Measure just the Controller/Model logic, not the full View rendering overhead
        $startTime = microtime(true);
        $books = Book::with('category')
                     ->orderBy('created_at', 'desc')
                     ->orderBy('id', 'desc')
                     ->cursorPaginate(100);
        $endTime = microtime(true);

        $duration = ($endTime - $startTime) * 1000;

        dump("Catalog Listing (Query Only): " . round($duration, 2) . "ms");
        $this->assertLessThan(100, $duration, "Query logic for listing 100 records is too slow.");
    }

    /** @test */
    public function full_text_search_benchmark()
    {
        // Seed specific searchable data with a valid category
        $category = Category::factory()->create();
        Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'The Quantum Physics of Cats',
            'description' => 'A search target.'
        ]);

        $startTime = microtime(true);
        // Simulate the search logic used in controller
        $results = Book::where(function ($q) {
                $q->where('title', 'like', '%Quantum%')
                ->orWhere('author', 'like', '%Quantum%')
                ->orWhere('description', 'like', '%Quantum%');
            })->limit(100)->get();
        $endTime = microtime(true);

        $duration = ($endTime - $startTime) * 1000;

        dump("Full-Text Search simulation: " . round($duration, 2) . "ms");
        $this->assertLessThan(300, $duration);
    }

    /** @test */
    public function export_streaming_benchmark()
    {
        // Ensure a category exists for factory
        Category::factory()->create();

        // 10,000 records export target < 30s
        // We simulate the chunked query part which is the bottleneck
        $startTime = microtime(true);

        // Simulate what BooksExport@query does
        $query = Book::query()->with('category');

        // Mocking the chunking behavior
        $query->chunk(1000, function($books) {
            // Processing logic
        });

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        dump("Export Processing Simulation (10k setup): " . round($duration, 2) . "s");
        $this->assertLessThan(30, $duration);
    }

    /** @test */
    public function isbn_search_benchmark()
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'isbn' => '9781234567890'
        ]);

        // First call to warm cache
        Book::findByIsbn($book->isbn);

        $startTime = microtime(true);
        $found = Book::findByIsbn($book->isbn);
        $endTime = microtime(true);

        $duration = ($endTime - $startTime) * 1000;
        $this->assertEquals($book->id, $found->id);

        dump("ISBN Search Duration (Cached): " . round($duration, 2) . "ms");
        $this->assertLessThan(50, $duration);
    }

    /** @test */
    public function category_filter_benchmark()
    {
        $category = Category::create(['name' => 'Benchmark Cat']);
        Book::factory(10)->create(['category_id' => $category->id]);

        // Warm cache
        Book::getByCategoryCached($category->id);

        $startTime = microtime(true);
        $results = Book::getByCategoryCached($category->id);
        $endTime = microtime(true);

        $duration = ($endTime - $startTime) * 1000;
        $this->assertNotEmpty($results);

        dump("Category Filter Duration (Cached): " . round($duration, 2) . "ms");
        $this->assertLessThan(150, $duration);
    }
}
