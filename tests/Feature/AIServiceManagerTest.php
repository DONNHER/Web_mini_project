<?php

namespace Tests\Feature;

use App\Services\AI\AIServiceManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AIServiceManagerTest extends TestCase
{
    #[Test]
    public function it_resolves_default_provider()
    {
        Config::set('ai.default', 'openai');
        Config::set('ai.providers.openai.key', 'test_key');

        $manager = new AIServiceManager();

        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'OpenAI Response']]]], 200)]);

        $result = $manager->generate('Hello');
        $this->assertEquals('OpenAI Response', $result);
    }

    #[Test]
    public function it_uses_feature_mapping()
    {
        Config::set('ai.features.chat', 'ollama');

        $manager = new AIServiceManager();

        Http::fake(['localhost:11434/*' => Http::response(['response' => 'Ollama Response'], 200)]);

        $result = $manager->generate('Hello', 'chat');
        $this->assertEquals('Ollama Response', $result);
    }

    #[Test]
    public function it_falls_back_through_the_chain()
    {
        Config::set('ai.fallback.chain', ['gemini', 'ollama']);
        Config::set('ai.providers.gemini.key', 'test_key');

        $manager = new AIServiceManager();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500), // Gemini fails
            'localhost:11434/*' => Http::response(['response' => 'Ollama Fallback Success'], 200) // Ollama succeeds
        ]);

        $result = $manager->generateWithFallback('Hello');
        $this->assertEquals('Ollama Fallback Success', $result['text']);
        $this->assertEquals('ollama', $result['provider']);
    }
}
