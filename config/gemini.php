<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Google Gemini API.
    | Set your API key in the .env file as GEMINI_API_KEY.
    |
    */

    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
    
    /*
    |--------------------------------------------------------------------------
    | Generation Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control how the AI generates responses.
    |
    */
    'temperature' => env('GEMINI_TEMPERATURE', 0.7),
    'max_output_tokens' => env('GEMINI_MAX_TOKENS', 2048),
];