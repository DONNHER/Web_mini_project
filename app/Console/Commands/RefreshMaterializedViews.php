<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshMaterializedViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-materialized-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh materialized views for reporting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Refreshing mv_bestseller_stats...');

        DB::transaction(function () {
            // Clear existing data - using delete() instead of truncate()
            // to avoid implicit commit in MySQL within a transaction
            DB::table('mv_bestseller_stats')->delete();

            // Re-populate from books table
            // Note: We use raw query for performance and to match the requested SQL logic
            $now = now();

            DB::statement("
                INSERT INTO mv_bestseller_stats (
                    category_id,
                    total_books,
                    avg_price,
                    total_inventory,
                    bestseller_count,
                    latest_publication,
                    created_at,
                    updated_at
                )
                SELECT
                    category_id,
                    COUNT(*) as total_books,
                    AVG(price) as avg_price,
                    SUM(stock_quantity) as total_inventory,
                    COUNT(CASE WHEN stock_quantity > 500 THEN 1 END) as bestseller_count,
                    MAX(published_at) as latest_publication,
                    ? as created_at,
                    ? as updated_at
                FROM books
                WHERE is_active = true
                GROUP BY category_id
            ", [$now, $now]);
        });

        $this->info('Materialized views refreshed successfully.');
    }
}
