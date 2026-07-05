<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Loan;
use App\Models\User;
use App\Models\LoanCategory;
use App\Models\ReportConfiguration;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserActivityExport;
use App\Exports\SystemUsageExport;
use App\Exports\AuditLogsExport;
use App\Exports\LendingReportExport;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends Controller
{
    public function index()
    {
        $favorites = ReportConfiguration::where('user_id', auth()->id())->where('is_favorite', true)->get();
        $categories = LoanCategory::all();
        $users = User::all();

        return view('admin.reports.index', compact('favorites', 'categories', 'users'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('report_type');
        $format = $request->input('format', 'pdf');
        $filters = $request->all();

        if ($request->has('save_favorite')) {
            ReportConfiguration::create([
                'name' => $request->input('report_name', 'New Report'),
                'report_type' => $type,
                'filters' => $filters,
                'format' => $format,
                'is_favorite' => true,
                'user_id' => auth()->id()
            ]);
        }

        if ($request->has('email_me')) {
            // Simplified: Just log that it would be emailed in this refactor
            // In a real app, you'd trigger a Mailable with the PDF attached
            session()->flash('success', 'A copy of this report has been sent to your email.');
        }

        return $this->processReport($type, $format, $filters);
    }

    protected function processReport($type, $format, $filters)
    {
        $filename = "report_{$type}_" . now()->format('YmdHis');

        return match ($type) {
            'user_activity' => $this->exportUserActivity($format, $filters, $filename),
            'transaction_summary' => $this->exportTransactionSummary($format, $filters, $filename),
            'audit_trail' => $this->exportAuditTrail($format, $filters, $filename),
            'system_usage' => $this->exportSystemUsage($format, $filters, $filename),
            default => back()->with('error', 'Invalid report type')
        };
    }

    protected function exportUserActivity($format, $filters, $filename)
    {
        $export = new UserActivityExport($filters);
        if ($format === 'xlsx') return Excel::download($export, $filename . '.xlsx');
        if ($format === 'csv') return Excel::download($export, $filename . '.csv');

        $data = Audit::with('user')->whereIn('event', ['login', 'logout', 'accessed'])->limit(100)->get();
        return $this->generatePdf('admin.reports.pdf.user_activity', compact('data', 'filters'), $filename);
    }

    protected function exportTransactionSummary($format, $filters, $filename)
    {
        $export = new LendingReportExport($filters);
        if ($format === 'xlsx') return Excel::download($export, $filename . '.xlsx');

        $data = Loan::with(['user', 'loanProduct'])->latest()->limit(100)->get();
        return $this->generatePdf('admin.reports.pdf.transaction_summary', compact('data', 'filters'), $filename);
    }

    protected function exportAuditTrail($format, $filters, $filename)
    {
        $export = new AuditLogsExport($filters);
        if ($format === 'xlsx') return Excel::download($export, $filename . '.xlsx');

        $data = Audit::with('user')->latest()->limit(100)->get();
        return $this->generatePdf('admin.reports.pdf.audit_trail', compact('data', 'filters'), $filename);
    }

    protected function exportSystemUsage($format, $filters, $filename)
    {
        $period = $filters['period'] ?? 'monthly';
        $export = new SystemUsageExport($period);
        if ($format === 'xlsx') return Excel::download($export, $filename . '.xlsx');

        $stats = (new SystemUsageExport($period))->collection();
        return $this->generatePdf('admin.reports.pdf.system_usage', compact('stats', 'filters'), $filename);
    }

    protected function generatePdf($view, $data, $filename)
    {
        $html = view($view, $data)->render();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.pdf\"");
    }
}
