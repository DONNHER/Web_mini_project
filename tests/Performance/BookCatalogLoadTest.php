<?php

namespace Tests\Performance;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BookCatalogLoadTest extends TestCase
{
    private const MAX_AVG_RESPONSE_TIME = 300;
    private const CONCURRENT_REQUESTS = 50;

    #[Test]
    public function it_simulates_concurrent_catalog_requests()
    {
        // Bypass rate limiting for this test
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $book = Book::where('is_active', true)->first();
        if (!$book) {
            $this->markTestSkipped('No active books found for load testing.');
        }

        $times = [];
        $errors = 0;

        for ($i = 0; $i < self::CONCURRENT_REQUESTS; $i++) {
            $start = microtime(true);
            $response = $this->getJson('/api/books');
            $duration = (microtime(true) - $start) * 1000;

            $times[] = $duration;

            if ($response->status() !== 200) {
                $errors++;
                continue;
            }

            $response->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'isbn', 'price']
                ]
            ]);
        }

        $avgTime = array_sum($times) / count($times);

        dump("--- PERFORMANCE REPORT: CATALOG ---");
        dump("Requests: " . self::CONCURRENT_REQUESTS);
        dump("Average Response: " . number_format($avgTime, 2) . "ms");
        dump("Errors: $errors");

        $this->assertEquals(0, $errors, "Requests failed. Check if API routes are working.");
        $this->assertLessThan(self::MAX_AVG_RESPONSE_TIME, $avgTime, "Target threshold of " . self::MAX_AVG_RESPONSE_TIME . "ms exceeded.");
    }

    #[Test]
    public function it_simulates_concurrent_isbn_lookups()
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $book = Book::where('is_active', true)->first();
        if (!$book) {
            $this->markTestSkipped('No active books found for load testing.');
        }

        $times = [];
        $errors = 0;

        for ($i = 0; $i < self::CONCURRENT_REQUESTS; $i++) {
            $start = microtime(true);
            $response = $this->getJson("/api/books/{$book->id}");
            $duration = (microtime(true) - $start) * 1000;

            $times[] = $duration;

            if ($response->status() !== 200) {
                $errors++;
                continue;
            }

            $response->assertJsonPath('data.id', $book->id);
        }

        $avgTime = array_sum($times) / count($times);

        dump("--- PERFORMANCE REPORT: ISBN LOOKUP ---");
        dump("Average Response: " . number_format($avgTime, 2) . "ms");
        dump("Errors: $errors");

        $this->assertEquals(0, $errors);
        $this->assertLessThan(200, $avgTime);
    }

    #[Test]
    public function it_verifies_cache_is_populated_during_load()
    {
        Cache::flush();
        $book = Book::where('is_active', true)->first();

        if (!$book) {
            $this->markTestSkipped('No active books found. Run: php artisan db:seed --class=MassBookSeeder');
        }

        $this->getJson("/api/books/{$book->id}");

        $start = microtime(true);
        $this->getJson("/api/books/{$book->id}");
        $duration = (microtime(true) - $start) * 1000;

        dump("--- PERFORMANCE REPORT: CACHE ---");
        dump("Cached response speed: " . number_format($duration, 2) . "ms");

        $this->assertLessThan(100, $duration, "Cache is not providing the required performance boost.");
    }
}
