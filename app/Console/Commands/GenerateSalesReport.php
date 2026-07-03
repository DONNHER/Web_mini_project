<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerateSalesReport extends Command
{
    protected $signature = 'app:generate-sales-report {--month= : Specific month (Y-m)}';
    protected $description = 'Generate monthly sales report';

    public function handle()
    {
        $month = $this->option('month')
            ? Carbon::createFromFormat('Y-m', $this->option('month'))
            : now()->subMonth();

        $this->info("Generating sales report for {$month->format('F Y')}...");

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $report = [
            'period' => $month->format('Y-m'),
            'total_orders' => Order::on('mysql')->whereBetween('created_at', [$start, $end])->count(),
            'total_revenue' => Order::on('mysql')->whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'average_order_value' => Order::on('mysql')->whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'cancelled')
                ->avg('total_amount') ?? 0,
            'orders_by_status' => Order::on('mysql')->whereBetween('created_at', [$start, $end])
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
            'top_books' => DB::connection('mysql')->table('order_items')
                ->join('books', 'order_items.book_id', '=', 'books.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$start, $end])
                ->where('orders.status', '!=', 'cancelled')
                ->select('books.title', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('books.id', 'books.title')
                ->orderBy('total_sold', 'desc')
                ->limit(10)
                ->get(),
            'generated_at' => now()->toDateTimeString(),
        ];

        // Save report as JSON
        $filename = "reports/sales_{$month->format('Y_m')}.json";
        Storage::disk('local')->put($filename, json_encode($report, JSON_PRETTY_PRINT));
        // Storage::disk('local')->makeDirectory('reports');
        // Storage::disk('local')->put($filename, json_encode($report, JSON_PRETTY_PRINT));

        // Optionally send email with report summary
        // Mail::to(env('ADMIN_EMAIL'))->send(new MonthlySalesReport($report));

        $this->info("Report saved to storage/app/{$filename}");
        $this->table(['Metric', 'Value'], [
            ['Total Orders', $report['total_orders']],
            ['Total Revenue', '₱' . number_format($report['total_revenue'], 2)],
            ['Average Order Value', '₱' . number_format($report['average_order_value'], 2)],
        ]);

        return Command::SUCCESS;
    }
}
