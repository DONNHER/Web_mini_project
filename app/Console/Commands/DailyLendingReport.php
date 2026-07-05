<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\User;
use App\Mail\DailyLendingReportMail;
use App\Exports\LoansExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DailyLendingReport extends Command
{
    protected $signature = 'report:generate-daily';
    protected $description = 'Generate daily lending report and email to administrators';

    public function handle()
    {
        $yesterday = Carbon::yesterday();
        $dateStr = $yesterday->format('Y-m-d');

        $this->info("Generating Daily Lending Report for $dateStr...");

        // Data for the email body
        $reportData = [
            'date' => $dateStr,
            'total_loans' => Loan::on('mysql')->whereDate('created_at', $yesterday)->count(),
            'total_deposits' => Loan::on('mysql')->whereDate('created_at', $yesterday)
                ->sum('total_amount'),
            'returned_loans' => Loan::on('mysql')->whereDate('created_at', $yesterday)
                ->where('status', 'returned')
                ->count(),
            'borrowed_loans' => Loan::on('mysql')->whereDate('created_at', $yesterday)
                ->where('status', 'borrowed')
                ->count(),
        ];

        // Generate the Excel file to attach
        $fileName = "daily_reports/loans_$dateStr.xlsx";

        // Ensure directory exists
        if (!Storage::disk('local')->exists('daily_reports')) {
            Storage::disk('local')->makeDirectory('daily_reports');
        }

        Excel::store(new LoansExport(['date_from' => $dateStr, 'date_to' => $dateStr]), $fileName, 'local');

        // Send to admins
        $admins = User::on('mysql')->whereHas('role', fn($q) => $q->where('name', 'admin'))->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new DailyLendingReportMail($reportData, Storage::disk('local')->path($fileName)));
        }

        $this->info("Daily report sent to " . $admins->count() . " administrators.");

        return 0;
    }
}
