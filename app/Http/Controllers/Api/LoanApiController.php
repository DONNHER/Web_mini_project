<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Services\AI\RiskAssessmentService;
use App\Services\LendingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanApiController extends Controller
{
    protected $riskService;
    protected $lendingService;

    public function __construct(RiskAssessmentService $riskService, LendingService $lendingService)
    {
        $this->riskService = $riskService;
        $this->lendingService = $lendingService;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Loan::with('loanProduct')
            ->where('user_id', $request->user()->id)
            ->orderBy('id', 'desc');

        $loans = $query->cursorPaginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $loans,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loan_product_id' => 'required|exists:loan_products,id',
            'principal_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'comaker_id' => 'nullable|exists:users,id',
        ]);

        $product = LoanProduct::findOrFail($validated['loan_product_id']);

        try {
            $this->lendingService->validateApplication($product, $validated['principal_amount']);

            DB::beginTransaction();

            $totalAmount = $this->lendingService->calculateTotal(
                $validated['principal_amount'],
                $product->interest_rate,
                $product->duration_months
            );

            $loan = Loan::create([
                'user_id' => $request->user()->id,
                'loan_product_id' => $product->id,
                'comaker_id' => $validated['comaker_id'] ?? null,
                'principal_amount' => $validated['principal_amount'],
                'interest_rate' => $product->interest_rate,
                'term_months' => $product->duration_months,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'due_date' => now()->addMonths($product->duration_months),
            ]);

            DB::commit();

            // AI Risk Assessment
            $riskAnalysis = $this->riskService->analyzeRisk($loan, $request->ip());

            if ($riskAnalysis['category'] === 'High') {
                $loan->update(['status' => 'flagged']);
            }

            return response()->json([
                'success' => true,
                'loan_id' => $loan->id,
                'status' => $loan->status,
                'risk_analysis' => [
                    'rating' => $riskAnalysis['category'],
                    'score' => $riskAnalysis['score'],
                    'reason' => $riskAnalysis['reason']
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
