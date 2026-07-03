<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Covering index for catalog listing (id is usually implicit, but including columns used in listing)
            // Note: SQLite index limits might apply, but this helps for listing
            $table->index(['created_at', 'id'], 'books_listing_index');

            // Composite index for Category Filtering
            $table->index(['category_id', 'created_at'], 'books_category_filter_index');

            // Ensure ISBN unique index (should already exist from create_books_table)
            // $table->unique('isbn');
        });

        // MySQL FULLTEXT index (if using MySQL)
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE books ADD FULLTEXT books_fulltext_index (title, author, description)');
        }
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('books_listing_index');
            $table->dropIndex('books_category_filter_index');
        });

        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE books DROP INDEX books_fulltext_index');
        }
    }
};
