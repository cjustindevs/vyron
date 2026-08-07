@extends('layouts.app')

@section('title', 'My Programs')

@section('content')

@php
    $muscleColors = [
        'chest' => ['text' => 'text-cyan-300', 'border' => 'border-cyan-500/25', 'bg' => 'bg-cyan-500/10'],
        'back' => ['text' => 'text-blue-300', 'border' => 'border-blue-500/25', 'bg' => 'bg-blue-500/10'],
        'shoulders' => ['text' => 'text-violet-300', 'border' => 'border-violet-500/25', 'bg' => 'bg-violet-500/10'],
        'legs' => ['text' => 'text-emerald-300', 'border' => 'border-emerald-500/25', 'bg' => 'bg-emerald-500/10'],
        'biceps' => ['text' => 'text-amber-300', 'border' => 'border-amber-500/25', 'bg' => 'bg-amber-500/10'],
        'triceps' => ['text' => 'text-orange-300', 'border' => 'border-orange-500/25', 'bg' => 'bg-orange-500/10'],
        'core' => ['text' => 'text-fuchsia-300', 'border' => 'border-fuchsia-500/25', 'bg' => 'bg-fuchsia-500/10'],
        'full body' => ['text' => 'text-sky-300', 'border' => 'border-sky-500/25', 'bg' => 'bg-sky-500/10'],
    ];
    $muscleAliases = [
        'quads' => 'legs', 'quadriceps' => 'legs', 'hamstrings' => 'legs', 'glutes' => 'legs',
        'calves' => 'legs', 'adductors' => 'legs', 'abductors' => 'legs', 'legs & glutes' => 'legs',
        'delts' => 'shoulders', 'deltoids' => 'shoulders', 'traps' => 'shoulders', 'trapezius' => 'shoulders',
        'lats' => 'back', 'latissimus' => 'back', 'lower back' => 'back',
        'abs' => 'core', 'abdominals' => 'core', 'obliques' => 'core',
    ];
    $muscleStyle = function ($group) use ($muscleColors, $muscleAliases) {
        $key = trim(preg_replace('/[^a-zA-Z ]/', '', strtolower((string) $group)));
        if (isset($muscleColors[$key])) {
            return $muscleColors[$key];
        }
        foreach (explode(' ', $key) as $word) {
            if (isset($muscleAliases[$word])) {
                return $muscleColors[$muscleAliases[$word]];
            }
        }
        return ['text' => 'text-slate-300', 'border' => 'border-slate-500/25', 'bg' => 'bg-slate-500/10'];
    };
@endphp

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 animate-fade-up">
    <div>
        <p class="text-[13px] text-[#666666] font-medium uppercase tracking-[0.18em] mb-2">Your Library</p>
        <h1 class="text-3xl font-black tracking-tight text-white">
            My <span class="text-gradient">Programs</span>
        </h1>
        <p class="text-sm text-[#8f8f8f] mt-2 max-w-lg">
            Every plan VYRON built for you — saved, organized and always ready to re-run.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-2 text-[11px] font-bold text-[#a3a3a3] bg-[#161616] border border-[#262626] px-3.5 py-2 rounded-full">
            💾 {{ $programs->total() }} saved {{ Str::plural('program', $programs->total()) }}
        </span>
        <a href="{{ route('workouts.generate') }}" class="btn-primary px-4 py-2.5 rounded-full text-xs font-bold inline-flex items-center gap-2">
            + New Program
        </a>
    </div>
</div>

@if($programs->isEmpty())
    <div class="card p-14 text-center relative overflow-hidden mt-8">
        <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-[#3B82F6]/8 blur-[90px] pointer-events-none"></div>
        <div class="relative">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-[#3B82F6]/15 to-[#1E90FF]/10 border border-[#3B82F6]/25 flex items-center justify-center text-4xl">💾</div>
            <h3 class="text-xl font-black text-white mt-6">Nothing saved yet</h3>
            <p class="text-sm text-[#8f8f8f] mt-2 max-w-sm mx-auto leading-relaxed">
                Generate a workout or ask the AI Coach for a plan, then hit <span class="text-[#60A5FA] font-semibold">Save Plan</span> — it will show up here.
            </p>
            <a href="{{ route('workouts.generate') }}" class="btn-primary inline-flex items-center gap-2 mt-6 px-6 py-3 text-sm font-bold">
                Generate your first program
            </a>
        </div>
    </div>
@else
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-8">
        @foreach($programs as $program)
            @php
                $p = $program->plan_data ?? [];
                $cardTitle = !empty($p['title']) ? $p['title'] : $program->title;
                $difficulty = $p['difficulty'] ?? 'intermediate';
                $focus = $p['focus'] ?? null;
                $duration = $p['duration'] ?? null;
                $calories = $p['calories'] ?? null;
                $daysPerWeek = $p['days_per_week'] ?? null;
                $summary = $p['summary'] ?? null;
                $exercisesList = collect($p['exercises'] ?? []);
                $tips = collect($p['tips'] ?? []);
                $isCoach = $program->source === 'ai_coach';
            @endphp

            <div class="animate-fade-up">
                <div class="program-card group relative overflow-hidden rounded-2xl border border-[#222222] bg-[#161616] transition-all duration-300 hover:border-[#3B82F6]/40 hover:shadow-[0_16px_50px_-12px_rgba(59,130,246,0.25)]">
                    <div class="absolute -top-24 -right-20 w-64 h-64 rounded-full bg-[#3B82F6]/8 blur-[90px] pointer-events-none transition-all duration-500 group-hover:bg-[#3B82F6]/15"></div>

                    <div class="relative p-5 sm:p-6">

                        {{-- ===== Card header: badges + actions ===== --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[9px] font-black tracking-[0.18em] px-2.5 py-1 rounded-full
                                            {{ $isCoach
                                                ? 'text-[#60A5FA] bg-[#3B82F6]/10 border border-[#3B82F6]/30'
                                                : 'text-emerald-300 bg-emerald-500/10 border border-emerald-500/30' }}">
                                    {{ $isCoach ? '✦ AI COACH' : '⚡ GENERATOR' }}
                                </span>
                                <span class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full border
                                            {{ $difficulty === 'beginner' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/25'
                                                : ($difficulty === 'advanced' ? 'text-red-300 bg-red-500/10 border-red-500/25'
                                                : 'text-purple-300 bg-purple-500/10 border-purple-500/25') }}">
                                    {{ ucfirst($difficulty) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button type="button"
                                        x-data
                                        x-on:click="window.VyronModal?.open('view-program-{{ $program->id }}')"
                                        class="p-2 rounded-lg text-[#9a9a9a] hover:text-white hover:bg-black/30 transition" title="View program">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-[18px] h-[18px]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('programs.destroy', $program) }}" onsubmit="return confirm('Delete “{{ addslashes($cardTitle) }}” from your library?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-[#9a9a9a] hover:text-[#f87171] hover:bg-black/30 transition" title="Delete program">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-[18px] h-[18px]">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-lg sm:text-xl font-black text-white mt-3 tracking-tight leading-snug group-hover:text-[#93c5fd] transition-colors duration-300">{{ $cardTitle }}</h3>

                        {{-- Stats row: duration · calories · days/week · exercises · target area --}}
                        <div class="flex flex-wrap gap-x-4 gap-y-2 mt-3 text-[12.5px] text-[#b3b3b3]">
                            @if($duration)
                                <span class="flex items-center gap-1.5"><span class="text-[#60A5FA]">⏱</span> {{ $duration }} min</span>
                            @endif
                            @if($calories)
                                <span class="flex items-center gap-1.5"><span class="text-orange-400">🔥</span> {{ $calories }} kcal</span>
                            @endif
                            @if($daysPerWeek)
                                <span class="flex items-center gap-1.5"><span class="text-emerald-400">📅</span> {{ $daysPerWeek }} days/wk</span>
                            @endif
                            <span class="flex items-center gap-1.5"><span class="text-purple-400">🏋</span> {{ $exercisesList->count() }} exercises</span>
                            @if($focus)
                                <span class="flex items-center gap-1.5"><span class="text-cyan-400">🎯</span> {{ ucwords($focus) }}</span>
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($summary)
                            <p class="text-[13px] text-[#9aa7c4] leading-relaxed mt-3.5">{{ $summary }}</p>
                        @else
                            <p class="text-[13px] text-[#7a879e] leading-relaxed mt-3.5 italic">A {{ $difficulty }}-level plan focused on {{ $focus ? ucwords($focus) : 'overall fitness' }}, built to progress week over week.</p>
                        @endif

                        {{-- Exercise list --}}
                        @if($exercisesList->isNotEmpty())
                            <div class="mt-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#777777]">Exercises</p>
                                    <span class="h-px flex-1 bg-[#222222]"></span>
                                    <span class="text-[10px] text-[#5a5a5a] font-semibold">{{ $exercisesList->count() }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach($exercisesList->take(3) as $exercise)
                                        <div class="flex items-center gap-3 py-2.5 px-3 rounded-xl hover:bg-white/[0.03] transition group/ex">
                                            <div class="w-7 h-7 rounded-lg bg-[#3B82F6]/12 border border-[#3B82F6]/25 flex items-center justify-center text-[11px] font-black text-[#60A5FA] flex-shrink-0">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                @php $exMuscle = $muscleStyle($exercise['muscle'] ?? ''); @endphp
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="text-[13px] font-semibold text-white truncate">{{ $exercise['name'] ?? 'Exercise' }}</p>
                                                    @if(!empty($exercise['muscle']))
                                                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-md border {{ $exMuscle['border'] }} {{ $exMuscle['bg'] }} {{ $exMuscle['text'] }}">
                                                            {{ $exercise['muscle'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!empty($exercise['notes']))
                                                    <p class="text-[11px] text-[#7a8ba8] mt-0.5 italic truncate">💡 {{ $exercise['notes'] }}</p>
                                                @elseif(!empty($exercise['rest']))
                                                    <p class="text-[11px] text-[#7a8ba8] mt-0.5 italic">Rest {{ $exercise['rest'] }} between sets</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-[#B3B3B3] flex-shrink-0">
                                                @if(isset($exercise['sets'], $exercise['reps']))
                                                    <span class="bg-[#1c2434] border border-[#2c3a52] px-2 py-1 rounded-lg font-semibold whitespace-nowrap">{{ $exercise['sets'] }} × {{ $exercise['reps'] }}</span>
                                                @endif
                                                @if(!empty($exercise['rest']))
                                                    <span class="text-[#6f7f9c] hidden sm:inline">⏳ {{ $exercise['rest'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($exercisesList->count() > 3)
                                    <button type="button"
                                            x-data
                                            x-on:click="window.VyronModal?.open('view-program-{{ $program->id }}')"
                                            class="mt-2 w-full text-[12px] font-semibold text-[#60A5FA] hover:text-white transition py-2 rounded-lg hover:bg-white/[0.03] text-center">
                                        + View all {{ $exercisesList->count() }} exercises
                                    </button>
                                @endif
                            </div>
                        @endif

                        {{-- Coach's tips --}}
                        @if($tips->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="text-[13px]">🧠</span>
                                    <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#5f6f8f]">Coach's tips</p>
                                </div>
                                <ul class="mt-2 space-y-1.5">
                                    @foreach($tips->take(2) as $tip)
                                        <li class="flex items-start gap-2 text-[12.5px] text-[#9aa7c4]">
                                            <span class="timeline-dot mt-1.5 flex-shrink-0"></span>{{ $tip }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Card footer: saved date + source --}}
                        <div class="mt-5 pt-4 border-t border-white/5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('workouts.session.start', $program) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[11px] font-black bg-gradient-to-r from-[#3B82F6] to-[#1E90FF] text-white hover:from-[#2563EB] hover:to-[#1E90FF] transition shadow-lg shadow-[#3B82F6]/25">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z"/>
                                    </svg>
                                    Start Workout
                                </a>
                                <span class="flex items-center gap-1.5 text-[11px] text-[#666666]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                    </svg>
                                    Saved {{ optional($program->created_at)->format('M j, Y') }}
                                </span>
                            </div>
                            <span class="text-[10px] font-black tracking-[0.14em] {{ $isCoach ? 'text-[#60A5FA]' : 'text-emerald-400' }}">
                                VIA {{ strtoupper(str_replace('_', ' ', $program->source)) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ===== View modal ===== --}}
                <div id="view-program-{{ $program->id }}"
                     class="fixed inset-0 z-[80] hidden items-end sm:items-center justify-center sm:p-6">
                    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" x-data x-on:click="window.VyronModal?.close('view-program-{{ $program->id }}')"></div>
                    <div class="relative w-full max-w-2xl max-h-[88vh] overflow-y-auto rounded-2xl border border-[#222222] bg-[#141414] shadow-2xl program-modal thin-scroll">
                        <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 bg-[#141414]/95 backdrop-blur border-b border-white/5">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#5f6f8f]">Program details</p>
                            <button type="button" x-data x-on:click="window.VyronModal?.close('view-program-{{ $program->id }}')"
                                    class="p-2 rounded-lg text-[#9a9a9a] hover:text-white hover:bg-white/5 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 pb-8">
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="text-[9px] font-black tracking-[0.18em] px-2.5 py-1 rounded-full border {{ $isCoach ? 'text-[#60A5FA] bg-[#3B82F6]/10 border-[#3B82F6]/30' : 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30' }}">
                                    {{ $isCoach ? 'AI COACH' : 'WORKOUT GENERATOR' }}
                                </span>
                                <span class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full border {{ $difficulty === 'beginner' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/25' : 'text-purple-300 bg-purple-500/10 border-purple-500/25' }}">
                                    {{ ucfirst($difficulty) }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-white mt-3 tracking-tight">{{ $cardTitle }}</h3>

                            <div class="flex flex-wrap gap-x-5 gap-y-2 mt-3 text-[13px] text-[#B3B3B3]">
                                <span class="flex items-center gap-1.5"><span class="text-[#60A5FA]">⏱</span> {{ $duration ?? '—' }} min</span>
                                <span class="flex items-center gap-1.5"><span class="text-orange-400">🔥</span> {{ $calories ?? '—' }} kcal</span>
                                <span class="flex items-center gap-1.5"><span class="text-emerald-400">📅</span> {{ $daysPerWeek ?? '—' }} days/wk</span>
                                <span class="flex items-center gap-1.5"><span class="text-purple-400">🏋</span> {{ $exercisesList->count() }} exercises</span>
                                @if($focus)
                                    <span class="flex items-center gap-1.5"><span class="text-cyan-400">🎯</span> {{ ucwords($focus) }}</span>
                                @endif
                            </div>

                            @if($summary)
                                <p class="text-[13px] text-[#9aa7c4] leading-relaxed mt-4">{{ $summary }}</p>
                            @endif

                            <div class="mt-5">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#777777]">All exercises</p>
                                    <span class="h-px flex-1 bg-white/5"></span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($exercisesList as $exercise)
                                        @php($exMuscle = $muscleStyle($exercise['muscle'] ?? ''))
                                        <div class="flex items-start gap-3.5 py-3 px-4 rounded-xl bg-black/20 border border-white/5">
                                            <div class="w-8 h-8 rounded-lg bg-[#3B82F6]/12 border border-[#3B82F6]/25 flex items-center justify-center text-[12px] font-black text-[#60A5FA] flex-shrink-0">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="text-sm font-semibold text-white">{{ $exercise['name'] ?? 'Exercise' }}</p>
                                                    @if(!empty($exercise['muscle']))
                                                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-md border {{ $exMuscle['bg'] }} {{ $exMuscle['text'] }}">{{ $exercise['muscle'] }}</span>
                                                    @endif
                                                </div>
                                                @if(isset($exercise['sets'], $exercise['reps']))
                                                    <p class="text-[12px] text-[#8f9fbd] mt-0.5">{{ $exercise['sets'] }} sets × {{ $exercise['reps'] }} reps @if(!empty($exercise['rest'])) · rest {{ $exercise['rest'] }} @endif</p>
                                                @endif
                                                @if(!empty($exercise['notes']))
                                                    <p class="text-[12px] text-[#7a8ba8] italic mt-1">💡 {{ $exercise['notes'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if($tips->isNotEmpty())
                                <div class="mt-6 pt-5 border-t border-white/5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[15px]">🧠</span>
                                        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#5f6f8f]">Coach's tips</p>
                                    </div>
                                    <ul class="mt-2.5 space-y-2">
                                        @foreach($tips as $tip)
                                            <li class="flex items-start gap-2 text-[13px] text-[#9aa7c4]">
                                                <span class="timeline-dot mt-1.5 flex-shrink-0"></span>{{ $tip }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between gap-3">
                                <span class="text-[11px] text-[#666666]">Saved {{ optional($program->created_at)->format('M j, Y \a\t g:i A') }}</span>
                                <span class="text-[10px] font-black uppercase tracking-[0.14em] {{ $isCoach ? 'text-[#60A5FA]' : 'text-emerald-400' }}">
                                    VIA {{ strtoupper(str_replace('_', ' ', $program->source)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($programs->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $programs->links() }}
        </div>
    @endif
@endif

@push('scripts')
<script>
    window.VyronModal = {
        open(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('hidden');
            el.classList.add('flex');
            document.body.style.overflow = 'hidden';
        },
        close(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hidden');
            el.classList.remove('flex');
            document.body.style.overflow = '';
        },
    };
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="view-program-"]').forEach(el => {
                if (!el.classList.contains('hidden')) {
                    window.VyronModal?.close(el.id);
                }
            });
        }
    });
</script>
@endpush

@endsection