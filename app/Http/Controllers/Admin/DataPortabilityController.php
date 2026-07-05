<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\LoanProductsImport;
use App\Exports\LoanProductsExport;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Models\LoanCategory;
use App\Models\ImportExportLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DataPortabilityController extends Controller
{
    public function index()
    {
        $categories = LoanCategory::all();
        $logs = ImportExportLog::with('user')->latest()->take(10)->get();
        return view('admin.data-portability.index', compact('categories', 'logs'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'duplicate_action' => 'required|in:skip,update',
        ]);

        $file = $request->file('file');
        $fileName = now()->timestamp . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('imports', $fileName, 'local');

        $log = ImportExportLog::create([
            'type' => 'import',
            'file_name' => $fileName,
            'status' => 'processing',
            'user_id' => auth()->id(),
        ]);

        $updateExisting = $request->duplicate_action === 'update';

        try {
            Excel::queueImport(new LoanProductsImport($updateExisting, $log->id), $path, 'local');
            return redirect()->back()->with('success', 'Loan product import has been queued. Check the logs below for progress.');
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'errors' => [$e->getMessage()]]);
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = 'users_' . now()->timestamp . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('imports/users', $fileName, 'local');

        $log = ImportExportLog::create([
            'type' => 'user_import',
            'file_name' => $fileName,
            'status' => 'processing',
            'user_id' => auth()->id(),
        ]);

        try {
            Excel::queueImport(new UsersImport($log->id), $path, 'local');
            return redirect()->back()->with('success', 'User import has been queued.');
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'errors' => [$e->getMessage()]]);
            return redirect()->back()->with('error', 'User import failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $filters = $request->only(['category', 'date_from', 'date_to']);
        $format = $request->input('format', 'xlsx');

        $fileName = 'loan_products_export_' . now()->format('Y-m-d_His') . '.' . $format;

        ImportExportLog::create([
            'type' => 'export',
            'file_name' => $fileName,
            'status' => 'completed',
            'user_id' => auth()->id(),
        ]);

        if ($format === 'json') {
            $data = \App\Models\LoanProduct::with('category')->get();
            return response()->json($data)->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }

        $export = new LoanProductsExport($filters);

        return Excel::download($export, $fileName);
    }

    public function exportUsers(Request $request)
    {
        $redactPii = $request->has('redact_pii');
        $format = $request->input('format', 'xlsx');

        $fileName = 'users_export_' . now()->format('Y-m-d_His') . '.' . $format;

        ImportExportLog::create([
            'type' => 'user_export',
            'file_name' => $fileName,
            'status' => 'completed',
            'user_id' => auth()->id(),
        ]);

        $export = new UsersExport($request->only(['role', 'date_from', 'date_to']), $redactPii);

        return Excel::download($export, $fileName);
    }

    public function template()
    {
        $headers = ['name', 'category', 'interest_rate', 'duration', 'min_amount', 'max_amount', 'description'];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['Salary Loan', 'Personal Loans', '3.5', '6', '5000', '50000', 'Fast cash based on salary']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="loan_products_template.csv"',
        ]);
    }

    public function userTemplate()
    {
        $headers = ['Name', 'Email', 'Role', 'Password'];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['Corporate User', 'user@company.com', 'borrower', 'securePassword123']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_template.csv"',
        ]);
    }

    public function show(ImportExportLog $log)
    {
        $log->load('user');
        return view('admin.data-portability.show', compact('log'));
    }
}
