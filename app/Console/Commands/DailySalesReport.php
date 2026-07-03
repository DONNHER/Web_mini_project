<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Mail\DailySalesReportMail;
use App\Exports\OrdersExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DailySalesReport extends Command
{
    protected $signature = 'report:generate-daily';
    protected $description = 'Generate daily sales report and email to administrators';

    public function handle()
    {
        $yesterday = Carbon::yesterday();
        $dateStr = $yesterday->format('Y-m-d');

        $this->info("Generating Daily Sales Report for $dateStr...");

        // Data for the email body
        $reportData = [
            'date' => $dateStr,
            'total_orders' => Order::on('mysql')->whereDate('created_at', $yesterday)->count(),
            'total_revenue' => Order::on('mysql')->whereDate('created_at', $yesterday)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'completed_orders' => Order::on('mysql')->whereDate('created_at', $yesterday)
                ->where('status', 'completed')
                ->count(),
            'pending_orders' => Order::on('mysql')->whereDate('created_at', $yesterday)
                ->where('status', 'pending')
                ->count(),
        ];

        // Generate the Excel file to attach
        $fileName = "daily_reports/orders_$dateStr.xlsx";

        // Ensure directory exists
        if (!Storage::disk('local')->exists('daily_reports')) {
            Storage::disk('local')->makeDirectory('daily_reports');
        }

        Excel::store(new OrdersExport(['date_from' => $dateStr, 'date_to' => $dateStr]), $fileName, 'local');

        // Send to admins
        $admins = User::on('mysql')->where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new DailySalesReportMail($reportData, Storage::disk('local')->path($fileName)));
        }

        $this->info("Daily report sent to " . $admins->count() . " administrators.");

        return 0;
    }
}
