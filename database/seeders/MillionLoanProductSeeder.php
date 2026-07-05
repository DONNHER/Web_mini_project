<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MillionLoanProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * The 1 Million Loan Product Challenge
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        DB::disableQueryLog();

        $this->command->info('--- Starting the 1 Million Loan Product Challenge ---');

        // 0. Clean up
        DB::table('loan_products')->truncate();

        // 1. Prepare Categories
        $categoryIds = DB::table('loan_categories')->pluck('id')->toArray();
        if (empty($categoryIds)) {
            $cats = ['Personal', 'Business', 'Auto', 'Education', 'Emergency', 'Mortgage'];
            foreach ($cats as $name) {
                DB::table('loan_categories')->insert([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $categoryIds = DB::table('loan_categories')->pluck('id')->toArray();
        }

        // 2. Seed in Batches
        $total = 1000000;
        $batchSize = 2000;
        $batches = $total / $batchSize;

        $startTime = microtime(true);

        for ($b = 0; $b < $batches; $b++) {
            $data = [];
            for ($i = 0; $i < $batchSize; $i++) {
                $data[] = [
                    'category_id' => $categoryIds[array_rand($categoryIds)],
                    'name' => 'Loan Product ' . (($b * $batchSize) + $i + 1),
                    'description' => 'Detailed description for loan product ' . (($b * $batchSize) + $i + 1),
                    'interest_rate' => rand(100, 1500) / 100,
                    'duration_months' => array_rand([3, 6, 12, 24, 36, 48, 60]),
                    'min_amount' => rand(1000, 5000),
                    'max_amount' => rand(10000, 1000000),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('loan_products')->insert($data);

            if (($b + 1) * $batchSize % 100000 === 0) {
                $progress = ($b + 1) * $batchSize;
                $this->command->info("Progress: $progress / $total seeded...");
                gc_collect_cycles();
            }
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        $this->command->info("--- Challenge Completed! ---");
        $this->command->info("Total Time: {$duration} seconds.");
    }
}
