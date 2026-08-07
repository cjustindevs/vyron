<?php

namespace App\Http\Controllers;

use App\Models\WorkoutLog;
use App\Models\WorkoutLogDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorkoutLogController extends Controller
{
    /**
     * Workout Logging – history of completed sessions.
     */
    public function index(): View
    {
        $logs = WorkoutLog::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->with('workoutPlan')
            ->limit(30)
            ->get();

        $logs = $logs->map(function (WorkoutLog $log) {
            $details = WorkoutLogDetail::where('workout_log_id', $log->id)
                ->with('exercise')
                ->get();

            $volume = (int) $details->sum(fn ($d) => (float) ($d->weight_used ?? 0)
                * (int) ($d->sets_completed ?? 0)
                * (int) ($d->repetitions_completed ?? 0));

            $sets = (int) $details->sum('sets_completed');
            $reps = (int) $details->sum('repetitions_completed');

            return [
                'id' => $log->id,
                'completed_at' => Carbon::parse($log->completed_at)->format('D, M j · g:i A'),
                'date_key' => Carbon::parse($log->completed_at)->toDateString(),
                'duration' => $log->duration,
                'notes' => $log->notes,
                'plan_title' => $log->workoutPlan?->title,
                'volume' => $volume,
                'sets' => $sets,
                'reps' => $reps,
                'exercises' => $details->map(fn ($d) => [
                    'name' => $d->exercise?->exercise_name ?? $d->exercise_id,
                    'muscle' => $d->exercise?->muscle_group,
                    'sets' => $d->sets_completed,
                    'reps' => $d->repetitions_completed,
                    'weight' => $d->weight_used,
                ])->values()->toArray(),
            ];
        });

        $totals = [
            'sessions' => $logs->count(),
            'volume' => (int) $logs->sum('volume'),
            'sets' => $logs->sum('sets'),
            'reps' => $logs->sum('reps'),
        ];

        return view('logs.index', compact('logs', 'totals'));
    }
}