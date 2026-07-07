<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class TestOllama extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ollama:test {prompt=hi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls check for Ollama, runs a prompt, and captures output into a string.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = env('OLLAMA_BASE_URL', 'http://localhost:11434');
        $model = env('OLLAMA_MODEL', 'llama3.2');
        $prompt = $this->argument('prompt');

        $this->info("Initializing Neural Link to {$baseUrl}...");

        try {
            // 1. Check if Ollama is accessible
            $this->warn("Step 1: Checking status...");
            $status = Http::get($baseUrl);
            if ($status->failed()) {
                $this->error("Ollama service not detected at {$baseUrl}. Ensure it is running.");
                return 1;
            }

            // 2. Simulate "pull" check (Ensuring model exists)
            $this->warn("Step 2: Verifying model [{$model}] availability...");
            $checkModel = Http::post("{$baseUrl}/api/show", ['name' => $model]);

            if ($checkModel->failed()) {
                $this->info("Model not found. Executing pull protocol...");
                // Note: Real pull can be slow, so we just log the requirement here
                $this->warn("Please run 'ollama pull {$model}' in your terminal.");
                return 1;
            }

            // 3. Run prompt and capture into string
            $this->warn("Step 3: Sending directive '{$prompt}'...");

            $response = Http::timeout(60)->post("{$baseUrl}/api/generate", [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
            ]);

            if ($response->successful()) {
                // Get the text and put it in a string variable
                $aiResponseString = $response->json()['response'] ?? '';

                $this->info("--- TEXT CAPTURED IN STRING ---");
                $this->line($aiResponseString);
                $this->info("--------------------------------");

                $this->success("Protocol successful. String length: " . strlen($aiResponseString));
            } else {
                $this->error("Neural generation failed: " . $response->status());
            }

        } catch (\Exception $e) {
            $this->error("Connection Error: " . $e->getMessage());
        }

        return 0;
    }
}
