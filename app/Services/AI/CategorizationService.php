<?php

namespace App\Services\AI;

use App\Models\LoanProduct;
use Illuminate\Support\Facades\Log;

class CategorizationService
{
    protected $aiManager;

    public function __construct(AIServiceManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Categorize a user's intent or description into an existing Loan Product.
     */
    public function categorizeLoanIntent(string $description): array
    {
        $products = LoanProduct::where('is_active', true)->get(['id', 'name', 'description'])->toArray();
        $productsJson = json_encode($products);

        $prompt = "You are a Customer Service AI for 'LendingSystem'.
        A user has described their loan need: \"{$description}\"

        Based on these available loan products, pick the most relevant one:
        {$productsJson}

        Respond ONLY in a structured JSON format with:
        'product_id' (The ID of the best matching product),
        'confidence_score' (0-100),
        'reason' (A short explanation why this product fits).";

        try {
            $aiResult = $this->aiManager->generateWithFallback($prompt, 'categorization');
            $data = $this->parseJsonResponse($aiResult['text']);

            $productId = $data['product_id'] ?? null;
            $suggestedProducts = [];

            if ($productId) {
                $suggestedProducts = LoanProduct::where('id', $productId)->get();
            }

            return [
                'product_id' => $productId,
                'category_id' => $productId, // Keep for backward compatibility with UI if needed
                'confidence_score' => $data['confidence_score'] ?? 0,
                'reason' => $data['reason'] ?? 'Unable to determine product.',
                'suggested_products' => $suggestedProducts,
                'provider' => $aiResult['provider']
            ];
        } catch (\Exception $e) {
            Log::error("Categorization failed: " . $e->getMessage());
            return [
                'product_id' => null,
                'category_id' => null,
                'confidence_score' => 0,
                'reason' => 'AI Service error',
                'suggested_products' => []
            ];
        }
    }

    /**
     * Automatically tag a loan purpose.
     */
    public function tagLoanPurpose(string $purpose): string
    {
        $prompt = "You are an automated loan processing system.
        Categorize the following loan purpose into a single word tag (e.g., 'Business', 'Education', 'Medical', 'Travel', 'Emergency', 'Other').

        Purpose: \"{$purpose}\"

        Respond ONLY with the single tag word.";

        try {
            $aiResult = $this->aiManager->generate($prompt, 'tagging');
            return trim($aiResult);
        } catch (\Exception $e) {
            Log::error("Purpose tagging failed: " . $e->getMessage());
            return 'Uncategorized';
        }
    }

    protected function parseJsonResponse(string $text): array
    {
        $cleanText = preg_replace('/```json|```/', '', $text);
        return json_decode(trim($cleanText), true) ?? [];
    }
}
