<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class FinancialReportExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Order::query()
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as total_orders"),
                DB::raw("SUM(total_amount) as gross_revenue"),
                DB::raw("SUM(total_amount * 0.12) as tax_amount"), // Assuming 12% tax
                DB::raw("SUM(total_amount * 0.88) as net_revenue")
            )
            ->where('status', 'completed');

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->groupBy('date')->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Total Orders',
            'Gross Revenue (₱)',
            'Tax (12%) (₱)',
            'Net Revenue (₱)'
        ];
    }
}
