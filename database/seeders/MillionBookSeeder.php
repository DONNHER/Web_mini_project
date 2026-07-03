<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MillionBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * The 1 Million Book Challenge
     * Constraints: < 512MB RAM, < 10 Minutes
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        DB::disableQueryLog();

        $this->command->info('--- Starting the 1 Million Book Challenge ---');

        // 0. Clean up existing data for fresh performance test
        DB::table('books')->truncate();
        DB::table('authors')->truncate();
        DB::table('publishers')->truncate();

        // 1. Prepare Related Data
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        if (empty($categoryIds)) {
            $this->command->info('Creating initial categories...');
            $cats = ['Fiction', 'Mystery', 'Science', 'Biography', 'History', 'Fantasy'];
            foreach ($cats as $name) {
                DB::table('categories')->insert([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $categoryIds = DB::table('categories')->pluck('id')->toArray();
        }

        $this->command->info('Creating authors and publishers...');
        $authorsData = [];
        for ($i = 1; $i <= 1000; $i++) {
            $authorsData[] = ['name' => 'Author ' . $i, 'created_at' => now(), 'updated_at' => now()];
            if ($i % 500 === 0) {
                DB::table('authors')->insert($authorsData);
                $authorsData = [];
            }
        }
        $authorIds = DB::table('authors')->pluck('id')->toArray();

        $publishersData = [];
        for ($i = 1; $i <= 500; $i++) {
            $publishersData[] = ['name' => 'Publisher ' . $i, 'created_at' => now(), 'updated_at' => now()];
            if ($i % 500 === 0) {
                DB::table('publishers')->insert($publishersData);
                $publishersData = [];
            }
        }
        $publisherIds = DB::table('publishers')->pluck('id')->toArray();

        // 2. Seed Books in Batches
        $total = 1000000;
        $batchSize = 1000; // Efficient but safe for 128MB limit
        $batches = $total / $batchSize;

        $startTime = microtime(true);
        $this->command->info('Seeding 1,000,000 books...');

        for ($b = 0; $b < $batches; $b++) {
            DB::beginTransaction();
            try {
                // Using the optimized bulkData method
                $data = \App\Models\Book::factory()->bulkData($batchSize, $categoryIds, $authorIds, $publisherIds);

                // Fast ISBN generation with valid checksum
                foreach ($data as $key => $val) {
                    $globalIndex = ($b * $batchSize) + $key + 1;
                    $baseIsbn = '978' . str_pad($globalIndex, 9, '0', STR_PAD_LEFT);

                    $sum = 0;
                    for ($i = 0; $i < 12; $i++) {
                        $digit = (int) $baseIsbn[$i];
                        $sum += ($i % 2 === 0) ? $digit : $digit * 3;
                    }
                    $checksum = (10 - ($sum % 10)) % 10;

                    $data[$key]['isbn'] = $baseIsbn . $checksum;
                    $data[$key]['published_at'] = $data[$key]['published_at']->format('Y-m-d H:i:s');
                }

                DB::table('books')->insert($data);
                DB::commit();

                unset($data); // Immediate memory free

                if (($b + 1) * $batchSize % 100000 === 0) {
                    $progress = ($b + 1) * $batchSize;
                    $this->command->info("Progress: $progress / $total seeded... (Memory: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB)");
                    gc_collect_cycles();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error('Seeding failed: ' . $e->getMessage());
                throw $e;
            }
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->command->info("--- Challenge Completed! ---");
        $this->command->info("Total Time: {$duration} seconds.");
        $this->command->info("Peak Memory: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . " MB");
    }
}
