<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use App\Repositories\LoanProductRepository;
use Illuminate\Http\Request;

class LoanProductController extends Controller
{
    protected $loanProductRepository;

    public function __construct(LoanProductRepository $loanProductRepository)
    {
        $this->loanProductRepository = $loanProductRepository;
    }

    public function index(Request $request)
    {
        $query = $request->has('trashed') ? LoanProduct::onlyTrashed() : LoanProduct::query();

        if ($request->has('search') && $request->search != '') {
            $loanProducts = $query->where('name', 'like', "%{$request->search}%")->paginate(10);
        } else {
            $loanProducts = $query->paginate(10);
        }

        return view('loan_products.index', compact('loanProducts'));
    }

    public function restore($id)
    {
        $product = LoanProduct::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('loan_products.index')
            ->with('success', 'Loan product restored successfully!');
    }

    public function create()
    {
        return view('loan_products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interest_rate' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0|gte:min_amount',
        ]);

        LoanProduct::create($validated);

        return redirect()->route('loan_products.index')
            ->with('success', 'Loan product created successfully!');
    }

    public function show(LoanProduct $loanProduct)
    {
        return view('loan_products.show', compact('loanProduct'));
    }

    public function edit(LoanProduct $loanProduct)
    {
        return view('loan_products.edit', compact('loanProduct'));
    }

    public function update(Request $request, LoanProduct $loanProduct)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interest_rate' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0|gte:min_amount',
        ]);

        try {
            $loanProduct->update($validated);
            return redirect()->route('loan_products.show', $loanProduct)
                             ->with('success', 'Loan product updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, LoanProduct $loanProduct)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        $loanProduct->delete();
        return redirect()->route('loan_products.index')
                         ->with('success', 'Loan product deleted successfully!');
    }
}
