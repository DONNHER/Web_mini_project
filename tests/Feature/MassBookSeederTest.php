<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Database\Seeders\MassBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class MassBookSeederTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_seeds_one_million_books_successfully()
    {
        // 1. Preparation: ensure at least one category exists as required by BookFactory
        Category::factory()->create(['name' => 'Test Category']);

        $startTime = microtime(true);

        // 2. Execute Seeder
        $this->seed(MassBookSeeder::class);

        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $peakMemory = memory_get_peak_usage() / 1024 / 1024;

        // 3. Assertions
        $count = DB::table('books')->count();
        $this->assertEquals(1000000, $count, "The books table should have exactly 1,000,000 records.");

        // Performance assertions (Adjust based on environment if necessary)
        // Usually, 1M inserts should happen within 10 minutes (600s)
        $this->assertLessThan(600, $duration, "Seeding took too long: {$duration} seconds");
        $this->assertLessThan(512, $peakMemory, "Memory peak was too high: {$peakMemory} MB");

        // 4. Data Integrity Check
        $book = DB::table('books')->first();
        $this->assertNotNull($book->title);
        $this->assertNotNull($book->isbn);
        $this->assertNotNull($book->category_id);

        dump("Mass Seeding Completed in " . round($duration, 2) . "s");
        dump("Peak Memory Usage: " . round($peakMemory, 2) . " MB");
    }
}
