<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * REFRESH STRATEGY:
     * This table acts as a Materialized View for performance-intensive reporting.
     *
     * 1. TRUNCATE/DELETE: Clear existing data in a transaction to avoid data gaps.
     * 2. REPOPULATE: Run an aggregate INSERT INTO ... SELECT query from the partitioned 'books' table.
     * 3. FREQUENCY: Dispatched hourly via 'app:refresh-materialized-views' command
     *    scheduled in app/Console/Kernel.php.
     * 4. TRIGGER: Can be manually refreshed via Artisan after mass imports.
     */
    public function up(): void
    {
        Schema::create('mv_bestseller_stats', function (Blueprint $table) {
            $table->foreignId('category_id')->primary()->constrained()->onDelete('cascade');
            $table->integer('total_books');
            $table->decimal('avg_price', 10, 2);
            $table->integer('total_inventory');
            $table->integer('bestseller_count');
            $table->timestamp('latest_publication')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mv_bestseller_stats');
    }
};
