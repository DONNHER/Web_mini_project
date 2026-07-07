<?php

namespace App\Exports;

use App\Models\LoanProduct;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LoanProductsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = LoanProduct::query();

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Interest Rate (%)',
            'Duration (Months)',
            'Min Amount',
            'Max Amount',
            'Status',
            'Created At',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->interest_rate,
            $product->duration_months,
            $product->min_amount,
            $product->max_amount,
            $product->is_active ? 'Active' : 'Inactive',
            $product->created_at,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
