<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HuggingFaceService
{
    protected ?string $apiKey;  // ✅ Now nullable
    protected string $model;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('huggingface.api_key');
        $this->model = config('huggingface.model', 'gpt2');
        $this->apiUrl = config('huggingface.api_url', 'https://api-inference.huggingface.co/models/');
    }

    /**
     * Generate a fitness response using Hugging Face
     */
    public function askFitnessCoach(string $question, array $userContext = []): array
    {
        $prompt = $this->buildFitnessPrompt($question, $userContext);
        return $this->sendRequest($prompt, 'ai_coach');
    }

    /**
     * Build the fitness prompt
     */
    private function buildFitnessPrompt(string $question, array $context): string
    {
        $contextStr = !empty($context) ? "User Context: " . json_encode($context) : "";

        return "You are VYRON, an AI fitness coach. Provide helpful fitness advice.

{$contextStr}

User Question: {$question}

Guidelines:
- Be professional and encouraging
- Prioritize safety
- Do NOT provide medical advice
- Focus on evidence-based fitness information
- Explain concepts clearly

Answer:";
    }

    /**
     * Generate a workout plan using Hugging Face
     */
    public function generateWorkoutPlan(array $profile, array $preferences): array
    {
        $prompt = $this->buildWorkoutPrompt($profile, $preferences);
        return $this->sendRequest($prompt, 'workout_generator');
    }

    /**
     * Build the workout prompt
     */
    private function buildWorkoutPrompt(array $profile, array $preferences): string
    {
        $equipment = isset($profile['equipment']) && is_array($profile['equipment'])
            ? implode(', ', $profile['equipment'])
            : 'None';

        return "Create a personalized workout plan for:

Goal: {$profile['goal']}
Experience: {$profile['experience_level']}
Equipment: {$equipment}
Duration: {$preferences['duration']} minutes
Days per week: {$preferences['days_per_week']}

Provide a structured weekly workout plan with exercises, sets, reps, and rest. Include safety tips.";
    }

    /**
     * Send request to Hugging Face API
     */
    private function sendRequest(string $prompt, string $feature = null): array
    {
        $cacheKey = md5($prompt . $feature);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $url = $this->apiUrl . $this->model;

            $headers = [
                'Content-Type' => 'application/json',
            ];

            // Add API key if available (for private models or higher rate limits)
            if ($this->apiKey) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($url, [
                    'inputs' => $prompt,
                    'parameters' => [
                        'max_new_tokens' => 256,
                        'temperature' => 0.7,
                        'do_sample' => true,
                    ]
                ]);

            // Handle model loading
            if ($response->status() === 503) {
                return [
                    'success' => false,
                    'text' => 'The model is loading. Please wait a few seconds and try again.',
                    'error' => 'Model loading (503)',
                    'retry_after' => 10,
                ];
            }

            if ($response->successful()) {
                $data = $response->json();
                $text = $this->extractText($data);

                if (empty($text)) {
                    return [
                        'success' => false,
                        'text' => 'Could not generate a response. Please try again.',
                        'error' => 'Empty response from AI'
                    ];
                }

                Cache::put($cacheKey, [
                    'success' => true,
                    'text' => $text
                ], 300);

                return [
                    'success' => true,
                    'text' => $text
                ];
            }

            Log::error('Hugging Face API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'feature' => $feature
            ]);

            return [
                'success' => false,
                'text' => 'I\'m experiencing technical difficulties. Please try again later.',
                'error' => 'API request failed'
            ];

        } catch (\Exception $e) {
            Log::error('Hugging Face API Exception', [
                'message' => $e->getMessage(),
                'feature' => $feature
            ]);

            return [
                'success' => false,
                'text' => 'I\'m experiencing technical difficulties. Please try again later.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract text from Hugging Face response
     */
    private function extractText(array $data): string
    {
        // Handle different response formats
        if (isset($data[0]['generated_text'])) {
            return $data[0]['generated_text'];
        }

        if (isset($data['generated_text'])) {
            return $data['generated_text'];
        }

        if (isset($data['error'])) {
            return '';
        }

        return json_encode($data);
    }
}