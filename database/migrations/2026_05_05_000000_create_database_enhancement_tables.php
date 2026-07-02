<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Import Logs
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('rows_processed')->default(0);
            $table->integer('failures')->default(0);
            $table->json('error_details')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Export Logs
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->string('format'); // csv, xlsx, pdf, json
            $table->json('filters')->nullable();
            $table->string('status'); // pending, completed, failed
            $table->string('download_link')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Scheduled Tasks Tracking
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->string('command');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('status'); // success, failed
            $table->text('output')->nullable();
            $table->integer('memory_usage')->nullable(); // in bytes
            $table->timestamps();
        });

        // 4. API Rate Limits Tracking
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('endpoint');
            $table->integer('hits');
            $table->timestamp('throttled_at')->useCurrent();
            $table->integer('retry_after')->nullable();
            $table->timestamps();
        });

        // 5. Backup Monitoring
        Schema::create('backup_monitoring', function (Blueprint $table) {
            $table->id();
            $table->string('backup_name');
            $table->string('status'); // success, failed
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('destination')->nullable(); // disk name
            $table->timestamp('verified_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_monitoring');
        Schema::dropIfExists('api_rate_limits');
        Schema::dropIfExists('scheduled_tasks');
        Schema::dropIfExists('export_logs');
        Schema::dropIfExists('import_logs');
    }
};
