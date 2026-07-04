<?php

namespace App\Services\AI;

use App\Models\AIUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class AIServiceManager
{
    /**
     * Generate content using the default or specified provider.
     */
    public function generate(string $prompt, ?string $feature = null): string
    {
        $provider = $this->resolveProvider($feature);
        return $this->callProvider($provider, $prompt, $feature);
    }

    /**
     * Generate content with automated multi-provider fallback.
     */
    public function generateWithFallback(string $prompt, ?string $feature = null): array
    {
        $start = microtime(true);
        $primary = $this->resolveProvider($feature);
        $chain = Config::get('ai.fallback_chain', ['gemini', 'openai', 'ollama']);
        $chain = array_unique(array_merge([$primary], $chain));

        if (Config::get('ai.fallback_enabled', true)) {
            foreach ($chain as $provider) {
                try {
                    if ($this->isAvailable($provider)) {
                        $text = $this->callProvider($provider, $prompt, $feature);

                        Log::info("AI Generation successful using provider: [{$provider}]");

                        return [
                            'text' => $text,
                            'provider' => $provider,
                            'time_ms' => (microtime(true) - $start) * 1000
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning("AI Provider [{$provider}] failed: " . $e->getMessage());
                    continue;
                }
            }
        } else {
            $text = $this->callProvider($primary, $prompt, $feature);
            return [
                'text' => $text,
                'provider' => $primary,
                'time_ms' => (microtime(true) - $start) * 1000
            ];
        }

        throw new \RuntimeException('All AI providers unavailable');
    }

    protected function callProvider(string $name, string $prompt, ?string $feature = null): string
    {
        $config = Config::get("ai.providers.{$name}");
        $response = '';

        try {
            $response = match ($name) {
                'gemini' => $this->callGemini($config, $prompt),
                'openai' => $this->callOpenAI($config, $prompt),
                'ollama' => $this->callOllama($config, $prompt),
                default  => throw new \Exception("Unsupported provider: {$name}"),
            };

            // Log usage on success
            $this->logUsage($name, $feature, $prompt, $response);
            $this->logAudit($name, $feature, $prompt, $response);

            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Estimate tokens and log cost
     */
    protected function logUsage(string $provider, ?string $feature, string $prompt, string $response): void
    {
        // Simple estimation: 4 chars per token
        $tokens = (int) ceil((strlen($prompt) + strlen($response)) / 4);

        AIUsageLog::create([
            'provider' => $provider,
            'feature' => $feature ?? 'general',
            'tokens_used' => $tokens,
            'cost_estimate' => $this->calculateCost($provider, $tokens),
        ]);

        // Alerting logic (simplified)
        $dailyCost = AIUsageLog::whereDate('created_at', now())->sum('cost_estimate');
        $threshold = Config::get('ai.monitoring.alert_threshold', 10.00);

        if ($dailyCost >= $threshold) {
            Log::alert("AI Cost Threshold Reached! Daily Cost: \${$dailyCost}");
        }
    }

    protected function calculateCost(string $provider, int $tokens): float
    {
        $rates = [
            'openai' => 0.00015 / 1000, // GPT-4o-mini approx rate per token
            'gemini' => 0, // Free tier
            'ollama' => 0, // Local
        ];

        return ($rates[$provider] ?? 0) * $tokens;
    }

    /**
     * Audit log for AI decisions
     */
    protected function logAudit(string $provider, ?string $feature, string $input, string $output): void
    {
        // Try to extract confidence/score if JSON
        $confidence = null;
        $decoded = json_decode(preg_replace('/```json|```/', '', $output), true);
        if (isset($decoded['score'])) {
            $confidence = $decoded['score'] / 100;
        }

        Log::channel('ai_audit')->info('AI Decision', [
            'feature' => $feature ?? 'content_generation',
            'input_hash' => md5($input),
            'output_hash' => md5($output),
            'confidence' => $confidence,
            'provider_used' => $provider,
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]);
    }

    protected function isAvailable(string $provider): bool
    {
        $config = Config::get("ai.providers.{$provider}");
        if (!$config) return false;
        if ($provider === 'ollama') return !empty($config['base_url']);
        return !empty($config['key']);
    }

    protected function resolveProvider(?string $feature): string
    {
        return Config::get("ai.features.{$feature}", Config::get('ai.default', 'gemini'));
    }

    protected function callGemini(array $config, string $prompt): string
    {
        $url = str_replace('{model}', $config['model'], $config['endpoint']) . '?key=' . $config['key'];
        $response = Http::timeout(5)->post($url, ['contents' => [['parts' => [['text' => $prompt]]]]]);
        if ($response->failed()) throw new \Exception("Gemini failed: " . $response->status());
        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    protected function callOpenAI(array $config, string $prompt): string
    {
        $response = Http::withToken($config['key'])->timeout(5)->post($config['endpoint'], [
            'model' => $config['model'],
            'messages' => [['role' => 'user', 'content' => $prompt]]
        ]);
        if ($response->failed()) throw new \Exception("OpenAI failed: " . $response->status());
        return $response->json()['choices'][0]['message']['content'] ?? '';
    }

    protected function callOllama(array $config, string $prompt): string
    {
        $response = Http::timeout(60)->post($config['base_url'] . '/api/generate', [
            'model' => $config['model'],
            'prompt' => $prompt,
            'stream' => false,
        ]);
        if ($response->failed()) throw new \Exception("Ollama failed: " . $response->status());
        return $response->json()['response'] ?? '';
    }
}
