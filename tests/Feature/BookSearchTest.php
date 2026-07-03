<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Use the 'collection' driver for testing Scout if not otherwise specified
        config(['scout.driver' => 'collection']);
    }

    /** @test */
    public function book_has_correct_searchable_data()
    {
        $category = Category::factory()->create(['name' => 'Science Fiction']);
        $book = Book::factory()->create([
            'title' => 'Dune',
            'author' => 'Frank Herbert',
            'publisher' => 'Chilton Books',
            'description' => 'A great space opera.',
            'category_id' => $category->id,
            'format' => 'Hardcover',
            'is_active' => true,
        ]);

        $searchableArray = $book->toSearchableArray();

        $this->assertEquals('Dune', $searchableArray['title']);
        $this->assertEquals('Frank Herbert', $searchableArray['author']);
        $this->assertEquals('Chilton Books', $searchableArray['publisher']);
        $this->assertEquals('A great space opera.', $searchableArray['description']);
        $this->assertEquals('Science Fiction', $searchableArray['category']);
        $this->assertEquals('Hardcover', $searchableArray['format']);
    }

    /** @test */
    public function only_active_books_are_searchable()
    {
        $activeBook = Book::factory()->create(['is_active' => true]);
        $inactiveBook = Book::factory()->create(['is_active' => false]);

        $this->assertTrue($activeBook->shouldBeSearchable());
        $this->assertFalse($inactiveBook->shouldBeSearchable());
    }

    /** @test */
    public function books_can_be_searched_using_scout()
    {
        // Note: The 'collection' driver searches through all models in memory
        $category = Category::factory()->create();

        Book::factory()->create([
            'title' => 'The Great Gatsby',
            'is_active' => true,
            'category_id' => $category->id
        ]);

        Book::factory()->create([
            'title' => 'Moby Dick',
            'is_active' => true,
            'category_id' => $category->id
        ]);

        // Search for 'Gatsby'
        $results = Book::search('Gatsby')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('The Great Gatsby', $results->first()->title);
    }

    /** @test */
    public function batch_index_command_works_successfully()
    {
        $category = Category::factory()->create();
        Book::factory(10)->create([
            'is_active' => true,
            'category_id' => $category->id
        ]);

        $this->artisan('app:index-books-batch --chunk=5')
             ->expectsOutputToContain('Starting to index 10 active books')
             ->expectsOutputToContain('Indexing completed successfully!')
             ->assertExitCode(0);
    }
}
