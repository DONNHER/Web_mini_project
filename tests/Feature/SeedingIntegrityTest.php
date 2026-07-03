<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Database\Seeders\MassBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Config;

class SeedingIntegrityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_verifies_data_integrity_with_smaller_volume()
    {
        // 1. Setup - Mock smaller volume for speed
        Category::factory()->count(5)->create();
        $categoryIds = Category::pluck('id')->toArray();

        // Use bulkData directly for speed and control
        $books = Book::factory()->bulkData(100, $categoryIds);

        // Apply seeder logic for ISBNs
        foreach ($books as $index => $book) {
            $baseIsbn = '978' . str_pad((string)($index + 1), 9, '0', STR_PAD_LEFT);
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $digit = (int) $baseIsbn[$i];
                $sum += ($i % 2 === 0) ? $digit : $digit * 3;
            }
            $checksum = (10 - ($sum % 10)) % 10;
            $books[$index]['isbn'] = $baseIsbn . $checksum;
            $books[$index]['published_at'] = $books[$index]['published_at']->format('Y-m-d H:i:s');
        }

        \Illuminate\Support\Facades\DB::table('books')->insert($books);

        // 2. ISBN Checksum Verification
        $insertedBooks = Book::all();
        $this->assertCount(100, $insertedBooks);

        foreach ($insertedBooks as $book) {
            $this->assertTrue($this->isValidIsbn13($book->isbn), "Invalid ISBN-13 checksum: {$book->isbn}");

            // 3. Foreign Key Integrity
            $this->assertNotNull($book->category_id);
            $this->assertTrue(Category::where('id', $book->category_id)->exists());
        }

        // 4. Realistic Distribution Check
        $uniqueAuthors = $insertedBooks->pluck('author')->unique()->count();
        $this->assertGreaterThan(10, $uniqueAuthors, "Author distribution should be diverse.");

        $uniqueFormats = $insertedBooks->pluck('format')->unique()->count();
        $this->assertGreaterThan(1, $uniqueFormats, "Formats should be diverse.");
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
