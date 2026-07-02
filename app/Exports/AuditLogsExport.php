<?php

namespace App\Exports;

use App\Models\Audit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuditLogsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Audit::with('user')->orderBy('created_at', 'desc');

        if (!empty($this->filters['auditable_type'])) {
            $query->where('auditable_type', 'App\\Models\\' . $this->filters['auditable_type']);
        }

        if (!empty($this->filters['event'])) {
            $query->where('event', $this->filters['event']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
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
            'User',
            'Event',
            'Auditable Type',
            'Auditable ID',
            'Old Values',
            'New Values',
            'URL',
            'IP Address',
            'User Agent',
            'Created At',
        ];
    }

    public function map($audit): array
    {
        return [
            $audit->id,
            $audit->user ? $audit->user->name : 'System',
            $audit->event,
            $audit->auditable_type,
            $audit->auditable_id,
            json_encode($audit->old_values),
            json_encode($audit->new_values),
            $audit->url,
            $audit->ip_address,
            $audit->user_agent,
            $audit->created_at,
        ];
    }
}
