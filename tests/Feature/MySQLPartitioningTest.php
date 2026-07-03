<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MySQLPartitioningTest extends TestCase
{
    /** @test */
    public function verify_mysql_connection_and_partitioning()
    {
        // 1. Verify we are actually using MySQL
        $driver = DB::connection()->getDriverName();
        $this->assertEquals('mysql', $driver, 'This test must be run with a MySQL connection (XAMPP).');

        // 2. Check if the 'books' table is partitioned
        $partitions = DB::select("
            SELECT PARTITION_NAME, TABLE_ROWS
            FROM information_schema.PARTITIONS
            WHERE TABLE_NAME = 'books'
            AND TABLE_SCHEMA = DATABASE()
            AND PARTITION_NAME IS NOT NULL
        ");

        $this->assertNotEmpty($partitions, 'The books table is NOT partitioned. Ensure migrations ran successfully on MySQL.');

        $partitionNames = array_map(fn($p) => $p->PARTITION_NAME, $partitions);
        $this->assertContains('p_old', $partitionNames);
        $this->assertContains('p2020', $partitionNames);

        dump("Detected Partitions: " . implode(', ', $partitionNames));
    }

    /** @test */
    public function verify_query_pruning_performance()
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('MySQL required for partitioning test.');
        }

        // 1. Create a category
        $category = Category::factory()->create();

        // 2. Create books in different partitions
        Book::factory()->create([
            'category_id' => $category->id,
            'publication_year' => 1995, // p_old
        ]);
        Book::factory()->create([
            'category_id' => $category->id,
            'publication_year' => 2022, // p2020
        ]);

        // 3. Use EXPLAIN to verify partition pruning
        // We look for books in 2022. It should only scan 'p2020'.
        $explain = DB::select("EXPLAIN FORMAT=JSON SELECT * FROM books WHERE publication_year = 2022");

        $json = json_decode($explain[0]->EXPLAIN, true);
        $usedPartitions = $json['query_block']['table']['partitions'][0] ?? 'unknown';

        dump("Query for 2022 books scanned partitions: " . $usedPartitions);

        $this->assertEquals('p2020', $usedPartitions, 'Query Pruning Failed: The database scanned more partitions than necessary.');
    }
}
