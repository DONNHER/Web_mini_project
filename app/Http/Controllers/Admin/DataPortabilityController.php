<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\BooksImport;
use App\Exports\BooksExport;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Models\Category;
use App\Models\ImportExportLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DataPortabilityController extends Controller
{
    public function index()
    {
        $categories = Category::all();
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
        // Use now()->timestamp instead of time() for better testability
        $fileName = now()->timestamp . '_' . $file->getClientOriginalName();
        // Explicitly use 'local' disk to ensure consistency with Excel::queueImport and tests
        $path = $file->storeAs('imports', $fileName, 'local');

        $log = ImportExportLog::create([
            'type' => 'import',
            'file_name' => $fileName,
            'status' => 'processing',
            'user_id' => auth()->id(),
        ]);

        $updateExisting = $request->duplicate_action === 'update';

        try {
            Excel::queueImport(new BooksImport($updateExisting, $log->id), $path, 'local');
            return redirect()->back()->with('success', 'Book import has been queued. Check the logs below for progress.');
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
        // Use now()->timestamp instead of time() for better testability
        $fileName = 'users_' . now()->timestamp . '_' . $file->getClientOriginalName();
        // Explicitly use 'local' disk to ensure consistency with Excel::queueImport and tests
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
        $filters = $request->only(['category', 'min_price', 'max_price', 'stock_status', 'date_from', 'date_to']);
        $columns = $request->input('columns', []);
        $format = $request->input('format', 'xlsx');

        $fileName = 'books_export_' . now()->format('Y-m-d_His') . '.' . $format;

        ImportExportLog::create([
            'type' => 'export',
            'file_name' => $fileName,
            'status' => 'completed',
            'user_id' => auth()->id(),
        ]);

        $export = new BooksExport($filters, $columns);

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
        $headers = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description'];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['9780123456789', 'Sample Book Title', 'Author Name', '29.99', '100', 'Fiction', 'A great book description']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="books_import_template.csv"',
        ]);
    }

    public function userTemplate()
    {
        $headers = ['Name', 'Email', 'Role', 'Password'];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['Corporate User', 'user@company.com', 'customer', 'securePassword123']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_import_template.csv"',
        ]);
    }

    public function show(ImportExportLog $log)
    {
        $log->load('user');
        return view('admin.data-portability.show', compact('log'));
    }
}
