<?php

namespace App\Services\AI;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RiskAssessmentService
{
    protected $aiManager;

    public function __construct(AIServiceManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Analyze a loan application for credit risk and potential default.
     *
     * @param Loan $loan
     * @param string|null $ip
     * @return array
     */
    public function analyzeRisk(Loan $loan, ?string $ip = null): array
    {
        $user = $loan->user;
        $context = $this->gatherSecurityContext($loan, $user, $ip);

        $prompt = $this->buildPrompt($context);

        try {
            $aiResult = $this->aiManager->generateWithFallback($prompt, 'risk_assessment');
            $parsedResponse = $this->parseJsonResponse($aiResult['text']);

            $this->logDecision($loan, $context, $parsedResponse, $aiResult);

            return $parsedResponse;
        } catch (\Exception $e) {
            Log::error("Risk Assessment System Failure: " . $e->getMessage());
            return [
                'score' => 0,
                'category' => 'Manual Review',
                'reason' => 'System error during AI analysis. Local review required.',
            ];
        }
    }

    protected function gatherSecurityContext(Loan $loan, User $user, ?string $ip): array
    {
        $context = [
            'loan_id' => $loan->id,
            'principal_amount' => $loan->principal_amount,
            'interest_rate' => $loan->interest_rate,
            'term_months' => $loan->term_months,
            'user_account_age_days' => $user->created_at->diffInDays(now()),
            'user_previous_loans_count' => $user->loans()->where('id', '!=', $loan->id)->count(),
            'user_active_loans_count' => $user->loans()->whereIn('status', ['released', 'overdue'])->count(),
            'has_comaker' => !is_null($loan->comaker_id),
            'request_ip' => $ip ?? 'Unknown',
        ];

        return $context;
    }

    protected function buildPrompt(array $context): string
    {
        $jsonData = json_encode($context);
        return "You are an expert Credit Risk AI for 'LendingSystem'.
        Analyze the following loan application for DEFAULT risk (e.g., high loan amounts for new accounts, multiple active loans).

        Respond ONLY in a structured JSON format with:
        'score' (0 to 100, where 100 is extreme risk of default),
        'category' ('Low', 'Medium', 'High'),
        'reason' (A concise 1-sentence explanation).

        Data: {$jsonData}";
    }

    protected function parseJsonResponse(string $text): array
    {
        $cleanText = preg_replace('/```json|```/', '', $text);
        $data = json_decode(trim($cleanText), true);

        return [
            'score' => $data['score'] ?? 0,
            'category' => $data['category'] ?? 'Manual Review',
            'reason' => $data['reason'] ?? 'AI response was not in expected format.',
        ];
    }

    protected function logDecision(Loan $loan, array $context, array $result, array $aiMetadata): void
    {
        try {
            DB::table('ai_security_logs')->insert([
                'feature' => 'risk_assessment',
                'user_id' => $loan->user_id,
                'resource_type' => 'Loan',
                'resource_id' => $loan->id,
                'risk_score' => $result['score'],
                'risk_category' => $result['category'],
                'reason' => $result['reason'],
                'provider' => $aiMetadata['provider'],
                'response_time_ms' => $aiMetadata['time_ms'],
                'input_context' => json_encode($context),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log AI risk audit: " . $e->getMessage());
        }
    }
}
