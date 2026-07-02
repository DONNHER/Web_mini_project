<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\ImportExportLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;

class BooksImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure, ShouldQueue, WithEvents
{
    use SkipsFailures;

    protected $updateExisting;
    protected $logId;
    protected $rowsCount = 0;

    public function __construct($updateExisting = false, $logId = null)
    {
        $this->updateExisting = $updateExisting;
        $this->logId = $logId;
    }

    public function model(array $row)
    {
        $this->rowsCount++;

        $category = Category::where('name', $row['category'])->first();

        if (!$category) {
            return null;
        }

        $data = [
            'category_id'    => $category->id,
            'title'          => $row['title'],
            'author'         => $row['author'],
            'isbn'           => $row['isbn'],
            'price'          => $row['price'],
            'stock_quantity' => $row['stock'],
            'description'    => $row['description'] ?? null,
        ];

        if ($this->updateExisting) {
            return Book::updateOrCreate(
                ['isbn' => $row['isbn']],
                $data
            );
        }

        return new Book($data);
    }

    public function rules(): array
    {
        return [
            'isbn' => [
                'required',
                $this->updateExisting ? '' : 'unique:books,isbn',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(?:\d{9}[\dX]|\d{13})$/', str_replace('-', '', $value))) {
                        $fail('The '.$attribute.' is not a valid ISBN-10 or ISBN-13.');
                    }
                },
            ],
            'title'    => 'required|max:255',
            'author'   => 'required|max:255',
            'price'    => 'required|numeric|min:0|max:9999.99',
            'stock'    => 'required|integer|min:0',
            'category' => 'required|exists:categories,name',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                if ($this->logId) {
                    $log = ImportExportLog::find($this->logId);
                    if ($log) {
                        // Get the total number of rows from the reader
                        $totalRows = 0;
                        $sheetCounts = $event->getReader()->getTotalRows();
                        foreach ($sheetCounts as $count) {
                            $totalRows += ($count > 0 ? $count - 1 : 0); // Subtract header row
                        }

                        $log->update([
                            'status' => 'completed',
                            'total_rows' => $totalRows,
                            'processed_rows' => $totalRows - $this->failures()->count(),
                            'errors' => $this->failures()->map(fn($f) => "Row {$f->row()}: " . implode(', ', $f->errors()))->toArray()
                        ]);
                    }
                }
            },
            ImportFailed::class => function(ImportFailed $event) {
                if ($this->logId) {
                    ImportExportLog::where('id', $this->logId)->update([
                        'status' => 'failed',
                        'errors' => [$event->getException()->getMessage()]
                    ]);
                }
            },
        ];
    }
}
