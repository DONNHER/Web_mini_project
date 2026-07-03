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
        // 1. search_index_queue - for tracking pending Scout indexing jobs
        Schema::create('search_index_queue', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('action'); // 'update', 'delete'
            $table->integer('attempts')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('processed_at');
        });

        // 2. query_performance_logs - for tracking slow queries
        Schema::create('query_performance_logs', function (Blueprint $table) {
            $table->id();
            $table->text('sql');
            $table->json('bindings')->nullable();
            $table->float('time_ms', 12, 2);
            $table->string('connection');
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->timestamps();

            $table->index('time_ms');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('query_performance_logs');
        Schema::dropIfExists('search_index_queue');
    }
};
