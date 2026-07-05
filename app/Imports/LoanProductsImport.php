<?php

namespace App\Imports;

use App\Models\LoanProduct;
use App\Models\LoanCategory;
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

class LoanProductsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure, ShouldQueue, WithEvents
{
    use SkipsFailures;

    protected $updateExisting;
    protected $logId;

    public function __construct($updateExisting = false, $logId = null)
    {
        $this->updateExisting = $updateExisting;
        $this->logId = $logId;
    }

    public function model(array $row)
    {
        $category = LoanCategory::where('name', $row['category'])->first();

        if (!$category) {
            return null;
        }

        $data = [
            'category_id'     => $category->id,
            'name'            => $row['name'],
            'description'     => $row['description'] ?? null,
            'interest_rate'   => $row['interest_rate'],
            'duration_months' => $row['duration'],
            'min_amount'      => $row['min_amount'],
            'max_amount'      => $row['max_amount'],
            'is_active'       => true,
        ];

        if ($this->updateExisting) {
            return LoanProduct::updateOrCreate(
                ['name' => $row['name']],
                $data
            );
        }

        return new LoanProduct($data);
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|max:255',
            'interest_rate'   => 'required|numeric|min:0',
            'duration'        => 'required|integer|min:1',
            'min_amount'      => 'required|numeric|min:0',
            'max_amount'      => 'required|numeric|gte:min_amount',
            'category'        => 'required|exists:loan_categories,name',
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
                        $totalRows = 0;
                        $sheetCounts = $event->getReader()->getTotalRows();
                        foreach ($sheetCounts as $count) {
                            $totalRows += ($count > 0 ? $count - 1 : 0);
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
