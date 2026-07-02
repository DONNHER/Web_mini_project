<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\AuditLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by model type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', 'App\\Models\\' . $request->auditable_type);
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Handle Export
        if ($request->has('export')) {
            $format = $request->get('export');
            $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s');

            if ($format === 'csv') {
                return Excel::download(new AuditLogsExport($request->all()), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
            } elseif ($format === 'pdf') {
                return Excel::download(new AuditLogsExport($request->all()), $filename . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            } else {
                return Excel::download(new AuditLogsExport($request->all()), $filename . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            }
        }

        $audits = $query->paginate(25);

        $modelTypes = [
            'Book' => 'Books',
            'User' => 'Users',
            'Category' => 'Categories',
            'Order' => 'Orders',
            'Review' => 'Reviews',
            'ImportExportLog' => 'Import/Export Logs'
        ];

        $events = ['created', 'updated', 'deleted', 'restored', 'login', 'logout', 'login_failed', 'password_reset', 'email_verified'];

        $users = User::orderBy('name')->pluck('name', 'id');

        return view('admin.audit-logs.index', compact('audits', 'modelTypes', 'events', 'users'));
    }

    public function show(Audit $audit)
    {
        $audit->load('user');
        return view('admin.audit-logs.show', compact('audit'));
    }
}
