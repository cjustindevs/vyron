<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hugging Face API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Hugging Face Inference API.
    |
    */

    'api_key' => env('HF_API_KEY'),
    'model' => env('HF_MODEL', 'gpt2'),
    'api_url' => 'https://api-inference.huggingface.co/models/',
];