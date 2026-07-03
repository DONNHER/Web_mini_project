<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_books_with_realistic_format_based_pricing()
    {
        Category::factory()->create();

        // Create 20 random books and verify each one's price matches its format range
        $books = Book::factory()->count(20)->create();

        foreach ($books as $book) {
            match($book->format) {
                'Paperback' => $this->assertTrue($book->price >= 9.99 && $book->price <= 19.99, "Paperback price $book->price out of range"),
                'Hardcover' => $this->assertTrue($book->price >= 24.99 && $book->price <= 49.99, "Hardcover price $book->price out of range"),
                'E-book'    => $this->assertTrue($book->price >= 4.99 && $book->price <= 14.99, "E-book price $book->price out of range"),
                'Audiobook' => $this->assertTrue($book->price >= 19.99 && $book->price <= 39.99, "Audiobook price $book->price out of range"),
                default     => $this->fail("Unknown format $book->format"),
            };
        }
    }

    /** @test */
    public function it_generates_valid_isbn_13_with_checksum()
    {
        Category::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $book = Book::factory()->create();
            $isbn = $book->isbn;

            $this->assertMatchesRegularExpression('/^978\d{10}$/', $isbn);
            $this->assertTrue($this->isValidIsbn13($isbn), "ISBN $isbn is not valid");
        }
    }

    /** @test */
    public function it_applies_bestseller_state_correctly()
    {
        Category::factory()->create();

        $bestseller = Book::factory()->bestseller()->create();

        $this->assertTrue($bestseller->is_active);
        $this->assertGreaterThanOrEqual(500, $bestseller->stock_quantity);
        $this->assertLessThanOrEqual(5000, $bestseller->stock_quantity);
    }

    /** @test */
    public function it_uses_the_publisher_pool()
    {
        Category::factory()->create();

        $publishers = [
            'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
            'Macmillan Publishers', 'Hachette Book Group', 'Scholastic',
            'Bloomsbury Publishing', 'Oxford University Press', 'Cambridge University Press',
            'Pearson Education', 'Elsevier', 'Wiley', 'Kogan Page',
            'Sourcebooks', 'Chronicle Books'
        ];

        $book = Book::factory()->create();
        $this->assertContains($book->publisher, $publishers);
    }

    /**
     * Helper to verify ISBN-13 checksum logic
     */
    private function isValidIsbn13($isbn): bool
    {
        $isbn = str_replace('-', '', $isbn);
        if (strlen($isbn) !== 13) return false;

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $isbn[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checksum = (10 - ($sum % 10)) % 10;
        return $checksum === (int) $isbn[12];
    }
}
