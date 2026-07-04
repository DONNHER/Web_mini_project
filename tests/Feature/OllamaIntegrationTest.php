<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OllamaIntegrationTest extends TestCase
{
    #[Test]
    public function it_can_connect_to_real_local_ollama_server()
    {
        $baseUrl = config('ai.providers.ollama.base_url', 'http://localhost:11434');

        try {
            $response = Http::timeout(2)->get($baseUrl);

            if ($response->failed()) {
                $this->markTestSkipped("Ollama server is not running.");
            }
        } catch (\Exception $e) {
            $this->markTestSkipped("Could not reach Ollama server.");
        }

        $this->assertTrue($response->successful());
        $this->assertStringContainsString('Ollama is running', $response->body());
    }

    #[Test]
    public function it_can_generate_a_response_from_local_ollama()
    {
        $baseUrl = config('ai.providers.ollama.base_url', 'http://localhost:11434');
        $model = config('ai.providers.ollama.model', 'llama3.2');

        $response = Http::timeout(60)->post("$baseUrl/api/generate", [
            'model' => $model,
            'prompt' => 'Say hello to the developer!',
            'stream' => false,
        ]);

        if ($response->failed()) {
            $this->fail("Ollama generation failed for model '$model'.");
        }

        $text = $response->json()['response'] ?? '';
        dump("Ollama model ($model) says: " . trim($text));

        // As long as the AI says something, the integration is working!
        $this->assertNotEmpty($text);
    }
}
