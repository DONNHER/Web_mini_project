<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsersExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithChunkReading
{
    use Exportable;

    protected $redactPii;
    protected $filters;

    public function chunkSize(): int
    {
        return 2000;
    }

    public function __construct($filters = [], $redactPii = false)
    {
        $this->filters = $filters;
        $this->redactPii = $redactPii;
    }

    public function query()
    {
        $query = User::query();

        if (!empty($this->filters['role'])) {
            $query->where('role', $this->filters['role']);
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
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Created At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $this->redactPii ? 'REDACTED' : $user->name,
            $this->redactPii ? $this->maskEmail($user->email) : $user->email,
            $user->role,
            $user->created_at->toDateTimeString(),
        ];
    }

    private function maskEmail($email)
    {
        $parts = explode("@", $email);
        $name = $parts[0];
        $len = strlen($name);
        if ($len > 2) {
            $maskedName = substr($name, 0, 1) . str_repeat('*', $len - 2) . substr($name, -1);
        } else {
            $maskedName = str_repeat('*', $len);
        }
        return $maskedName . "@" . ($parts[1] ?? 'example.com');
    }
}
