<?php

namespace Tests\Feature;

use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIAuditLoggingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_ai_decisions_to_the_audit_channel()
    {
        Log::shouldReceive('channel')
            ->with('ai_audit')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->with('AI Decision', \Mockery::on(function ($data) {
                return $data['feature'] === 'fraud_detection' &&
                       isset($data['input_hash']) &&
                       isset($data['output_hash']) &&
                       $data['provider_used'] === 'gemini';
            }))
            ->once();

        // Also mock standard Log for usage/cost tracking if necessary
        Log::shouldReceive('warning');
        Log::shouldReceive('alert');
        Log::shouldReceive('info'); // Catch any other info calls

        Config::set('ai.providers.gemini.key', 'test_key');

        $manager = new AIServiceManager();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => '{"score": 85, "category": "High", "reason": "Test"}']]]]]
            ], 200)
        ]);

        $manager->generate('Suspicious input', 'fraud_detection');
    }
}
