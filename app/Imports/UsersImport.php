<?php

namespace App\Imports;

use App\Models\User;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure, ShouldQueue, WithEvents
{
    use SkipsFailures;

    protected $logId;
    protected $rowsCount = 0;

    public function __construct($logId = null)
    {
        $this->logId = $logId;
    }

    public function model(array $row)
    {
        $this->rowsCount++;

        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password'] ?? Str::random(12)),
            'role'     => $row['role'] ?? 'customer',
        ]);
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role'  => 'nullable|string|in:admin,customer',
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
