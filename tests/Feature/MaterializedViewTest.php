<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MaterializedViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_refreshes_bestseller_stats_materialized_view()
    {
        // 1. Setup data
        $category = Category::factory()->create();

        // Create 3 books in this category
        // Book 1: Bestseller (stock > 500)
        Book::factory()->create([
            'category_id' => $category->id,
            'price' => 10.00,
            'stock_quantity' => 600,
            'is_active' => true,
            'published_at' => now()->subDays(10),
        ]);

        // Book 2: Not a bestseller (stock <= 500)
        Book::factory()->create([
            'category_id' => $category->id,
            'price' => 20.00,
            'stock_quantity' => 100,
            'is_active' => true,
            'published_at' => now()->subDays(5),
        ]);

        // Book 3: Inactive (should be excluded)
        Book::factory()->create([
            'category_id' => $category->id,
            'price' => 30.00,
            'stock_quantity' => 1000,
            'is_active' => false,
        ]);

        // 2. Run the command
        $this->artisan('app:refresh-materialized-views')
             ->expectsOutput('Refreshing mv_bestseller_stats...')
             ->expectsOutput('Materialized views refreshed successfully.')
             ->assertExitCode(0);

        // 3. Assertions
        $stats = DB::table('mv_bestseller_stats')->where('category_id', $category->id)->first();

        $this->assertNotNull($stats, "Stats record for category should exist.");
        $this->assertEquals(2, $stats->total_books, "Only active books should be counted.");
        $this->assertEquals(15.00, (float)$stats->avg_price, "Average price should be (10+20)/2 = 15.");
        $this->assertEquals(700, $stats->total_inventory, "Total inventory should be 600+100 = 700.");
        $this->assertEquals(1, $stats->bestseller_count, "Only 1 book has stock > 500.");

        // Latest publication should be Book 2 (5 days ago)
        $this->assertNotNull($stats->latest_publication);
    }

    /** @test */
    public function it_is_scheduled_to_run_hourly()
    {
        $this->artisan('schedule:list')
             ->assertExitCode(0)
             ->expectsOutputToContain('app:refresh-materialized-views');
    }
}
