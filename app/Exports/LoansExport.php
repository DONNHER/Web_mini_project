<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LoansExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    use Exportable;

    protected $filters;

    public function chunkSize(): int
    {
        return 2000;
    }

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Loan::on('mysql')->with(['user', 'loanItems.book']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['borrower_id'])) {
            $query->where('user_id', $this->filters['borrower_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Loan ID',
            'Borrower Name',
            'Borrower Email',
            'Late Fees',
            'Status',
            'Due Date',
            'Returned At',
            'Created At',
            'Items Count'
        ];
    }

    public function map($loan): array
    {
        return [
            $loan->id,
            $loan->user->name,
            $loan->user->email,
            number_format($loan->total_amount, 2),
            ucfirst($loan->status),
            $loan->due_date ? $loan->due_date->toDateString() : 'N/A',
            $loan->returned_at ? $loan->returned_at->toDateTimeString() : 'Pending',
            $loan->created_at->toDateTimeString(),
            $loan->loanItems->count()
        ];
    }
}
