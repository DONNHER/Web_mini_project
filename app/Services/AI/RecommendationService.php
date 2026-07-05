<?php

namespace App\Services\AI;

use App\Models\Loan;
use App\Models\User;
use App\Models\LoanProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    protected $aiManager;

    public function __construct(AIServiceManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Get AI-driven financial insights and pre-approval status.
     */
    public function getFinancialInsights(User $user): array
    {
        $cacheKey = "user_{$user->id}_financial_insights";

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            // 1. Analyze Repayment Behavior
            $loans = $user->loans()->with('repayments')->get();

            $stats = [
                'total_loans' => $loans->count(),
                'completed_loans' => $loans->where('status', 'completed')->count(),
                'on_time_payments' => 0,
                'delayed_payments' => 0,
                'current_overdue' => $loans->where('status', 'overdue')->count(),
            ];

            foreach ($loans as $loan) {
                foreach ($loan->repayments as $repayment) {
                    if ($repayment->payment_date && $repayment->payment_date > $loan->due_date) {
                        $stats['delayed_payments']++;
                    } else {
                        $stats['on_time_payments']++;
                    }
                }
            }

            // 2. Build AI Prompt for Credit Analysis
            $prompt = $this->buildCreditAnalysisPrompt($stats, $user);

            // 3. Call AI
            try {
                $aiResult = $this->aiManager->generateWithFallback($prompt, 'credit_insights');
                $insights = $this->parseInsightResponse($aiResult['text']);

                // 4. Attach Suggested Products if Reliability is High
                if ($insights['reliability_score'] >= 80 && $stats['delayed_payments'] === 0) {
                    $insights['suggested_products'] = LoanProduct::where('is_active', true)
                        ->where('interest_rate', '<', 5) // Suggest "Prime" rates for good borrowers
                        ->limit(3)
                        ->get()
                        ->toArray();
                }

                return $insights;

            } catch (\Exception $e) {
                Log::error("AI Credit Insight failed: " . $e->getMessage());
                return $this->fallbackInsights();
            }
        });
    }

    protected function buildCreditAnalysisPrompt(array $stats, User $user): string
    {
        $data = json_encode($stats);
        return "You are a Senior Credit Analyst for 'LendingSystem'.
        Analyze this borrower's performance data and provide a reliability rating and financial advice.

        Borrower Stats: {$data}
        Name: {$user->name}
        Member Since: {$user->created_at->format('Y-m-d')}

        Respond ONLY in structured JSON format with:
        'reliability_score' (0-100),
        'status' ('Excellent', 'Good', 'At Risk', 'Poor'),
        'ai_insight' (A 2-sentence summary of their reliability),
        'recommendation' (What they should do next, e.g., 'Eligible for higher limits' or 'Improve payment consistency').";
    }

    protected function parseInsightResponse(string $text): array
    {
        $cleanText = preg_replace('/```json|```/', '', $text);
        $data = json_decode(trim($cleanText), true);

        return [
            'reliability_score' => $data['reliability_score'] ?? 50,
            'status' => $data['status'] ?? 'Needs Review',
            'ai_insight' => $data['ai_insight'] ?? 'Insufficient data for analysis.',
            'recommendation' => $data['recommendation'] ?? 'Maintain regular payments.',
            'suggested_products' => []
        ];
    }

    protected function fallbackInsights(): array
    {
        return [
            'reliability_score' => 0,
            'status' => 'Pending Analysis',
            'ai_insight' => 'We are currently analyzing your payment history.',
            'recommendation' => 'Please wait for your next billing cycle.',
            'suggested_products' => []
        ];
    }
}
