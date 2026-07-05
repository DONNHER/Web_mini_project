<?php

namespace App\Exports;

use App\Models\Audit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserActivityExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Audit::with('user')->whereIn('event', ['login', 'logout', 'accessed']);

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Date/Time', 'User', 'Action', 'Module', 'IP Address'];
    }

    public function map($audit): array
    {
        return [
            $audit->created_at->format('Y-m-d H:i:s'),
            $audit->user->name ?? 'System',
            ucfirst($audit->event),
            $audit->auditable_type === 'Page' ? 'Navigation' : class_basename($audit->auditable_type),
            $audit->ip_address,
        ];
    }
}
