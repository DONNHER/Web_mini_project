<?php

namespace App\Http\Controllers;

use App\Models\LoanCategory;
use Illuminate\Http\Request;

class LoanCategoryController extends Controller
{
    public function index()
    {
        $categories = LoanCategory::withCount('loanProducts')->paginate(10);
        return view('loan_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('loan_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loan_categories',
            'description' => 'nullable|string',
        ]);

        LoanCategory::create($validated);

        return redirect()->route('admin.loan-categories.index')
                         ->with('success', 'Loan Category created successfully!');
    }

    public function show(LoanCategory $category)
    {
        $loanProducts = $category->loanProducts()->paginate(12);
        return view('loan_categories.show', compact('category', 'loanProducts'));
    }

    public function edit(LoanCategory $category)
    {
        return view('loan_categories.edit', compact('category'));
    }

    public function update(Request $request, LoanCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loan_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.loan-categories.index')
                         ->with('success', 'Loan Category updated successfully!');
    }

    public function destroy(LoanCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.loan-categories.index')
                         ->with('success', 'Loan Category deleted successfully!');
    }
}
