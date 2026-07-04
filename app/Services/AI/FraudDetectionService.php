<?php

namespace App\Services\AI;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FraudDetectionService
{
    protected $aiManager;

    public function __construct(AIServiceManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Analyze an order for potential fraud with full database context and audit logging.
     *
     * @param Order $order
     * @param string|null $ip
     * @return array
     */
    public function analyzeOrder(Order $order, ?string $ip = null): array
    {
        // 1. Retrieve Context from Database
        $user = $order->user;
        $context = $this->gatherSecurityContext($order, $user, $ip);

        // 2. Build Effective Prompt
        $prompt = $this->buildPrompt($context);

        // 3. Call AI through Manager (with fallback)
        try {
            $aiResult = $this->aiManager->generateWithFallback($prompt, 'fraud_detection');
            $parsedResponse = $this->parseJsonResponse($aiResult['text']);

            // 4. Log Decisions for Audit
            $this->logDetection($order, $context, $parsedResponse, $aiResult);

            return $parsedResponse;
        } catch (\Exception $e) {
            Log::error("Fraud Detection System Failure: " . $e->getMessage());
            return [
                'score' => 0,
                'category' => 'Manual Review',
                'reason' => 'System error during AI analysis. Local review required.',
            ];
        }
    }

    /**
     * Gathers relevant security data to provide context to the AI.
     */
    protected function gatherSecurityContext(Order $order, User $user, ?string $ip): array
    {
        $context = [
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'items_count' => $order->orderItems()->count(),
            'shipping_address' => $order->shipping_address,
            'user_account_age_days' => $user->created_at->diffInDays(now()),
            'user_previous_orders_count' => $user->orders()->where('id', '!=', $order->id)->count(),
            'user_total_spent_history' => $user->orders()->where('status', 'completed')->sum('total_amount'),
            'request_ip' => $ip ?? 'Unknown',
        ];

        // Retrieve Geolocation context (Free service, no key needed)
        if ($ip && $ip !== '127.0.0.1') {
            try {
                $geo = Http::timeout(2)->get("http://ip-api.com/json/{$ip}")->json();
                if ($geo && $geo['status'] === 'success') {
                    $context['ip_location'] = "{$geo['city']}, {$geo['country']}";
                }
            } catch (\Exception $e) {}
        }

        return $context;
    }

    protected function buildPrompt(array $context): string
    {
        $jsonData = json_encode($context);
        return "You are an expert Security AI for 'PageTurner' Bookstore.
        Analyze the following order data for FRAUD patterns (e.g., massive orders from new accounts, geolocation mismatches, unusual spending).

        Respond ONLY in a structured JSON format with:
        'score' (0 to 100, where 100 is definite fraud),
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

    /**
     * Persist the AI's decision to the database for audit and tracking.
     */
    protected function logDetection(Order $order, array $context, array $result, array $aiMetadata): void
    {
        try {
            DB::table('ai_security_logs')->insert([
                'feature' => 'fraud_detection',
                'user_id' => $order->user_id,
                'resource_type' => 'Order',
                'resource_id' => $order->id,
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
            Log::error("Failed to log AI security audit: " . $e->getMessage());
        }
    }
}
