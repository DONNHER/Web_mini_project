<?php

namespace App\Http\Controllers;

use App\Exports\BooksExport;
use App\Imports\BooksImport;
use App\Models\Category;
use App\Models\ImportExportLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BookImportExportController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $logs = ImportExportLog::with('user')->latest()->take(10)->get();
        return view('admin.books.import-export', compact('categories', 'logs'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt',
            'duplicate_action' => 'required|in:skip,update',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('imports', $fileName);

        $log = ImportExportLog::create([
            'type' => 'import',
            'file_name' => $fileName,
            'status' => 'processing',
            'user_id' => auth()->id(),
        ]);

        $updateExisting = $request->duplicate_action === 'update';

        Excel::queueImport(new BooksImport($updateExisting, $log->id), $path);

        return back()->with('success', 'Import has been queued. You can check the progress in the logs below.');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['category', 'min_price', 'max_price', 'stock_status', 'date_from', 'date_to']);
        $columns = $request->input('columns', []);
        $format = $request->input('format', 'xlsx');

        $fileName = 'books_export_' . now()->format('Y-m-d_H-i-s') . '.' . $format;

        ImportExportLog::create([
            'type' => 'export',
            'file_name' => $fileName,
            'status' => 'completed',
            'user_id' => auth()->id(),
        ]);

        return Excel::download(new BooksExport($filters, $columns), $fileName);
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $headers = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description'];
        $fileName = 'books_import_template.csv';

        if (!Storage::exists('templates')) {
            Storage::makeDirectory('templates');
        }

        $path = storage_path('app/public/templates/' . $fileName);
        $file = fopen($path, 'w');
        fputcsv($file, $headers);
        fclose($file);

        return response()->download($path);
    }
}
