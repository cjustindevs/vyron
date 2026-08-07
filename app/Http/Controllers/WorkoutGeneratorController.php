<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutGenerationRequest;
use App\Models\SavedProgram;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutGeneratorController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show the workout generator form.
     */
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('workouts.generate', [
            'profile' => $profile,
            'plan' => session('plan'),
        ]);
    }

    /**
     * Generate a workout plan using AI (AJAX, returns JSON).
     */
    public function generate(WorkoutGenerationRequest $request)
    {
        $validated = $request->validated();

        $profile = [
            'goal' => $validated['goal'] ?? 'general_fitness',
            'age' => $validated['age'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'experience_level' => $validated['experience_level'] ?? 'intermediate',
            'activity_level' => $validated['activity_level'] ?? 'moderate',
            'workout_location' => $validated['workout_location'] ?? 'gym',
            'target_muscle' => $request->input('target_muscle', 'full_body'),
            'difficulty' => $request->input('difficulty', 'moderate'),
            'equipment' => $validated['equipment'] ?? [],
        ];

        $preferences = [
            'duration' => (int) $validated['duration'],
            'days_per_week' => (int) $validated['days_per_week'],
        ];

        $result = $this->aiService->generateWorkoutPlan($profile, $preferences);

        // Keep a copy in the session for non-AJAX / page reload fallback.
        if ($request->wantsJson() || $request->expectsJson()) {
            $plan = $result['plan'] ?? null;

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success']
                    ? 'Your personalized workout plan is ready!'
                    : ($result['error'] ?? 'Failed to generate workout plan.'),
                'plan' => $plan,
                'plan_html' => $plan
                    ? view('workouts.partials.plan-card', [
                        'plan' => $plan,
                        'source' => 'workout_generator',
                        'plan_encoded' => base64_encode(json_encode($plan)),
                    ])->render()
                    : null,
                'raw' => $result['text'],
            ], $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return back()
                ->withInput()
                ->with('error', $result['error'] ?? 'Failed to generate workout plan. Please try again.');
        }

        session()->flash('plan', $result['text']);

        return redirect()->route('workouts.generate')
            ->with('success', 'Your personalized workout plan has been generated!');
    }

    /**
     * Save a generated plan into the saved_programs table.
     */
    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'plan_data' => 'required|string',
            'source' => 'nullable|string|in:workout_generator,ai_coach',
        ]);

        // plan_data may arrive as raw JSON string or base64-encoded JSON.
        $payload = trim($request->input('plan_data'));

        $plan = json_decode($payload, true);
        if (!is_array($plan)) {
            $decoded = base64_decode($payload, true);
            $plan = is_string($decoded) ? json_decode($decoded, true) : null;
        }

        if (!is_array($plan)) {
            return response()->json([
                'success' => false,
                'message' => 'The plan payload could not be parsed. Please regenerate.',
            ], 422);
        }

        $program = SavedProgram::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'plan_data' => $plan,
            'source' => $request->input('source', 'workout_generator'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program saved to your library!',
            'program' => [
                'id' => $program->id,
                'title' => $program->title,
                'created_at' => $program->created_at->diffForHumans(),
            ],
        ]);
    }
}