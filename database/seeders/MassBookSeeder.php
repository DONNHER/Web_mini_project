<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MassBookSeeder extends Seeder
{
    private const CHUNK_SIZE = 5000; // Increased chunk size for MySQL
    private const TOTAL_RECORDS = 1000000;

    public function run(): void
    {
        // Performance optimization
        DB::disableQueryLog();
        ini_set('memory_limit', '1024M');

        $this->command->info('--- Starting the Mass 1 Million Book Seeding ---');

        // Prepare IDs
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        if (empty($categoryIds)) {
            $this->command->info('Creating initial categories...');
            for ($i = 0; $i < 10; $i++) {
                DB::table('categories')->insert(['name' => 'Category ' . $i, 'created_at' => now(), 'updated_at' => now()]);
            }
            $categoryIds = DB::table('categories')->pluck('id')->toArray();
        }

        // Clear existing books using truncate (fastest)
        $this->command->info('Clearing existing books...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('books')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $inserted = 0;
        $startTime = microtime(true);
        $now = now()->format('Y-m-d H:i:s');

        while ($inserted < self::TOTAL_RECORDS) {
            $batchSize = min(self::CHUNK_SIZE, self::TOTAL_RECORDS - $inserted);
            $books = [];

            for ($i = 0; $i < $batchSize; $i++) {
                $globalIndex = $inserted + $i + 1;
                $baseIsbn = '978' . str_pad((string)$globalIndex, 9, '0', STR_PAD_LEFT);

                // ISBN-13 Checksum calculation
                $sum = 0;
                for ($j = 0; $j < 12; $j++) {
                    $digit = (int) $baseIsbn[$j];
                    $sum += ($j % 2 === 0) ? $digit : $digit * 3;
                }
                $checksum = (10 - ($sum % 10)) % 10;

                $books[] = [
                    'category_id' => $categoryIds[array_rand($categoryIds)],
                    'title' => 'Book Title ' . $globalIndex,
                    'author' => 'Author Name ' . ($globalIndex % 50000),
                    'isbn' => $baseIsbn . $checksum,
                    'price' => mt_rand(999, 14999) / 100,
                    'stock_quantity' => mt_rand(0, 1000),
                    'format' => ['Paperback', 'Hardcover', 'E-book', 'Audiobook'][mt_rand(0, 3)],
                    'is_active' => true,
                    'published_at' => $now,
                    'publication_year' => 2024,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::beginTransaction();
            DB::table('books')->insert($books);
            DB::commit();

            $inserted += $batchSize;

            if ($inserted % 100000 === 0) {
                $this->command->info("Inserted $inserted / " . self::TOTAL_RECORDS . " records... (Memory: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB)");
                gc_collect_cycles();
            }
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->command->info("--- Challenge Completed! ---");
        $this->command->info("Successfully seeded 1,000,000 books in {$duration} seconds.");
    }
}
