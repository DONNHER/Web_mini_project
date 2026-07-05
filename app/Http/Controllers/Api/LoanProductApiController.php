<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanProductResource;
use App\Models\LoanProduct;
use App\Repositories\LoanProductRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoanProductApiController extends Controller
{
    protected $loanProductRepository;

    public function __construct(LoanProductRepository $loanProductRepository)
    {
        $this->loanProductRepository = $loanProductRepository;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->has('search')) {
            $products = $this->loanProductRepository->search($request->search, $request->get('per_page', 20));
        } else {
            $products = $this->loanProductRepository->getActiveCatalog($request->get('per_page', 20));
        }

        return LoanProductResource::collection($products);
    }

    public function show(Request $request, LoanProduct $loanProduct): LoanProductResource
    {
        $loanProduct->loadMissing(['category:id,name']);

        return new LoanProductResource($loanProduct);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:loan_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interest_rate' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0|gte:min_amount',
        ]);

        $product = LoanProduct::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Loan product created successfully',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, LoanProduct $loanProduct): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:loan_categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'interest_rate' => 'sometimes|numeric|min:0',
            'duration_months' => 'sometimes|integer|min:1',
            'min_amount' => 'sometimes|numeric|min:0',
            'max_amount' => 'sometimes|numeric|min:0|gte:min_amount',
        ]);

        $loanProduct->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Loan product updated successfully',
            'data' => $loanProduct,
        ]);
    }

    public function destroy(LoanProduct $loanProduct): JsonResponse
    {
        $loanProduct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Loan product deleted successfully',
        ]);
    }
}
