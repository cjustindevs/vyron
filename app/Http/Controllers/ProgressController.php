<?php

namespace App\Http\Controllers;

use App\Models\ProgressRecord;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProgressController extends Controller
{
    /**
     * Progress Tracker – charts + metrics for the current user.
     */
    public function index(Request $request): View
    {
        $period = in_array($request->query('period'), ['week', 'month', 'year'], true)
            ? $request->query('period')
            : 'month';

        $since = match ($period) {
            'week' => Carbon::now()->subWeek()->startOfDay(),
            'year' => Carbon::now()->subYear()->startOfDay(),
            default => Carbon::now()->subMonth()->startOfDay(),
        };

        $user = Auth::user();

        $logs = WorkoutLog::where('user_id', $user->id)
            ->where(function ($q) use ($since) {
                $q->where('completed_at', '>=', $since)
                    ->orWhereNull('completed_at');
            })
            ->orderBy('completed_at')
            ->get();

        $details = WorkoutLogDetail::whereHas('workoutLog', function ($q) use ($user, $since) {
            $q->where('user_id', $user->id)
                ->where(function ($w) use ($since) {
                    $w->where('completed_at', '>=', $since)
                        ->orWhereNull('completed_at');
                });
        })
            ->with(['exercise', 'workoutLog'])
            ->get();

        $records = ProgressRecord::where('user_id', $user->id)
            ->where('recorded_at', '>=', $since->copy()->startOfYear())
            ->orderBy('recorded_at')
            ->get();

        $weightTrend = $this->weightTrend($records);
        $frequency = $this->workoutFrequency($logs, $period);
        $volumeTrend = $this->volumeTrend($logs, $details, $period);
        $muscleDistribution = $this->muscleDistribution($details);
        $bestLifts = $this->bestLifts($details);
        $totalLiftVolume = $this->totalLiftVolume($logs, $details);

        // ---------- Metrics ----------
        $latestRecord = $records->last();
        $currentWeight = $latestRecord?->weight ?? $user->profile?->weight ?? null;
        $startingWeight = $records->first()?->weight ?? $currentWeight;
        $weightDelta = ($currentWeight !== null && $startingWeight !== null)
            ? round($currentWeight - $startingWeight, 1)
            : null;

        $totalWorkouts = $logs->whereNotNull('completed_at')->count();
        $totalMinutes = (int) $logs->sum('duration');

        return view('progress.index', compact(
            'period',
            'weightTrend',
            'frequency',
            'volumeTrend',
            'muscleDistribution',
            'bestLifts',
            'currentWeight',
            'startingWeight',
            'weightDelta',
            'totalWorkouts',
            'totalMinutes',
            'totalLiftVolume'
        ));
    }

    // ============================================================
    // CHART BUILDERS
    // ============================================================

    private function weightTrend($records): array
    {
        return [
            'labels' => $records->map(fn ($r) => Carbon::parse($r->recorded_at)->format('M j'))->values()->toArray(),
            'values' => $records->map(fn ($r) => (float) ($r->weight ?? 0))->values()->toArray(),
        ];
    }

    private function workoutFrequency($logs, string $period): array
    {
        $batches = $logs->groupBy(function ($log) use ($period) {
            $date = Carbon::parse($log->completed_at ?? $log->created_at);

            return match ($period) {
                'week' => $date->format('D'),
                'year' => $date->format('M'),
                default => 'W' . $date->isoWeek(),
            };
        });

        $labels = $batches->keys()->map(fn ($k) => (string) $k)->values()->toArray();
        $counts = $batches->map->count()->values()->toArray();

        if ($period === 'month' && count($labels) > 10) {
            $labels = array_slice($labels, -10);
            $counts = array_slice($counts, -10);
        }

        return ['labels' => $labels, 'values' => $counts];
    }

    private function volumeTrend($logs, $details, string $period): array
    {
        $bucketOf = function ($date) use ($period) {
            return match ($period) {
                'week' => $date->format('D'),
                'year' => $date->format('M'),
                default => 'W' . $date->isoWeek(),
            };
        };

        $map = [];
        foreach ($logs as $log) {
            $bucket = $bucketOf(Carbon::parse($log->completed_at ?? $log->created_at));
            $map[$bucket] ??= 0;
        }

        foreach ($details as $detail) {
            $log = $detail->workoutLog;
            if (! $log) {
                continue;
            }
            $bucket = $bucketOf(Carbon::parse($log->completed_at ?? $log->created_at));
            $map[$bucket] = ($map[$bucket] ?? 0) + $this->volumeOf($detail);
        }

        $labels = array_keys($map);
        $values = array_values($map);

        if ($period === 'month' && count($labels) > 10) {
            $labels = array_slice($labels, -10);
            $values = array_slice($values, -10);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function muscleDistribution($details): array
    {
        $groups = [];
        foreach ($details as $detail) {
            $group = $detail->exercise?->muscle_group ?? 'Other';
            $groups[$group] = ($groups[$group] ?? 0) + $this->volumeOf($detail);
        }

        arsort($groups);

        return [
            'labels' => array_keys($groups),
            'values' => array_values($groups),
        ];
    }

    private function bestLifts($details): array
    {
        $best = [];
        foreach ($details as $detail) {
            $weight = (float) ($detail->weight_used ?? 0);
            if ($weight <= 0) {
                continue;
            }

            $exercise = $detail->exercise?->exercise_name ?? 'Unknown';
            if (! isset($best[$exercise]) || $weight > $best[$exercise]['weight']) {
                $best[$exercise] = [
                    'weight' => round($weight, 1),
                    'reps' => (int) ($detail->repetitions_completed ?? 0),
                    'muscle' => $detail->exercise?->muscle_group ?? 'Other',
                    'date' => isset($detail->workoutLog?->completed_at)
                        ? Carbon::parse($detail->workoutLog->completed_at)->format('M j, Y')
                        : null,
                ];
            }
        }

        uasort($best, fn ($a, $b) => $b['weight'] <=> $a['weight']);

        return array_slice($best, 0, 8);
    }

    private function totalLiftVolume($logs, $details): int
    {
        return (int) $details->sum(fn ($d) => $this->volumeOf($d));
    }

    private function volumeOf(WorkoutLogDetail $detail): float
    {
        return (float) ((float) ($detail->weight_used ?? 0)
            * (int) ($detail->sets_completed ?? 0)
            * (int) ($detail->repetitions_completed ?? 0));
    }
}