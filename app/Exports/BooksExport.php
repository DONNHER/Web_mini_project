<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class BooksExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithChunkReading
{
    use Exportable;

    protected $filters;
    protected $columns;

    public function chunkSize(): int
    {
        return 2000;
    }

    public function query()
    {
        return Book::query()
            ->select(['isbn', 'title', 'author', 'price', 'stock_quantity', 'published_at'])
            ->where('is_active', true)
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'Title',
            'Author',
            'Price',
            'Stock',
            'Published At',
        ];
    }

    public function map($book): array
    {
        return [
            $book->isbn,
            $book->title,
            $book->author,
            $book->price,
            $book->stock_quantity,
            $book->published_at,
        ];
    }
}
