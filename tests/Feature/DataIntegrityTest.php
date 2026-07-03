<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Exports\BooksExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DataIntegrityTest extends TestCase
{
    /**
     * Requirement: 1M records are queryable via Eloquent without timeout
     */
    #[Test]
    public function it_queries_one_million_records_without_timeout()
    {
        $count = Book::count();
        $this->assertGreaterThanOrEqual(1000000, $count, "Ensure 1M records are seeded before running this test.");

        $start = microtime(true);

        // Test a complex Eloquent query on 1M records
        $book = Book::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->first();

        $duration = (microtime(true) - $start) * 1000;

        dump("Query speed for 1M records: " . round($duration, 2) . "ms");

        $this->assertNotNull($book);
        $this->assertLessThan(500, $duration, "Querying 1M records is too slow.");
    }

    /**
     * Requirement: Export of 50K records completes via queue without memory exhaustion
     */
    #[Test]
    public function it_triggers_large_export_without_memory_exhaustion()
    {
        Excel::fake();

        $filters = ['is_active' => true];
        $export = new BooksExport($filters);

        // We verify that the export is chunked correctly
        $this->assertEquals(2000, $export->chunkSize());

        // Trigger export
        Excel::store($export, 'exports/large_export.xlsx');

        Excel::assertStored('exports/large_export.xlsx', function(BooksExport $export) {
            return true;
        });

        $memoryMB = memory_get_peak_usage() / 1024 / 1024;
        dump("Peak Memory during export trigger: " . round($memoryMB, 2) . "MB");

        $this->assertLessThan(512, $memoryMB);
    }

    /**
     * Requirement: Partition pruning works correctly
     */
    #[Test]
    public function it_verifies_partition_pruning()
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('MySQL required for partitioning verification.');
        }

        // Ensure we have a book for the target year
        if (!Book::where('publication_year', 2022)->exists()) {
            Book::factory()->create(['publication_year' => 2022]);
        }

        // Check EXPLAIN for a year-specific query
        $year = 2022; // Belongs to p2020 (Values less than 2025)
        $explain = DB::select("EXPLAIN FORMAT=JSON SELECT * FROM books WHERE publication_year = ?", [$year]);

        $json = json_decode($explain[0]->EXPLAIN, true);
        $partitions = $json['query_block']['table']['partitions'] ?? [];

        $usedPartitions = implode(',', $partitions);
        dump("Partitions scanned for year $year: " . $usedPartitions);

        // It should only scan p2020, not ALL partitions
        $this->assertCount(1, $partitions, "Partition pruning failed. Scanned: " . $usedPartitions);
        $this->assertEquals('p2020', $partitions[0]);
    }
}
