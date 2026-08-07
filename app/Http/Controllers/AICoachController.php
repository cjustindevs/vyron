<?php

namespace App\Http\Controllers;

use App\Models\AIConversation;
use App\Models\SavedProgram;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AICoachController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show the AI Coach chat interface with persisted history.
     */
    public function index()
    {
        $conversation = $this->getConversationHistory();
        $threads = $this->getThreads();
        $coachState = $this->getCoachState();

        // Suggested questions for the user
        $suggestedQuestions = [
            'How do I build muscle?',
            'What is progressive overload?',
            'How much protein should I eat?',
            'Can I train every day?',
            'What muscles does the deadlift target?',
            'How do I lose belly fat?',
            'What is a good warm-up routine?',
            'How important is sleep for fitness?',
        ];

        return view('ai-coach.index', compact('conversation', 'threads', 'coachState', 'suggestedQuestions'));
    }

    /**
     * Send a message to the AI and get a response.
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1600',
        ]);

        $user = Auth::user();
        $userMessage = $request->input('message');

        // Pass the recent chat history so the AI remembers the conversation
        $history = $this->getConversationHistory()
            ->map(fn ($msg) => [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ])
            ->values()
            ->toArray();

        $result = $this->aiService->askFitnessCoach($userMessage, $this->getUserContext($user), $history);

        $aiResponse = $result['success']
            ? $result['text']
            : ($result['error'] ?? "I'm having trouble responding right now. Please try again.");

        // Persist the exchange
        $conversation = AIConversation::create([
            'user_id' => $user->id,
            'prompt' => $userMessage,
            'response' => $aiResponse,
            'feature_used' => 'ai_coach',
        ]);

        $plan = $result['plan'] ?? null;

        return response()->json([
            'success' => $result['success'] ?? false,
            'message' => $aiResponse,
            'time' => now()->format('g:i A'),
            'plan' => $plan,
            'plan_html' => $plan
                ? view('workouts.partials.plan-card', [
                    'plan' => $plan,
                    'source' => 'ai_coach',
                    'plan_encoded' => base64_encode(json_encode($plan)),
                ])->render()
                : null,
            'thread' => [
                'title' => Str::limit($userMessage, 34),
                'time' => now()->format('M d'),
                'count' => 1,
            ],
        ]);
    }

    /**
     * Save an inline AI-coach plan card into saved_programs.
     */
    public function savePlan(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'plan_data' => 'required|string',
        ]);

        $payload = trim($request->input('plan_data'));

        $plan = json_decode($payload, true);
        if (!is_array($plan)) {
            $decoded = base64_decode($payload, true);
            $plan = is_string($decoded) ? json_decode($decoded, true) : null;
        }

        if (!is_array($plan)) {
            return response()->json([
                'success' => false,
                'message' => 'The plan payload could not be parsed.',
            ], 422);
        }

        $program = SavedProgram::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'plan_data' => $plan,
            'source' => 'ai_coach',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plan saved to your programs!',
            'program' => [
                'id' => $program->id,
                'title' => $program->title,
            ],
        ]);
    }

    /**
     * Clear the user's entire conversation history.
     */
    public function clear()
    {
        AIConversation::where('user_id', Auth::id())
            ->where('feature_used', 'ai_coach')
            ->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Conversation cleared.']);
        }

        return redirect()->route('ai.coach')->with('success', 'Conversation cleared.');
    }

    /**
     * Build a chat-friendly message list from the database (oldest first).
     */
    private function getConversationHistory()
    {
        return AIConversation::where('user_id', Auth::id())
            ->where('feature_used', 'ai_coach')
            ->orderBy('created_at')
            ->get()
            ->flatMap(function (AIConversation $row) {
                return [
                    [
                        'role' => 'user',
                        'content' => $row->prompt,
                        'time' => $row->created_at->format('g:i A'),
                    ],
                    [
                        'role' => 'assistant',
                        'content' => $row->response,
                        'time' => $row->created_at->format('g:i A'),
                    ],
                ];
            });
    }

    /**
     * Sidebar "conversation threads" – one per day, with the leading prompt as the label.
     */
    private function getThreads()
    {
        return AIConversation::where('user_id', Auth::id())
            ->where('feature_used', 'ai_coach')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($row) => $row->created_at->toDateString())
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'title' => Str::limit($first->prompt, 36),
                    'time' => $first->created_at->format('M j, g:i A'),
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->toArray();
    }

    private function getCoachState(): array
    {
        $user = Auth::user();
        $logs = $user->workoutLogs()->whereNotNull('completed_at')->get();

        if ($logs->isEmpty()) {
            return [
                'recovery' => ['label' => 'Recovery', 'value' => 'HIGH', 'color' => 'text-emerald-400'],
                'fatigue' => ['label' => 'Fatigue index', 'value' => 'LOW', 'color' => 'text-emerald-400'],
                'adaptation' => ['label' => 'Adaptation', 'value' => 'HYPERSTRENGTH', 'color' => 'text-[#3B82F6]'],
                'sessions' => 128,
            ];
        }

        $totalMinutes = (int) $logs->sum('duration');
        $recovery = $totalMinutes / max(1, $logs->count()) < 60 ? 'HIGH' : 'MODERATE';

        return [
            'recovery' => ['label' => 'Recovery', 'value' => $recovery, 'color' => $recovery === 'HIGH' ? 'text-emerald-400' : 'text-amber-400'],
            'fatigue' => ['label' => 'Fatigue index', 'value' => $totalMinutes > 300 ? 'MODERATE' : 'LOW', 'color' => 'text-amber-400'],
            'adaptation' => ['label' => 'Adaptation', 'value' => strtoupper(Str::slug($user->profile->experience_level ?? 'strength')), 'color' => 'text-[#3B82F6]'],
            'sessions' => $logs->count(),
        ];
    }

    /**
     * Build the user profile context passed to the AI.
     */
    private function getUserContext($user): array
    {
        if (!$user->profile) {
            return [];
        }

        return [
            'goal' => $user->profile->fitness_goal,
            'experience' => $user->profile->experience_level,
            'age' => $user->profile->date_of_birth ? \Carbon\Carbon::parse($user->profile->date_of_birth)->age : null,
            'sex' => $user->profile->sex,
            'activity_level' => $user->profile->activity_level,
            'workout_location' => $user->profile->workout_location,
        ];
    }
}