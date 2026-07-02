<?php

namespace Database\Seeders;

use App\Models\ImportLog;
use App\Models\ExportLog;
use App\Models\ScheduledTask;
use App\Models\ApiRateLimit;
use App\Models\BackupMonitoring;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Audit;
use Illuminate\Support\Str;

class MonitoringSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            $customers = User::factory(5)->create(['role' => 'customer']);
        }

        // 1. Seed Import Logs
        ImportLog::create([
            'filename' => 'books_bulk_import_v1.csv',
            'rows_processed' => 500,
            'failures' => 5,
            'error_details' => ['Row 10: Invalid Price', 'Row 55: Duplicate ISBN'],
            'user_id' => $admin->id,
            'created_at' => now()->subDays(2),
        ]);

        // 2. Seed Export Logs
        ExportLog::create([
            'format' => 'xlsx',
            'filters' => ['category' => 'Fiction', 'status' => 'in_stock'],
            'status' => 'completed',
            'download_link' => 'exports/fiction_books.xlsx',
            'user_id' => $admin->id,
            'created_at' => now()->subDays(1),
        ]);

        // 3. Seed Scheduled Tasks
        ScheduledTask::create([
            'task_name' => 'Database Cleanup',
            'command' => 'app:cleanup-old-sessions',
            'started_at' => now()->subHours(12),
            'finished_at' => now()->subHours(12)->addMinutes(2),
            'status' => 'success',
            'output' => 'Cleaned up 1500 expired sessions.',
            'memory_usage' => 1048576 * 12,
        ]);

        ScheduledTask::create([
            'task_name' => 'Monthly Sales Report',
            'command' => 'app:generate-sales-report',
            'started_at' => now()->subDays(5),
            'finished_at' => now()->subDays(5)->addMinutes(10),
            'status' => 'failed',
            'output' => 'Error: SMTP connection timed out.',
            'memory_usage' => 1048576 * 45,
        ]);

        // 4. Seed API Rate Limits
        foreach ($customers->take(3) as $customer) {
            ApiRateLimit::create([
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_id' => $customer->id,
                'endpoint' => 'api/books',
                'hits' => 120,
                'throttled_at' => now()->subHours(rand(1, 24)),
                'retry_after' => 60,
            ]);
        }

        // 5. Seed Backup Monitoring
        BackupMonitoring::create([
            'backup_name' => 'daily-db-backup-2026-05-04',
            'status' => 'success',
            'file_size' => 1024 * 1024 * 250,
            'destination' => 'local',
            'verified_at' => now()->subDays(1),
        ]);

        // 6. Seed Mock Audits (Direct insertion since it's a monitoring seeder)
        Audit::create([
            'id' => (string) Str::uuid(),
            'user_type' => 'App\Models\User',
            'user_id' => $admin->id,
            'event' => 'updated',
            'auditable_type' => 'App\Models\Book',
            'auditable_id' => 1,
            'old_values' => ['price' => 19.99],
            'new_values' => ['price' => 24.99],
            'url' => 'http://localhost/admin/books/1',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'created_at' => now()->subMinutes(30),
        ]);
    }
}
