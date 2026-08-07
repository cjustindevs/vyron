<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\SavedProgram;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorkoutSessionController extends Controller
{
    /**
     * Session key used to hold in-progress workout state (live sets).
     */
    protected function sessionKey(int $programId): string
    {
        return "vyron_workout_session_{$programId}";
    }

    /**
     * Parse a prescribed rest string ("90s", "2 min", "1.5min", "60 sec") into seconds.
     */
    private function restToSeconds(?string $rest): int
    {
        $rest = trim((string) $rest);

        if (preg_match('/^(\d+(?:\.\d+)?)\s*min(?:ute)?s?$/i', $rest, $m)) {
            return (int) round((float) $m[1] * 60);
        }

        if (preg_match('/^(\d+)\s*s(?:ec(?:ond)?s?)?$/i', $rest, $m)) {
            return (int) $m[1];
        }

        return 90;
    }

    /**
     * Show the live workout session for a saved program.
     */
    public function start(Request $request, SavedProgram $program): View|RedirectResponse
    {
        if ((int) $program->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $plan = $program->plan_data ?? [];
        $rawExercises = collect($plan['exercises'] ?? [])->values();

        if ($rawExercises->isEmpty()) {
            return redirect()->route('programs.index')
                ->with('error', 'This program has no exercises to run.');
        }

        $exercises = $rawExercises->map(function ($exercise, $index) {
            return [
                'index' => $index,
                'name' => $exercise['name'] ?? 'Exercise '.($index + 1),
                'muscle' => $exercise['muscle'] ?? null,
                'sets' => max(1, (int) ($exercise['sets'] ?? 1)),
                'reps' => (string) ($exercise['reps'] ?? '10'),
                'rest' => (string) ($exercise['rest'] ?? ''),
                'rest_seconds' => $this->restToSeconds($exercise['rest'] ?? null),
                'notes' => $exercise['notes'] ?? null,
            ];
        })->all();

        $key = $this->sessionKey($program->id);
        $session = $request->session()->get($key, null);

        if (!is_array($session)) {
            $session = [
                'program_id' => $program->id,
                'started_at' => now()->timestamp,
                'sets' => [],
            ];
            $request->session()->put($key, $session);
        }

        return view('workout-session.index', [
            'program' => $program,
            'plan' => $plan,
            'exercises' => $exercises,
            'startedAt' => $session['started_at'] ?? now()->timestamp,
            'loggedSets' => $session['sets'] ?? [],
        ]);
    }

    /**
     * AJAX endpoint – save a completed set into the live session.
     */
    public function logSet(Request $request)
    {
        $validated = $request->validate([
            'exercise_index' => 'required|integer|min:0',
            'set_number' => 'required|integer|min:1',
            'weight' => 'required|numeric|min:0|max:1000',
            'reps' => 'required|integer|min:1|max:100',
            'rest_time' => 'required|integer|min:0|max:3600',
        ]);

        $programId = (int) $request->input('program_id');
        $program = SavedProgram::find($programId);

        if (!$program || (int) $program->user_id !== (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found or session expired. Restart the workout.',
            ], 404);
        }

        $plan = $program->plan_data ?? [];
        $exercises = collect($plan['exercises'] ?? [])->values();
        $index = $validated['exercise_index'];

        if (!isset($exercises[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'That exercise no longer exists in this program.',
            ], 422);
        }

        $key = $this->sessionKey($program->id);
        $session = $request->session()->get($key, null);

        if (!is_array($session)) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please restart the workout.',
            ], 409);
        }

        $prescribed = max(1, (int) ($exercises[$index]['sets'] ?? 1));

        if ($validated['set_number'] > $prescribed) {
            return response()->json([
                'success' => false,
                'message' => "This exercise only prescribes {$prescribed} sets.",
            ], 422);
        }

        $session['sets'][$index] = $session['sets'][$index] ?? [];
        $session['sets'][$index][$validated['set_number'] - 1] = [
            'weight' => (float) $validated['weight'],
            'reps' => (int) $validated['reps'],
            'rest' => (int) $validated['rest_time'],
        ];

        $request->session()->put($key, $session);

        $loggedCount = (int) count($session['sets'][$index]);
        $totalSets = (int) collect($exercises)->sum(fn ($e) => max(1, (int) ($e['sets'] ?? 1)));

        $doneSets = 0;
        foreach ($exercises as $i => $e) {
            $doneSets += min(
                count($session['sets'][$i] ?? []),
                max(1, (int) ($e['sets'] ?? 1))
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Set {$validated['set_number']} logged — {$validated['weight']} kg × {$validated['reps']} reps.",
            'exercise_index' => (int) $validated['exercise_index'],
            'set_number' => (int) $validated['set_number'],
            'logged_count' => $loggedCount,
            'prescribed' => $prescribed,
            'exercise_complete' => $loggedCount >= $prescribed,
            'done_sets' => $doneSets,
            'total_sets' => max(1, $totalSets),
            'all_done' => $doneSets >= $totalSets,
        ]);
    }

    /**
     * Finish the workout – persist the live session into workout_logs
     * and workout_log_details, then clear the session.
     */
    public function complete(Request $request, SavedProgram $program): RedirectResponse
    {
        if ((int) $program->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'duration' => 'nullable|integer|min:0|max:86400',
            'notes' => 'nullable|string|max:500',
        ]);

        $key = $this->sessionKey($program->id);
        $session = $request->session()->get($key, null);

        if (!is_array($session)) {
            return redirect()->route('programs.index')
                ->with('error', 'No active workout session was found.');
        }

        $plan = $program->plan_data ?? [];
        $exercises = collect($plan['exercises'] ?? [])->values()->map(function (array $e, $i) {
            return [
                'index' => $i,
                'name' => (string) ($e['name'] ?? 'Exercise '.($i + 1)),
                'muscle' => (string) ($e['muscle'] ?? 'General'),
                'sets' => max(1, (int) ($e['sets'] ?? 1)),
            ];
        });

        $loggedSets = $session['sets'] ?? [];

        $incomplete = $exercises->filter(fn ($e) => empty($loggedSets[$e['index']]));

        if ($incomplete->isNotEmpty()) {
            $names = $incomplete->pluck('name')->take(3)->implode(', ');

            return back()
                ->with('error', 'Finish every exercise first — you still have sets left to log for: '.$names.'.');
        }

        $duration = $validated['duration']
            ?? max(0, now()->timestamp - (int) ($session['started_at'] ?? now()->timestamp));

        $log = WorkoutLog::create([
            'user_id' => Auth::id(),
            'workout_plan_id' => null,
            'completed_at' => now(),
            'duration' => (int) $duration,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($exercises as $e) {
            $sets = array_values($loggedSets[$e['index']]);

            $exercise = Exercise::where('exercise_name', $e['name'])->first();

            if (!$exercise) {
                $exercise = Exercise::create([
                    'exercise_name' => $e['name'],
                    'muscle_group' => $e['muscle'] ?: 'General',
                    'equipment' => null,
                    'difficulty' => in_array($plan['difficulty'] ?? '', ['beginner', 'intermediate', 'advanced'])
                        ? $plan['difficulty']
                        : 'intermediate',
                    'instructions' => null,
                    'image_url' => null,
                ]);
            }

            WorkoutLogDetail::create([
                'workout_log_id' => $log->id,
                'exercise_id' => $exercise->id,
                'sets_completed' => count($sets),
                'repetitions_completed' => (int) collect($sets)->sum('reps'),
                'weight_used' => round((float) collect($sets)->avg('weight'), 2),
                'rest_time' => (int) round((float) collect($sets)->avg('rest')),
            ]);
        }

        $request->session()->forget($key);

        return redirect()->route('programs.index')
            ->with('success', "Workout completed 🎉 — {$exercises->count()} exercises, "
                .$this->formatDuration((int) $duration).' logged to your Workout Log.');
    }

    /**
     * Format a seconds duration as e.g. "42m 10s" or "57s".
     */
    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds.'s';
        }

        return floor($seconds / 60).'m '.($seconds % 60).'s';
    }
}