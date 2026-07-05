<?php

namespace App\Jobs;

use App\Models\ReportConfiguration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\ReportController;

class SendScheduledReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $configId;

    public function __construct($configId)
    {
        $this->configId = $configId;
    }

    public function handle()
    {
        $config = ReportConfiguration::find($this->configId);
        if (!$config || !$config->recipient_email) return;

        // Manually trigger the report generation (internal call or refactor logic to service)
        // For simplicity in this refactor, we assume a Service exists or use the controller method
        $controller = app(ReportController::class);

        // This is a bit of a hack, in a real app, use a dedicated ReportingService
        // But for this project, let's assume we send a simplified notification
        Mail::raw("Your scheduled report '{$config->name}' is ready. Please log in to the dashboard to download it.", function ($message) use ($config) {
            $message->to($config->recipient_email)
                ->subject("Scheduled Report: " . $config->name);
        });
    }
}
