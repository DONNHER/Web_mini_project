<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Contracts\Queue\ShouldQueue;

class BooksExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    protected $filters;
    protected $columns;

    public function __construct($filters = [], $columns = [])
    {
        $this->filters = $filters;
        $this->columns = $columns;
    }

    public function query()
    {
        $query = Book::query()->with('category');

        if (!empty($this->filters['category'])) {
            $query->where('category_id', $this->filters['category']);
        }

        if (isset($this->filters['min_price'])) {
            $query->where('price', '>=', $this->filters['min_price']);
        }

        if (isset($this->filters['max_price'])) {
            $query->where('price', '<=', $this->filters['max_price']);
        }

        if (isset($this->filters['stock_status'])) {
            if ($this->filters['stock_status'] === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($this->filters['stock_status'] === 'out_of_stock') {
                $query->where('stock_quantity', '=', 0);
            }
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        $allHeadings = [
            'id' => 'ID',
            'isbn' => 'ISBN',
            'title' => 'Title',
            'author' => 'Author',
            'price' => 'Price',
            'stock_quantity' => 'Stock',
            'category' => 'Category',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];

        if (empty($this->columns)) {
            return array_values($allHeadings);
        }

        return array_intersect_key($allHeadings, array_flip($this->columns));
    }

    public function map($book): array
    {
        $mapping = [
            'id' => $book->id,
            'isbn' => $book->isbn,
            'title' => $book->title,
            'author' => $book->author,
            'price' => $book->price,
            'stock_quantity' => $book->stock_quantity,
            'category' => $book->category->name,
            'description' => $book->description,
            'created_at' => $book->created_at->toDateTimeString(),
        ];

        if (empty($this->columns)) {
            return array_values($mapping);
        }

        return array_intersect_key($mapping, array_flip($this->columns));
    }
}
