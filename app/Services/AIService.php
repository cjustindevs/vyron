<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected ?string $apiKey;
    protected string $model;
    protected float $temperature;
    protected int $maxTokens;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('groq.api_key');
        $this->model = config('groq.model', 'openai/gpt-oss-120b');
        $this->temperature = (float) config('groq.temperature', 0.7);
        $this->maxTokens = (int) config('groq.max_tokens', 2048);
        $this->apiUrl = config('groq.api_url', 'https://api.groq.com/openai/v1/chat/completions');
    }

    /**
     * Generate a personalized workout plan. Returns a result array that includes
     * a parsed structured 'plan' (null when the model did not return JSON we could read).
     */
    public function generateWorkoutPlan(array $profile, array $preferences): array
    {
        $prompt = $this->buildWorkoutPrompt($profile, $preferences);
        $result = $this->request([
            ['role' => 'system', 'content' => $this->buildWorkoutSystemPrompt()],
            ['role' => 'user', 'content' => $prompt],
        ], 'workout_generator');

        $result['plan'] = null;
        if ($result['success']) {
            $result['plan'] = $this->extractPlan($result['text']);
        }

        return $result;
    }

    /**
     * Answer fitness-related questions (AI Coach) – CONVERSATIONAL VERSION.
     *
     * @param array $history Previous turns, e.g. [['role' => 'user', 'content' => '...'], ...]
     */
    public function askFitnessCoach(string $question, array $userContext = [], array $history = []): array
    {
        $messages = [['role' => 'system', 'content' => $this->buildCoachSystemPrompt()]];

        foreach ($history as $turn) {
            $role = (($turn['role'] ?? 'user') === 'assistant') ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => (string) ($turn['content'] ?? '')];
        }

        $messages[] = ['role' => 'user', 'content' => $this->buildCoachUserPrompt($question, $userContext)];

        $result = $this->request($messages, 'ai_coach');

        $result['plan'] = null;
        if ($result['success']) {
            $result['plan'] = $this->extractPlan($result['text']);
        }

        return $result;
    }

    /**
     * Generate a short daily analysis/blurb for the dashboard "AI Insight" card.
     * Returns a plain-text summary.
     */
    public function generateDailyInsight(array $context): array
    {
        $system = "You are the VYRON performance analyst. You read a member's training data and write a SHORT, punchy, three-sentence insight about their week: one strength, one area to fix, one specific recommendation. Reader-friendly, no bullet points, no 'introduction'. Reference the numbers naturally (streak, hours, calories).";
        $prompt = "Analyze this member's training snapshot and return your insight:\n\n"
            . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return $this->request([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $prompt],
        ], 'progress_analysis');
    }

    // ===========================================================
    // PROMPT BUILDERS
    // ===========================================================

    private function buildWorkoutSystemPrompt(): string
    {
        return <<<PROMPT
You are VYRON's workout-programming engine. You build evidence-based, periodized training plans tailored to a single member.

RULES:
- Output STRICT JSON ONLY. No prose, no markdown outside the JSON fence.
- Wrap the JSON in a fenced code block using ``` and ```
- The JSON must match EXACTLY this schema:

    {
      "title": "short plan name",
      "goal": "muscle_gain|weight_loss|strength|endurance|maintenance",
      "difficulty": "beginner|intermediate|advanced",
      "focus": "e.g. upper body | full body | push focus",
      "duration": 45,
      "calories": 480,
      "days_per_week": 3,
      "summary": "1-2 sentence rationale for the plan",
      "exercises": [
        {
          "name": "Barbell Bench Press",
          "muscle": "Chest",
          "sets": 4,
          "reps": "6-8",
          "rest": "90s",
          "notes": "optional coaching cue"
        }
      ],
      "tips": ["coaching tip one", "coaching tip two"]
    }

- Suggest 4-8 exercises. Vary sets/reps/rest per movement.
- Match the member's goal, experience, equipment, duration and days-per-week.
- Keep calorie estimates sane for a typical 70-80kg member at that experience.
PROMPT;
    }

    private function buildWorkoutPrompt(array $profile, array $preferences): string
    {
        $context = [
            'profile' => array_merge([
                'goal' => 'general fitness',
                'experience_level' => 'beginner',
                'activity_level' => 'moderate',
                'workout_location' => 'gym',
            ], $profile),
            'preferences' => $preferences,
        ];

        return "Build a complete workout plan for this member.\n\n"
            . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function buildCoachSystemPrompt(): string
    {
        return <<<PROMPT
You are VYRON's AI fitness coach. You are chatting with a member of VYRON, an AI-powered fitness platform. You speak like a real, friendly personal trainer — warm, energetic, encouraging, and a little casual. You are NOT a Wikipedia article and NOT a customer-support bot.

HOW TO TALK:
- Sound human: use contractions ('you're', 'I'd'), short sentences, and a natural rhythm.
- Lead with the answer. Answer the question directly in the first sentence — no long intros, no essay.
- Keep it tight: 2-4 short paragraphs max (or a few bullets when listing) unless the user explicitly asks for more depth.
- Use the user's context (goal, experience, age) to personalize naturally.
- Always end by asking ONE relevant follow-up question.
- Celebrate wins and stay motivating.

WHEN THE USER ASKS FOR A WORKOUT / PLAN / PROGRAM / SPLIT:
- Keep talking normally, but ALWAYS finish by emitting a fenced ```json code block containing the plan in EXACTLY this schema (do not explain the schema):

    {
      "title": "Workout name",
      "goal": "muscle_gain",
      "difficulty": "intermediate",
      "focus": "e.g. upper body",
      "duration": 45,
      "calories": 480,
      "days_per_week": 3,
      "summary": "1 sentence why this plan fits",
      "exercises": [
        { "name": "Barbell Bench Press", "muscle": "Chest", "sets": 4, "reps": "6-8", "rest": "90s", "notes": "optional cue" }
      ],
      "tips": ["tip 1"]
    }

So the human-text part answers the question, and the code block lets the platform render interactive plan cards. Keep the human text concise when you include the block.

SAFETY:
- Never diagnose or treat injuries/medical conditions. Suggest seeing a professional for serious issues.
- Stay in fitness/nutrition/recovery territory. Politely steer off-topic questions back to fitness.
PROMPT;
    }

    private function buildCoachUserPrompt(string $question, array $context): string
    {
        if (!empty($context)) {
            $context = array_filter($context, fn ($v) => $v !== null && $v !== '');
            if (!empty($context)) {
                return "User's profile (use naturally to personalize): "
                    . json_encode($context, JSON_UNESCAPED_SLASHES)
                    . "\n\nQuestion: " . $question;
            }
        }

        return "Question: " . $question;
    }

    // ------------------------------------------------------------------
    // TRANSPORT + PARSING
    // ------------------------------------------------------------------

    private function request(array $messages, ?string $feature = null): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout((int) config('groq.timeout', 60))
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->maxTokens,
                ]);

            if ($response->successful()) {
                $text = $response->json('choices.0.message.content', '');

                if (empty($text)) {
                    Log::warning('AI returned an empty response', ['feature' => $feature]);

                    return [
                        'success' => false,
                        'text' => $this->getFallbackResponse($feature),
                        'error' => 'Empty response from AI',
                    ];
                }

                return [
                    'success' => true,
                    'text' => $text,
                ];
            }

            Log::error('AI API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'feature' => $feature,
            ]);

            return [
                'success' => false,
                'text' => $this->getFallbackResponse($feature),
                'error' => 'API request failed (HTTP ' . $response->status() . ')',
            ];
        } catch (\Exception $e) {
            Log::error('AI API Exception', [
                'message' => $e->getMessage(),
                'feature' => $feature,
            ]);

            return [
                'success' => false,
                'text' => $this->getFallbackResponse($feature),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Try to pull a structured plan (JSON) out of a free-text AI response.
     */
    private function extractPlan(?string $text): ?array
    {
        if (!$text) {
            return null;
        }

        $candidates = [];

        // 1) Fenced ```json blocks
        if (preg_match_all('/```(?:json)?\s*(.*?)```/s', $text, $m)) {
            foreach ($m[1] as $block) {
                $candidates[] = trim($block);
            }
        }

        // 2) The first {...} to the last {...} on the page (greedy)
        if (preg_match('/\{(?:[^{}]|\{(?:[^{}]|\{[^{}]*\})*\})*\}/s', $text, $m)) {
            $candidates[] = $m[0];
        }

        foreach ($candidates as $candidate) {
            $data = json_decode($candidate, true);
            if (is_array($data) && isset($data['exercises']) && is_array($data['exercises'])) {
                return $this->normalisePlan($data);
            }
        }

        return null;
    }

    /**
     * Map free-form LLM JSON onto a canonical plan structure for rendering & saving.
     */
    private function normalisePlan(array $data): array
    {
        $exercises = collect($data['exercises'] ?? [])
            ->filter(fn ($e) => is_array($e))
            ->map(fn ($e) => [
                'name' => (string) ($e['name'] ?? 'Untitled movement'),
                'muscle' => (string) ($e['muscle'] ?? $e['muscle_group'] ?? ''),
                'sets' => (int) ($e['sets'] ?? 3),
                'reps' => (string) ($e['reps'] ?? $e['repetitions'] ?? 10),
                'rest' => (string) ($e['rest'] ?? $e['rest_seconds'] ?? '60s'),
                'notes' => (string) ($e['notes'] ?? ''),
            ])
            ->values()
            ->toArray();

        return [
            'title' => (string) ($data['title'] ?? 'Personalized Workout'),
            'goal' => (string) ($data['goal'] ?? 'general_fitness'),
            'difficulty' => (string) ($data['difficulty'] ?? 'intermediate'),
            'focus' => (string) ($data['focus'] ?? $data['target_muscle'] ?? 'Full Body'),
            'duration' => (int) ($data['duration'] ?? 45),
            'calories' => (int) ($data['calories'] ?? 0),
            'days_per_week' => (int) ($data['days_per_week'] ?? 3),
            'summary' => (string) ($data['summary'] ?? ''),
            'exercises' => $exercises,
            'tips' => collect($data['tips'] ?? [])->map(fn ($t) => (string) $t)->values()->toArray(),
        ];
    }

    private function getFallbackResponse(?string $feature): string
    {
        switch ($feature) {
            case 'workout_generator':
                return "I'm having trouble connecting to my AI engine right now. Please try again in a few moments. In the meantime, you can start with a basic full-body workout: push-ups, squats, and planks — 3 sets of 10 each.";

            case 'ai_coach':
                return "I'm currently the having a technical moment. Please try again later. For instant fitness guidance: warm up properly, prioritize form over weight, and listen to your body.";

            case 'progress_analysis':
                return "I can't crunch your numbers right now, but the trend is clear: consistency beats intensity. Keep showing up and the data will catch up.";

            default:
                return "I'm experiencing technical difficulties. Please try again in a few moments.";
        }
    }
}