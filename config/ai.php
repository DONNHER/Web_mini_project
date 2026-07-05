<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This provider will be used when no specific feature mapping is found.
    |
    */

    'default' => env('AI_DEFAULT_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configure credentials and endpoints for different AI services.
    |
    */

    'providers' => [
        'gemini' => [
            'name' => 'Google Gemini',
            'key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
            'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent',
            'rate_limit' => 1500, // requests per day (free tier)
        ],

        'openai' => [
            'name' => 'OpenAI',
            'key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'endpoint' => 'https://api.openai.com/v1/chat/completions',
            'rate_limit' => 200000, // tokens per day (example)
        ],

        'ollama' => [
            'name' => 'Ollama (Local)',
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model' => env('OLLAMA_MODEL', 'llama3.2'),
            'rate_limit' => null, // unlimited
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Mapping
    |--------------------------------------------------------------------------
    |
    | Map specific application features to specific AI providers.
    |
    */

    'features' => [
        'risk_assessment' => 'gemini',
        'chat' => 'ollama',
        'credit_insights' => 'gemini',
        'summarization' => 'gemini',
        'recommendations' => 'gemini',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | The chain of providers to try if the primary one fails.
    |
    */

    'fallback_enabled' => env('AI_FALLBACK_ENABLED', true),
    'fallback_chain' => ['gemini', 'openai', 'ollama'],

    /*
    |--------------------------------------------------------------------------
    | Cost & Monitoring
    |--------------------------------------------------------------------------
    |
    | Thresholds for alerting and cost control.
    |
    */

    'monitoring' => [
        'alert_threshold' => 10.00, // USD per day
        'timeout' => 5, // seconds
    ],

];
