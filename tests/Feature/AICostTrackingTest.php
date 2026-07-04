<?php

namespace Tests\Feature;

use App\Models\AIUsageLog;
use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AICostTrackingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_usage_and_cost_on_successful_generation()
    {
        Config::set('ai.providers.openai.key', 'test_key');

        $manager = new AIServiceManager();

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'This is a test response.']]]
            ], 200)
        ]);

        $manager->generate('Test prompt', 'chat');

        $this->assertDatabaseHas('ai_usage_logs', [
            'provider' => 'openai',
            'feature' => 'chat',
        ]);

        $log = AIUsageLog::first();
        $this->assertGreaterThan(0, $log->tokens_used);
        $this->assertGreaterThan(0, $log->cost_estimate);
    }
}
