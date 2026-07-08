<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\User;
use App\Services\LendingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessAITask;

class LoanController extends Controller
{
    protected $lendingService;

    public function __construct(LendingService $lendingService)
    {
        $this->lendingService = $lendingService;
    }

    /**
     * Display loan application form
     */
    public function apply(LoanProduct $loanProduct)
    {
        $comakers = User::where('is_comaker', true)
                        ->where('id', '!=', auth()->id())
                        ->get();

        return view('loans.apply', compact('loanProduct', 'comakers'));
    }

    /**
     * Store loan application
     */
    public function store(Request $request, \App\Services\AI\CategorizationService $categorizationService)
    {
        $request->validate([
            'loan_product_id' => 'required|exists:loan_products,id',
            'principal_amount' => 'required|numeric|min:0',
            'comaker_id' => 'nullable|exists:users,id',
            'payment_method' => 'required|string',
            'purpose' => 'required|string|max:1000',
        ]);

        $product = LoanProduct::findOrFail($request->loan_product_id);

        try {
            $this->lendingService->validateApplication($product, $request->principal_amount);

            // Automated Categorization (AI Integration Bonus)
            $aiTag = $categorizationService->tagLoanPurpose($request->purpose);

            DB::beginTransaction();

            $totalAmount = $this->lendingService->calculateTotal(
                $request->principal_amount,
                $product->interest_rate,
                $product->duration_months
            );

            $loan = Loan::create([
                'user_id' => auth()->id(),
                'loan_product_id' => $product->id,
                'comaker_id' => $request->comaker_id,
                'principal_amount' => $request->principal_amount,
                'interest_rate' => $product->interest_rate,
                'term_months' => $product->duration_months,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'purpose' => $request->purpose,
                'ai_tag' => $aiTag,
                'due_date' => now()->addMonths($product->duration_months),
            ]);

            DB::commit();

            // Dispatch AI Security Scan/Risk Assessment
            ProcessAITask::dispatch($loan->id, request()->ip())->onQueue('ai-tasks');

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Loan application submitted successfully and is pending approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit loan application: ' . $e->getMessage());
        }
    }

    /**
     * Display customer's loan history
     */
    public function index()
    {
        $loans = Loan::with('loanProduct')
                    ->where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('loans.index', compact('loans'));
    }

    /**
     * Display loan details
     */
    public function show(Loan $loan)
    {
        if (auth()->id() !== $loan->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $loan->load(['loanProduct', 'user', 'comaker']);

        return view('loans.show', compact('loan'));
    }

    /**
     * Generate PDF Invoice for the loan
     */
    public function invoice(Loan $loan)
    {
        if (auth()->id() !== $loan->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $loan->load(['loanProduct', 'user']);

        $html = view('loans.invoice', compact('loan'))->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"Loan_Invoice_{$loan->id}.pdf\"");
    }

    /**
     * Admin: List all loans
     */
    public function adminIndex(Request $request)
    {
        $query = Loan::with('user', 'loanProduct');

        // Search by borrower name or ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10))->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    /**
     * Admin: Display loan creation form
     */
    public function adminCreate()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'shareholder_capital']);
        $loanProducts = LoanProduct::where('is_active', true)->get();

        return view('admin.loans.create', compact('users', 'loanProducts'));
    }

    /**
     * Admin: Store loan created by administrator
     */
    public function adminStore(Request $request, \App\Services\AI\CategorizationService $categorizationService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'principal_amount' => 'required|numeric|min:0',
            'comaker_1_id' => 'nullable|exists:users,id|different:user_id',
            'comaker_2_id' => 'nullable|exists:users,id|different:user_id|different:comaker_1_id',
            'purpose' => 'required|string|max:1000',
        ]);

        // If no product is selected (field removed from UI), use a default configuration
        $product = LoanProduct::where('is_active', true)->first();

        $interestRate = $product ? $product->interest_rate : 5.0;
        $termMonths = $product ? $product->duration_months : 12;
        $productId = $product ? $product->id : null;

        try {
            DB::beginTransaction();

            $totalAmount = $this->lendingService->calculateTotal(
                $request->principal_amount,
                $interestRate,
                $termMonths
            );

            // AI Categorization
            $aiTag = $categorizationService->tagLoanPurpose($request->purpose);

            $loan = Loan::create([
                'user_id' => $request->user_id,
                'loan_product_id' => $productId,
                'comaker_id' => $request->comaker_1_id,
                'principal_amount' => $request->principal_amount,
                'interest_rate' => $interestRate,
                'term_months' => $termMonths,
                'total_amount' => $totalAmount,
                'status' => 'approved',
                'purpose' => $request->purpose . ($request->comaker_2_id ? " [Secondary Co-maker ID: {$request->comaker_2_id}]" : ""),
                'ai_tag' => $aiTag,
                'due_date' => now()->addMonths($product->duration_months),
                'payment_method' => 'Internal Transfer',
            ]);

            DB::commit();

            return redirect()->route('admin.loans.index')
                ->with('success', 'Loan successfully initialized for borrower.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Initialization Failed: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Update loan status
     */
    public function updateStatus(Request $request, Loan $loan)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,released,disbursed,active,rejected,cancelled,completed,overdue,past due,flagged'
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'released' && !$loan->released_at) {
            $data['released_at'] = now();
        }

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
        }

        $loan->update($data);

        return redirect()->back()->with('success', 'Loan status updated!');
    }
}
