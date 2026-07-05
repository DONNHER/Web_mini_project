<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SystemUsageExport implements FromCollection, WithHeadings
{
    protected $period;

    public function __construct($period = 'monthly')
    {
        $this->period = $period;
    }

    public function collection()
    {
        $format = match($this->period) {
            'yearly' => '%Y',
            'quarterly' => '%Y-Q',
            default => '%Y-%m'
        };

        return Loan::select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                DB::raw("COUNT(*) as total_loans"),
                DB::raw("SUM(principal_amount) as principal"),
                DB::raw("SUM(total_amount) as total_value")
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Period', 'Total Applications', 'Principal Amount', 'Total Value (with Interest)'];
    }
}
