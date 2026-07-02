<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->index('category_id'); // Frequent join/filter pattern
            $table->index('title');
            $table->index('author');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id'); // Frequent join pattern
            $table->index('created_at'); // "order_date" equivalent
            $table->index('status');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('book_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('book_id');
            $table->index('created_at');
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['title']);
            $table->dropIndex(['author']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['book_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['book_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
