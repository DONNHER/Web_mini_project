<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Covering index for optimized catalog listing
            // Matches: where('is_active', true)->orderBy('published_at', 'desc')->orderBy('id', 'desc')
            $table->index(['is_active', 'published_at', 'id'], 'idx_books_active_published_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_active_published_id');
        });
    }
};
