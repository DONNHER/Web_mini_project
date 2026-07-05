<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class LendingReportExport implements FromQuery, WithHeadings, WithChunkReading
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    public function query()
    {
        $query = Loan::query()
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as total_loans"),
                DB::raw("SUM(total_amount) as total_deposits"),
                DB::raw("COUNT(CASE WHEN status = 'overdue' THEN 1 END) as overdue_count"),
                DB::raw("COUNT(CASE WHEN status = 'returned' THEN 1 END) as returned_count")
            );

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->groupBy('date')->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Total Loans',
            'Total Deposits (₱)',
            'Overdue Count',
            'Returned Count'
        ];
    }
}
