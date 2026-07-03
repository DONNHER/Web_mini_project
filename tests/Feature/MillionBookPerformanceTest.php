<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Category;
use Database\Seeders\MillionBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MillionBookPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_completes_the_one_million_book_challenge_within_constraints()
    {
        // 1. Preparation: ensure categories exist
        $cats = ['Fiction', 'Mystery', 'Science', 'Biography', 'History', 'Fantasy'];
        foreach ($cats as $name) {
            Category::create(['name' => $name]);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // 2. Execute Seeder
        $this->seed(MillionBookSeeder::class);

        $endTime = microtime(true);
        $peakMemory = memory_get_peak_usage();
        $duration = $endTime - $startTime;

        // 3. Performance Assertions
        $this->assertLessThan(600, $duration, "Seeding took too long: {$duration} seconds (Limit: 600s)");
        $this->assertLessThan(512 * 1024 * 1024, $peakMemory, "Memory peak was too high: " . ($peakMemory / 1024 / 1024) . " MB (Limit: 512MB)");

        // 4. Data Volume Assertion
        $count = Book::count();
        $this->assertEquals(1000000, $count, "Book count mismatch. Found: {$count}");

        // 5. Integrity Assertions (Check random samples)
        $sampleBooks = Book::inRandomOrder()->limit(10)->get();
        foreach ($sampleBooks as $book) {
            $this->assertNotNull($book->category_id, "Book #{$book->id} has no category");
            $this->assertNotNull($book->author_id, "Book #{$book->id} has no author_id");
            $this->assertNotNull($book->publisher_id, "Book #{$book->id} has no publisher_id");

            // Verify relationships actually exist in DB
            $this->assertTrue(Category::where('id', $book->category_id)->exists());
            $this->assertTrue(Author::where('id', $book->author_id)->exists());
            $this->assertTrue(Publisher::where('id', $book->publisher_id)->exists());

            // Realism: ISBN check
            $this->assertMatchesRegularExpression('/^978\d{10}$/', $book->isbn);

            // Realism: Pricing distribution
            $this->assertGreaterThanOrEqual(9.99, $book->price);
            $this->assertLessThanOrEqual(149.99, $book->price);
        }

        dump("Challenge Stats - Time: " . round($duration, 2) . "s, Peak Memory: " . round($peakMemory / 1024 / 1024, 2) . " MB");
    }
}
