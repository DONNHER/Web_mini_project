<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerateLendingReport extends Command
{
    protected $signature = 'app:generate-lending-report {--month= : Specific month (Y-m)}';
    protected $description = 'Generate monthly lending report';

    public function handle()
    {
        $month = $this->option('month')
            ? Carbon::createFromFormat('Y-m', $this->option('month'))
            : now()->subMonth();

        $this->info("Generating lending report for {$month->format('F Y')}...");

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $report = [
            'period' => $month->format('Y-m'),
            'total_loans' => Loan::on('mysql')->whereBetween('created_at', [$start, $end])->count(),
            'total_late_fees' => Loan::on('mysql')->whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'average_books_per_loan' => DB::connection('mysql')->table('loan_items')
                ->join('loans', 'loan_items.loan_id', '=', 'loans.id')
                ->whereBetween('loans.created_at', [$start, $end])
                ->avg('quantity') ?? 0,
            'loans_by_status' => Loan::on('mysql')->whereBetween('created_at', [$start, $end])
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
            'top_books' => DB::connection('mysql')->table('loan_items')
                ->join('books', 'loan_items.book_id', '=', 'books.id')
                ->join('loans', 'loan_items.loan_id', '=', 'loans.id')
                ->whereBetween('loans.created_at', [$start, $end])
                ->where('loans.status', '!=', 'cancelled')
                ->select('books.title', DB::raw('SUM(loan_items.quantity) as total_borrowed'))
                ->groupBy('books.id', 'books.title')
                ->orderBy('total_borrowed', 'desc')
                ->limit(10)
                ->get(),
            'generated_at' => now()->toDateTimeString(),
        ];

        // Save report as JSON
        $filename = "reports/lending_{$month->format('Y_m')}.json";
        Storage::disk('local')->put($filename, json_encode($report, JSON_PRETTY_PRINT));

        $this->info("Report saved to storage/app/{$filename}");
        $this->table(['Metric', 'Value'], [
            ['Total Loans', $report['total_loans']],
            ['Total Late Fees (Estimated)', '₱' . number_format($report['total_late_fees'], 2)],
            ['Average Books Per Loan', number_format($report['average_books_per_loan'], 2)],
        ]);

        return Command::SUCCESS;
    }
}
