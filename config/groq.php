<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Groq API Configuration
    |--------------------------------------------------------------------------
    |
    | Groq is a free, fast, OpenAI-compatible LLM provider.
    | Get a free API key (no credit card required) at:
    | https://console.groq.com/keys
    |
    */

    'api_key' => env('GROQ_API_KEY'),
    'model' => env('GROQ_MODEL', 'openai/gpt-oss-120b'),
    'api_url' => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),

    /*
    |--------------------------------------------------------------------------
    | Generation Configuration
    |--------------------------------------------------------------------------
    */
    'temperature' => env('GROQ_TEMPERATURE', 0.7),
    'max_tokens' => env('GROQ_MAX_TOKENS', 2048),
    'timeout' => env('GROQ_TIMEOUT', 60),
];