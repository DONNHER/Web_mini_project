<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\BooksImport;
use App\Exports\BooksExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataPortabilityController extends Controller
{
    public function index()
    {
        return view('admin.data-portability.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $import = new BooksImport(auth()->id());
            Excel::import($import, $request->file('file'));

            return redirect()->back()->with('success', 'Import has been queued. You will receive a notification when completed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $filters = $request->only(['category_id', 'search']);
        $filename = 'books_export_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new BooksExport($filters), $filename);
    }

    public function template()
    {
        return response()->download(storage_path('app/templates/books_import_template.xlsx'));
    }
}