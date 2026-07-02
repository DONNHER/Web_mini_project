<?php

namespace Tests\Feature;

use App\Models\ApiRateLimit;
use App\Models\ExportLog;
use App\Models\ImportLog;
use App\Models\User;
use Database\Seeders\MonitoringSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function migration_tables_exist_with_correct_columns()
    {
        $tables = [
            'import_logs' => ['filename', 'rows_processed', 'failures', 'user_id'],
            'export_logs' => ['format', 'filters', 'status', 'user_id'],
            'scheduled_tasks' => ['task_name', 'command', 'status'],
            'api_rate_limits' => ['ip_address', 'endpoint', 'hits'],
            'backup_monitoring' => ['backup_name', 'status', 'file_size']
        ];

        foreach ($tables as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} missing");
            foreach ($columns as $column) {
                $this->assertTrue(Schema::hasColumn($table, $column), "Column {$column} missing in {$table}");
            }
        }
    }

    /** @test */
    public function monitoring_seeder_populates_all_tables()
    {
        // Run the seeder
        $this->seed(MonitoringSeeder::class);

        // Verify data exists in all enhancement tables
        $this->assertGreaterThan(0, ImportLog::count(), 'Import logs not seeded');
        $this->assertGreaterThan(0, ExportLog::count(), 'Export logs not seeded');
        $this->assertGreaterThan(0, ApiRateLimit::count(), 'API rate limits not seeded');
        $this->assertDatabaseHas('scheduled_tasks', ['task_name' => 'Database Cleanup']);
        $this->assertDatabaseHas('backup_monitoring', ['status' => 'success']);

        // Verify mock audits were seeded
        $this->assertDatabaseHas('audits', ['event' => 'updated', 'auditable_type' => 'App\Models\Book']);
    }

    /** @test */
    public function foreign_key_constraints_cascade_on_user_deletion()
    {
        $user = User::factory()->create();

        // Create logs for this specific user
        ImportLog::create([
            'filename' => 'test.csv',
            'user_id' => $user->id,
            'rows_processed' => 10,
            'failures' => 0
        ]);

        ExportLog::create([
            'format' => 'json',
            'status' => 'completed',
            'user_id' => $user->id
        ]);

        ApiRateLimit::create([
            'ip_address' => '127.0.0.1',
            'user_id' => $user->id,
            'endpoint' => 'api/test',
            'hits' => 1,
            'throttled_at' => now()
        ]);

        // Delete the user
        $user->delete();

        // Verify cascading delete (Requirement 10.2)
        $this->assertDatabaseMissing('import_logs', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('export_logs', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('api_rate_limits', ['user_id' => $user->id]);
    }
}
