<?php

namespace Tests\Feature;

use App\Models\ApiRateLimit;
use App\Models\BackupMonitoring;
use App\Models\ExportLog;
use App\Models\ImportLog;
use App\Models\ScheduledTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseEnhancementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_import_logs()
    {
        $user = User::factory()->create();

        $log = ImportLog::create([
            'filename' => 'books_import.csv',
            'rows_processed' => 100,
            'failures' => 2,
            'error_details' => ['Row 5: Invalid ISBN', 'Row 12: Missing Title'],
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('import_logs', [
            'filename' => 'books_import.csv',
            'rows_processed' => 100,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(['Row 5: Invalid ISBN', 'Row 12: Missing Title'], $log->error_details);
    }

    /** @test */
    public function it_can_store_export_logs()
    {
        $user = User::factory()->create();

        ExportLog::create([
            'format' => 'pdf',
            'filters' => ['date_from' => '2023-01-01', 'status' => 'completed'],
            'status' => 'completed',
            'download_link' => 'exports/orders_2023.pdf',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('export_logs', [
            'format' => 'pdf',
            'status' => 'completed',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_can_track_scheduled_tasks()
    {
        $task = ScheduledTask::create([
            'task_name' => 'Daily Sales Report',
            'command' => 'app:generate-sales-report',
            'started_at' => now()->subMinutes(5),
            'finished_at' => now(),
            'status' => 'success',
            'output' => 'Report generated for 150 orders.',
            'memory_usage' => 1024 * 1024 * 5, // 5MB
        ]);

        $this->assertDatabaseHas('scheduled_tasks', [
            'task_name' => 'Daily Sales Report',
            'status' => 'success',
        ]);
    }

    /** @test */
    public function it_can_log_api_rate_limiting_events()
    {
        $user = User::factory()->create();

        ApiRateLimit::create([
            'ip_address' => '127.0.0.1',
            'user_id' => $user->id,
            'endpoint' => 'api/books',
            'hits' => 60,
            'throttled_at' => now(),
            'retry_after' => 60,
        ]);

        $this->assertDatabaseHas('api_rate_limits', [
            'ip_address' => '127.0.0.1',
            'endpoint' => 'api/books',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_can_monitor_backups()
    {
        BackupMonitoring::create([
            'backup_name' => 'weekly-full-backup',
            'status' => 'success',
            'file_size' => 500 * 1024 * 1024, // 500MB
            'destination' => 's3',
            'verified_at' => now(),
        ]);

        $this->assertDatabaseHas('backup_monitoring', [
            'backup_name' => 'weekly-full-backup',
            'status' => 'success',
            'destination' => 's3',
        ]);
    }

    /** @test */
    public function it_verifies_audit_logging_is_active()
    {
        // Audit logging is usually triggered by model events.
        // Let's create a user and check if an audit record is created.
        $user = User::factory()->create(['name' => 'Original Name']);

        // Since we are using RefreshDatabase and a library, we check the audits table.
        // Note: Some configurations might require 'auditable' trait on User model.
        $user->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'updated',
        ]);
    }
}
