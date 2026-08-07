<?php

namespace App\Http\Controllers;

use App\Models\SavedProgram;
use App\Models\WorkoutLog;
use App\Services\AIService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Home / Dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $logs = WorkoutLog::where('user_id', $user->id)
            ->orderBy('completed_at')
            ->get();

        $stats = $this->buildStats($logs);
        $recommendation = $this->buildRecommendation($user, $logs);
        $calendar = $this->buildCalendar($logs);
        $upcoming = $this->buildUpcoming();
        $achievements = $this->buildAchievements($logs);
        $insight = $this->getInsight($stats);
        $recentPrograms = SavedProgram::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard', compact(
            'user',
            'stats',
            'recommendation',
            'calendar',
            'upcoming',
            'achievements',
            'insight',
            'recentPrograms'
        ));
    }

    // ============================================================
    // DATA BUILDERS (placeholder-safe but ready for real data)
    // ============================================================

    private function buildStats($logs): array
    {
        $hasLogs = $logs->count() > 0;

        // Real values when available, sensible defaults otherwise.
        $streakDays = $hasLogs ? $this->computeStreak($logs) : 24;
        $totalMinutes = (int) $logs->sum('duration');
        $trainingHours = $hasLogs ? round($totalMinutes / 60, 1) : 6.4;
        $weeklyCalories = $hasLogs ? (int) round($totalMinutes * 4.6) : 3862;
        $fitnessScore = $hasLogs
            ? min(1000, 600 + ($streakDays * 6) + ($logs->count() * 4))
            : 847;

        return [
            'streak_days' => $streakDays,
            'weekly_calories' => $weeklyCalories,
            'calories_delta' => '+12%',
            'training_hours' => $trainingHours,
            'hours_delta' => '+0.8h',
            'fitness_score' => $fitnessScore,
            'score_delta' => '+18',
            'sessions_this_month' => $this->sessionsThisMonth($logs),
            'has_logs' => $hasLogs,
        ];
    }

    private function computeStreak($logs): int
    {
        $dates = $logs->pluck('completed_at')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->sortDesc()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        // Count consecutive days ending at the most recent workout (or today).
        $cursor = Carbon::parse($dates->first());
        $streak = 0;
        $dateSet = $dates->flip();

        while ($dateSet->has($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    private function sessionsThisMonth($logs): int
    {
        return $logs->filter(fn ($log) => Carbon::parse($log->completed_at)->isCurrentMonth())->count();
    }

    private function buildRecommendation($user, $logs): array
    {
        $goal = $user->profile->fitness_goal ?? null;
        $experience = $user->profile->experience_level ?? 'intermediate';

        $title = match ($goal) {
            'muscle_gain' => 'Push · Pull · Legs — Hypertrophy',
            'weight_loss' => 'Metabolic Full-Body Circuit',
            'strength' => 'Strength Focus · Squat Day',
            'endurance' => 'Aerobic + Resistance Combo',
            default => 'Upper Body Power · Push Focus',
        };

        return [
            'title' => $title,
            'duration' => 52,
            'calories' => 480,
            'exercise_count' => 6,
            'difficulty' => $experience,
            'confidence' => 96 + (int) $logs->count() % 4, // deterministic 96–99
            'reason' => 'Your training load is balanced and recovery is high. Today is a great day to push intensity — hit the main lifts first, then cap the session with two pump movements.',
            'exercises' => $this->recommendationExercises($title),
        ];
    }

    private function recommendationExercises(string $title): array
    {
        $sets = [
            'Upper Body Power · Push Focus' => ['Barbell Bench Press — 4 × 6', 'Incline Dumbbell Press — 3 × 8', 'Weighted Dip — 3 × 10', 'Lateral Raise — 3 × 15', 'Rope Pushdown — 3 × 12', 'Face Pull — 3 × 15'],
            'Push · Pull · Legs — Hypertrophy' => ['Barbell Bench Press — 4 × 8', 'Pull-Up — 4 × 8', 'Back Squat — 4 × 10', 'Seated Row — 3 × 10', 'Romanian Deadlift — 3 × 10', 'Overhead Press — 3 × 8'],
            'Metabolic Full-Body Circuit' => ['Goblet Squat — 4 × 12', 'Kettlebell Swing — 4 × 15', 'Push-Up — 4 × 15', "Farmer's Carry — 4 × 40m", 'Plank — 3 × 60s', 'Row Machine — 3 × 500m'],
            'Strength Focus · Squat Day' => ['Back Squat — 5 × 5', 'Front Squat — 3 × 6', 'Romanian Deadlift — 3 × 8', 'Leg Press — 3 × 10', 'Walking Lunge — 3 × 10', 'Calf Raise — 4 × 12'],
            'Aerobic + Resistance Combo' => ['Treadmill Intervals — 4 × 3 min', 'Goblet Squat — 3 × 12', 'Push-Up — 3 × 15', 'Dumbbell Row — 3 × 12', 'Kettlebell Swing — 3 × 15', 'Cooldown Walk — 5 min'],
        ];

        return $sets[$title] ?? $sets['Upper Body Power · Push Focus'];
    }

    private function buildCalendar($logs): array
    {
        $now = Carbon::now();
        $monthName = $now->format('F');
        $year = $now->format('Y');
        $daysInMonth = $now->daysInMonth;

        $workoutDays = $logs->filter(fn ($log) => Carbon::parse($log->completed_at)->isCurrentMonth())
            ->pluck('completed_at')
            ->map(fn ($d) => (int) Carbon::parse($d)->day)
            ->unique()
            ->values();

        // If we have no real data yet, use a believable training pattern.
        if ($workoutDays->isEmpty()) {
            $workoutDays = collect([1, 3, 5, 8, 10, 12, 15, 17, 18, 19, 22, 24, 26, 28, 31]);
        }

        $leading = (Carbon::createFromDate($year, $now->month, 1))->dayOfWeek === 0
            ? 6
            : Carbon::createFromDate($year, $now->month, 1)->dayOfWeek - 1; // Monday-first

        $cells = [];
        for ($i = 0; $i < $leading; $i++) {
            $cells[] = ['day' => null, 'workout' => false, 'today' => false];
        }
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $cells[] = [
                'day' => $day,
                'workout' => $workoutDays->contains($day),
                'today' => $day === (int) $now->day,
            ];
        }

        return [
            'month' => $monthName,
            'year' => $year,
            'cells' => $cells,
        ];
    }

    private function buildUpcoming(): array
    {
        return [
            [
                'title' => 'Lower Hypertrophy',
                'time' => '07:30 – 08:30',
                'day' => 'Today',
                'exercises' => ['Back Squat — 4 × 10', 'Romanian Deadlift — 3 × 10', 'Walking Lunges — 2 × 10'],
                'active' => true,
            ],
            [
                'title' => 'Push Power',
                'time' => '08:00 – 09:00',
                'day' => 'Tomorrow',
                'exercises' => ['Barbell Bench Press — 4 × 6', 'Incline Press — 3 × 8', 'Overhead Press — 3 × 8'],
                'active' => false,
            ],
            [
                'title' => 'Pull & Rear Delts',
                'time' => '18:00 – 19:00',
                'day' => 'Wed',
                'exercises' => ['Weighted Pull-Up — 4 × 8', 'Seated Row — 3 × 10', 'Face Pull — 3 × 15'],
                'active' => false,
            ],
        ];
    }

    private function buildAchievements($logs): array
    {
        $streak = $this->computeStreak($logs);
        $sessions = $logs->count();

        $definitions = [
            [
                'icon' => '🔥',
                'title' => 'Ion Consistency',
                'subtitle' => $logs->isEmpty() ? '24-day streak sample' : "{$streak}-day streak unlocked",
                'color' => '#3B82F6',
                'unlocked' => $streak >= 24 || $logs->isEmpty(),
            ],
            [
                'icon' => '📈',
                'title' => 'Volume Boost',
                'subtitle' => $logs->isEmpty() ? '18-week high · 3× month average' : "{$sessions} sessions logged · keep stacking",
                'color' => '#22C55E',
                'unlocked' => $sessions >= 18 || $logs->isEmpty(),
            ],
            [
                'icon' => '🌅',
                'title' => 'Early Riser',
                'subtitle' => $logs->isEmpty() ? '12 sessions before 6am' : 'Train before 6am to unlock',
                'color' => '#F59E0B',
                'unlocked' => $logs->isEmpty(),
            ],
            [
                'icon' => '🛡️',
                'title' => 'Iron Wall',
                'subtitle' => $logs->isEmpty() ? '10 injury-free weeks' : 'Consistency builds armor',
                'color' => '#8B5CF6',
                'unlocked' => $logs->isEmpty(),
            ],
        ];

        return $definitions;
    }

    private function getInsight(array $stats): array
    {
        $user = Auth::user();

        $context = [
            'member' => $user->name,
            'goal' => $user->profile->fitness_goal ?? 'general fitness',
            'experience' => $user->profile->experience_level ?? 'intermediate',
            'streak_days' => $stats['streak_days'],
            'weekly_calories' => $stats['weekly_calories'],
            'training_hours' => $stats['training_hours'],
            'fitness_score' => $stats['fitness_score'],
            'sessions_this_month' => $stats['sessions_this_month'],
        ];

        $cacheKey = 'vyron.insight.' . $user->id;

        try {
            $result = $this->aiService->generateDailyInsight($context);
            if ($result['success']) {
                Cache::put($cacheKey, $result['text'], now()->addMinutes(30));

                return ['text' => $result['text'], 'from_ai' => true];
            }

            if (Cache::has($cacheKey)) {
                return ['text' => Cache::get($cacheKey), 'from_ai' => true];
            }
        } catch (\Throwable $e) {
            // fall through to the offline insight
        }

        return [
            'text' => 'Your pull volume is tracking about 22% behind push volume. Adding one rowing movement per week should rebalance shoulder health within 3 weeks.',
            'from_ai' => false,
        ];
    }
}
