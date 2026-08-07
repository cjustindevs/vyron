@extends('layouts.app')

@section('title', 'Home')

@section('content')

{{-- ============ HERO ============ --}}
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 animate-fade-up">
    <div>
        <p class="text-[13px] text-[#666666] font-medium uppercase tracking-[0.18em] mb-2">
            {{ now()->format('l, F j') }}
        </p>
        <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white leading-tight">
            Welcome back, {{ explode(' ', $user->name)[0] }}
        </h1>
        <p class="text-[#8f8f8f] text-sm mt-2 max-w-xl">
            Your intelligence layer recalibrated overnight. Recovery is high — today is a <span class="text-[#60A5FA] font-semibold">power day</span>.
        </p>
    </div>

    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-2 text-xs font-semibold bg-[#161616] border border-[#2a2a2a] text-[#E5E7EB] px-4 py-2.5 rounded-full">
            <span class="text-orange-400">🔥</span> {{ $stats['streak_days'] }}-day streak
        </span>
        <a href="{{ route('ai.coach') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold btn-primary px-4 py-2.5 rounded-full">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            VYRON Intelligence · Online
        </a>
    </div>
</div>

{{-- ============ AI RECOMMENDATION ============ --}}
@php
    $recPlan = [
        'title' => $recommendation['title'],
        'goal' => 'general_fitness',
        'difficulty' => $recommendation['difficulty'],
        'focus' => $recommendation['title'],
        'duration' => $recommendation['duration'],
        'calories' => $recommendation['calories'],
        'days_per_week' => 3,
        'summary' => $recommendation['reason'],
        'exercises' => collect($recommendation['exercises'])->map(fn ($e) => [
            'name' => preg_split('/\s+—\s+|\s+-\s+/', $e)[0],
            'muscle' => '',
            'sets' => preg_match('/^.*?(\d+)\s*×/', $e, $m) ? (int) $m[1] : 3,
            'reps' => preg_match('/×\s*([\d\s,+-]+)/', $e, $m) ? trim($m[1]) : '10',
            'rest' => '75s',
            'notes' => '',
        ])->toArray(),
        'tips' => ['Warm up 5–7 minutes before the first working set.', 'Stop 2 reps shy of failure on main lifts.'],
    ];
    $recEncoded = base64_encode(json_encode($recPlan));
@endphp

<div class="relative overflow-hidden rounded-3xl border border-[#223049] bg-gradient-to-br from-[#10192e] via-[#0d1526] to-[#0a0f1d] mt-8 animate-fade-up" style="animation-delay:80ms">
    <div class="absolute -top-32 -right-24 w-96 h-96 rounded-full bg-[#3B82F6]/25 blur-[110px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-20 w-80 h-80 rounded-full bg-[#1E90FF]/15 blur-[100px] pointer-events-none"></div>

    <div class="relative p-6 sm:p-9 flex flex-col lg:flex-row lg:items-center gap-8">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-[11px] font-bold tracking-[0.22em] uppercase text-[#60A5FA] bg-[#3B82F6]/10 border border-[#3B82F6]/25 px-3 py-1.5 rounded-full">
                    ✦ AI Recommendation · Today
                </span>
                <span class="text-[11px] font-semibold text-[#8b9dc7] uppercase tracking-wider">Auto-tuned to your recovery</span>
            </div>

            <h2 class="text-2xl sm:text-3xl font-black text-white mt-4 tracking-tight">{{ $recommendation['title'] }}</h2>
            <p class="text-[#9aa7c4] text-sm mt-2 max-w-2xl leading-relaxed">{{ $recommendation['reason'] }}</p>

            <div class="flex flex-wrap gap-x-6 gap-y-2 mt-5 text-[13px] text-[#B3B3B3]">
                <span class="flex items-center gap-1.5"><span class="text-[#60A5FA]">⏱</span> {{ $recommendation['duration'] }} min</span>
                <span class="flex items-center gap-1.5"><span class="text-orange-400">🔥</span> {{ $recommendation['calories'] }} kcal</span>
                <span class="flex items-center gap-1.5"><span class="text-emerald-400">🏋</span> {{ $recommendation['exercise_count'] }} exercises</span>
                <span class="flex items-center gap-1.5"><span class="text-purple-400">📈</span> {{ ucfirst($recommendation['difficulty']) }}</span>
            </div>

            <div class="flex flex-wrap gap-3 mt-7">
                <button onclick="Vyron.saveProgram({{ json_encode($recommendation['title']) }}, {{ json_encode($recEncoded) }}, 'ai_coach')"
                        class="btn-primary px-6 py-3 text-sm inline-flex items-center gap-2">
                    💾 Save to My Programs
                </button>
                <a href="{{ route('ai.coach') }}" class="btn-ghost px-6 py-3 text-sm inline-flex items-center gap-2 hover:text-[#60A5FA]">
                    ✦ Adjust with AI
                </a>
            </div>
        </div>

        {{-- Confidence ring --}}
        <div class="flex-shrink-0 flex flex-col items-center lg:items-end gap-3 lg:pl-10 lg:border-l lg:border-white/10">
            <div class="relative w-28 h-28">
                <svg class="w-28 h-28 -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="#1c2740" stroke-width="8"/>
                    <circle cx="50" cy="50" r="42" fill="none" stroke="url(#recGrad)" stroke-width="8"
                            stroke-linecap="round" stroke-dasharray="264" stroke-dashoffset="{{ 264 * (1 - $recommendation['confidence'] / 100) }}"/>
                    <defs>
                        <linearGradient id="recGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#3B82F6"/>
                            <stop offset="100%" stop-color="#22D3EE"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-black text-white">{{ $recommendation['confidence'] }}%</span>
                    <span class="text-[9px] uppercase tracking-[0.2em] text-[#8b9dc7] mt-0.5">Confidence</span>
                </div>
            </div>
            <a href="{{ route('workouts.generate') }}" class="text-xs text-[#60A5FA] hover:text-white transition font-medium">
                Open in Generator →
            </a>
        </div>
    </div>
</div>

{{-- ============ STATS ============ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8 stagger">
    @php
        $statCards = [
            ['label' => 'Streak', 'value' => $stats['streak_days'], 'unit' => 'days', 'icon' => '🔥', 'accent' => '#F59E0B', 'delta' => 'best all-time', 'up' => true],
            ['label' => 'Weekly Calories', 'value' => number_format($stats['weekly_calories']), 'unit' => 'kcal', 'icon' => '⚡', 'accent' => '#3B82F6', 'delta' => $stats['calories_delta'] . ' this week', 'up' => true],
            ['label' => 'Training Hours', 'value' => $stats['training_hours'], 'unit' => 'hours', 'icon' => '🕒', 'accent' => '#22C55E', 'delta' => $stats['hours_delta'], 'up' => true],
            ['label' => 'Fitness Score', 'value' => number_format($stats['fitness_score']), 'unit' => 'points', 'icon' => '💪', 'accent' => '#8B5CF6', 'delta' => $stats['score_delta'] . ' pts', 'up' => true],
        ];
    @endphp
    @foreach($statCards as $card)
        <div class="card p-5 group relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full opacity-10 group-hover:opacity-20 transition" style="background: {{ $card['accent'] }}"></div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7a7a]">{{ $card['label'] }}</span>
                <span class="text-base opacity-80">{{ $card['icon'] }}</span>
            </div>
            <p class="text-3xl font-black text-white mt-2.5 tracking-tight">
                {{ $card['value'] }} <span class="text-xs font-semibold text-[#666666]">{{ $card['unit'] }}</span>
            </p>
            <p class="text-xs mt-2 font-medium {{ $card['up'] ? 'text-emerald-400' : 'text-[#8a8a8a]' }}">{{ $card['delta'] }}</p>
            <div class="mt-4 h-1 rounded-full bg-[#222222] overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700 group-hover:brightness-125" style="width: 70%; background: linear-gradient(90deg, {{ $card['accent'] }}, transparent)"></div>
            </div>
        </div>
    @endforeach
</div>

{{-- ============ CALENDAR + UPCOMING ============ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
    {{-- Monthly training calendar --}}
    <div class="lg:col-span-2 card p-6 sm:p-7">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-5 h-5 text-[#3B82F6]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                </svg>
                Monthly Training Calendar
            </h3>
            <span class="text-sm font-semibold text-[#B3B3B3] bg-[#1c1c1c] px-3.5 py-1.5 rounded-full border border-[#2a2a2a]">
                {{ $calendar['month'] }} {{ $calendar['year'] }}
            </span>
        </div>

        <div class="grid grid-cols-7 gap-1.5 sm:gap-2 text-center text-[11px] font-semibold mb-2">
            @foreach(['Mo','Tu','We','Th','Fr','Sa','Su'] as $d)
                <div class="text-[#555555] uppercase tracking-wider py-1">{{ $d }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
            @foreach($calendar['cells'] as $cell)
                @if($cell['day'] === null)
                    <div></div>
                @else
                    <div class="relative aspect-square flex items-center justify-center rounded-xl text-[13px] font-semibold transition-all duration-200
                                {{ $cell['today'] ? 'bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] text-white shadow-lg shadow-[#3B82F6]/30 scale-105' : ($cell['workout'] ? 'bg-[#3B82F6]/15 text-[#60A5FA] border border-[#3B82F6]/30 hover:bg-[#3B82F6]/25 cursor-default' : 'text-[#6f6f6f] hover:bg-[#1d1d1d] cursor-default') }}">
                        {{ $cell['day'] }}
                        @if($cell['workout'] && !$cell['today'])
                            <span class="absolute bottom-1 w-1 h-1 rounded-full bg-[#3B82F6]"></span>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <div class="flex items-center justify-between mt-6 pt-5 border-t border-[#1e1e1e]">
            <p class="text-sm text-[#8a8a8a]">
                📊 <span class="text-white font-semibold">{{ $stats['sessions_this_month'] }}</span> sessions logged this month
            </p>
            <div class="flex items-center gap-4 text-[11px] text-[#6f6f6f]">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#3B82F6]/60"></span> Workout</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#3B82F6]"></span> Today</span>
            </div>
        </div>
    </div>

    {{-- Upcoming --}}
    <div class="card p-6 sm:p-7">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-white">Up Next</h3>
            <span class="text-[11px] font-semibold text-[#3B82F6] uppercase tracking-wider">Scheduled</span>
        </div>
        <div class="space-y-3.5">
            @foreach($upcoming as $workout)
                <div class="rounded-2xl border p-4 transition-all duration-200
                            {{ $workout['active'] ? 'border-[#3B82F6]/35 bg-[#3B82F6]/[0.06]' : 'border-[#242424] bg-[#1a1a1a] hover:border-[#3B82F6]/30' }}">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-bold text-white text-sm">{{ $workout['title'] }}</p>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full shrink-0
                                    {{ $workout['active'] ? 'bg-[#3B82F6]/15 text-[#60A5FA] border border-[#3B82F6]/30' : 'bg-[#222222] text-[#7a7a7a] border border-[#2c2c2c]' }}">
                            {{ $workout['day'] }}
                        </span>
                    </div>
                    <p class="text-xs text-[#6f6f6f] mt-1.5">⏰ {{ $workout['time'] }}</p>
                    @if(!empty($workout['exercises']))
                        <ul class="mt-3 space-y-1.5 text-xs text-[#a3a3a3]">
                            @foreach($workout['exercises'] as $exercise)
                                <li class="flex items-center gap-2">
                                    <span class="timeline-dot"></span>{{ $exercise }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============ ACHIEVEMENTS + AI INSIGHT ============ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
    {{-- Achievements --}}
    <div class="card p-6 sm:p-7">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2.5">
                <span class="text-xl">🏆</span> Achievements
            </h3>
            <span class="text-[11px] text-[#6f6f6f] font-semibold uppercase tracking-wider">{{ count(array_filter($achievements, fn ($a) => $a['unlocked'])) }}/{{ count($achievements) }} unlocked</span>
        </div>
        <div class="space-y-4">
            @foreach($achievements as $achievement)
                <div class="flex items-center gap-4 group">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 border transition-all duration-200 group-hover:scale-105"
                         style="background: {{ $achievement['color'] }}14; border-color: {{ $achievement['unlocked'] ? $achievement['color'] . '44' : '#2a2a2a' }};">
                        {{ $achievement['icon'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white text-sm">{{ $achievement['title'] }}</p>
                        <p class="text-xs text-[#7a7a7a] mt-0.5 truncate">{{ $achievement['subtitle'] }}</p>
                    </div>
                    @if($achievement['unlocked'])
                        <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-2.5 py-1 rounded-full">UNLOCKED</span>
                    @else
                        <span class="text-[10px] font-bold text-[#6f6f6f] bg-[#1c1c1c] border border-[#2a2a2a] px-2.5 py-1 rounded-full">LOCKED</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- AI Insight --}}
    <div class="relative overflow-hidden rounded-[1.25rem] border border-[#222222] p-6 sm:p-7 bg-gradient-to-br from-[#101318] via-[#0d0f14] to-[#0a0b0f]">
        <div class="absolute -top-20 -right-16 w-56 h-56 rounded-full bg-[#3B82F6]/15 blur-[90px] pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-[#3B82F6]/15 border border-[#3B82F6]/30 flex items-center justify-center text-[15px]">🧠</span>
                    AI Insight
                </h3>
                <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live
                </span>
            </div>

            <p class="text-[#c4c4c4] text-sm leading-relaxed mt-4">
                {{ $insight['text'] }}
            </p>

            <div class="flex items-center gap-3 mt-6 pt-5 border-t border-white/5">
                <div class="flex -space-x-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-[9px] font-black text-white border border-white/10">AI</div>
                    <div class="w-7 h-7 rounded-full bg-[#222222] flex items-center justify-center text-[9px] font-black text-white border border-white/10">VR</div>
                </div>
                <a href="{{ route('ai.coach') }}" class="text-sm font-semibold text-[#60A5FA] hover:text-white transition">
                    Discuss with AI Coach →
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ============ RECENT PROGRAMS ============ --}}
@if($recentPrograms->isNotEmpty())
    <div class="mt-8 animate-fade-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">Recently Saved Programs</h3>
            <a href="{{ route('programs.index') }}" class="text-sm font-semibold text-[#60A5FA] hover:text-white transition">View all →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($recentPrograms as $program)
                <a href="{{ route('programs.index') }}" class="card p-5 group block">
                    <div class="flex items-center justify-between">
                        <span class="w-9 h-9 rounded-xl bg-[#3B82F6]/12 border border-[#3B82F6]/25 flex items-center justify-center text-base group-hover:scale-110 transition">💾</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#6f6f6f]">{{ $program->source === 'ai_coach' ? 'AI Coach' : 'Generator' }}</span>
                    </div>
                    <p class="font-bold text-white text-sm mt-3.5 leading-snug">{{ $program->title }}</p>
                    <p class="text-xs text-[#6f6f6f] mt-1.5">{{ $program->created_at->format('M j, Y') }} · {{ count($program->plan_data['exercises'] ?? []) }} exercises</p>
                </a>
            @endforeach
        </div>
    </div>
@endif

@endsection
