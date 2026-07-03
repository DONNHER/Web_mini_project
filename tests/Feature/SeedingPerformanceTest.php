<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Database\Seeders\MassBookSeeder;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\DB;

class SeedingPerformanceTest extends TestCase
{
    #[Test]
    public function it_seeds_one_million_records_efficiently()
    {
        // 1. Setup - Clean tables manually (Avoid RefreshDatabase for 1M records)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('order_items')->truncate();
        DB::table('reviews')->truncate();
        DB::table('books')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if (Category::count() === 0) {
            Category::factory()->count(10)->create();
        }

        $startTime = microtime(true);

        // 2. Run Seeder
        $this->seed(MassBookSeeder::class);

        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $peakMemory = memory_get_peak_usage() / 1024 / 1024;

        dump("Seeding Duration: " . round($duration, 2) . "s");
        dump("Peak Memory Usage: " . round($peakMemory, 2) . "MB");

        // 3. Performance Assertions
        $this->assertLessThan(600, $duration, "Seeding took longer than 10 minutes.");
        $this->assertLessThan(1024, $peakMemory, "Memory usage exceeded 1024MB.");

        // 4. Data Volume Assertion
        $this->assertEquals(1000000, Book::count(), "Database does not contain 1M records.");
    }

    #[Test]
    public function it_verifies_data_integrity_and_realism()
    {
        // Sample check from the already seeded data (or seed a smaller batch if empty)
        if (Book::count() === 0) {
             Category::factory()->count(5)->create();
             $this->seed(MassBookSeeder::class);
        }

        // 1. ISBN Checksum Verification
        $sampleBooks = Book::inRandomOrder()->limit(100)->get();
        foreach ($sampleBooks as $book) {
            $this->assertTrue($this->isValidIsbn13($book->isbn), "Invalid ISBN-13 checksum: {$book->isbn}");

            // 2. Foreign Key Integrity
            $this->assertNotNull($book->category_id);
            $this->assertTrue(Category::where('id', $book->category_id)->exists(), "Book references non-existent category.");
        }

        // 3. Distribution Check
        $uniqueTitles = Book::limit(1000)->distinct()->count('title');
        $this->assertGreaterThan(1, $uniqueTitles, "Seeder produced non-unique titles.");
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (strlen($isbn) !== 13 || !is_numeric($isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $isbn[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checksum = (10 - ($sum % 10)) % 10;
        return (int) $isbn[12] === $checksum;
    }
}
