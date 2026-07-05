<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Notifications\SecurityAlert;
use Illuminate\Support\Facades\Notification;

class CheckSystemHealth extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Check storage capacity and other system health metrics';

    public function handle()
    {
        // 1. Check Storage Capacity (Requirement 6.2)
        $freeSpace = disk_free_space(base_path());
        $totalSpace = disk_total_space(base_path());
        $usedPercentage = ($totalSpace - $freeSpace) / $totalSpace * 100;

        if ($usedPercentage >= 85) {
            $message = "CRITICAL: Storage capacity is at " . round($usedPercentage, 2) . "%. Suggest immediate cleanup.";
            $this->warn($message);
            Log::warning($message);

            // Notify admins
            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            // Assuming we use an audit log as a notification trigger
            \App\Models\Audit::create([
                'event' => 'system_warning',
                'auditable_type' => 'System',
                'auditable_id' => 0,
                'new_values' => ['message' => $message],
                'ip_address' => '127.0.0.1',
            ]);
        }

        return 0;
    }
}
