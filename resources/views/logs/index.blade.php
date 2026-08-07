@extends('layouts.app')

@section('title', 'Workout Logging')

@section('content')

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 animate-fade-up">
    <div>
        <p class="text-[13px] text-[#666666] font-medium uppercase tracking-[0.18em] mb-2">Session history</p>
        <h1 class="text-3xl font-black tracking-tight text-white">
            Workout <span class="text-gradient">Logging</span>
        </h1>
        <p class="text-sm text-[#8f8f8f] mt-2 max-w-lg">
            Every completed session, recorded — volume, sets and the exercises that got you there.
        </p>
    </div>
    <a href="{{ route('workouts.generate') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold self-start md:self-auto">
        + Log a workout
    </a>
</div>

{{-- ============ TOTALS ============ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Sessions</p>
        <p class="text-2xl font-black text-white mt-2">{{ $totals['sessions'] }}</p>
        <p class="text-[11px] text-[#8f8f8f] mt-1.5">in your log book</p>
    </div>
    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Total volume</p>
        <p class="text-2xl font-black text-white mt-2">{{ number_format($totals['volume']) }} <span class="text-sm font-bold text-[#8f8f8f]">kg</span></p>
        <p class="text-[11px] text-emerald-400 mt-1.5">weight × sets × reps</p>
    </div>
    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Sets completed</p>
        <p class="text-2xl font-black text-white mt-2">{{ number_format($totals['sets']) }}</p>
        <p class="text-[11px] text-[#8f8f8f] mt-1.5">across all sessions</p>
    </div>
    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Reps completed</p>
        <p class="text-2xl font-black text-white mt-2">{{ number_format($totals['reps']) }}</p>
        <p class="text-[11px] text-[#8f8f8f] mt-1.5">total repetitions</p>
    </div>
</div>

{{-- ============ LOG ENTRIES ============ --}}
@if($logs->isEmpty())
    <div class="card p-14 text-center relative overflow-hidden mt-6">
        <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-[#3B82F6]/8 blur-[90px] pointer-events-none"></div>
        <div class="relative">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-[#3B82F6]/15 to-[#1E90FF]/10 border border-[#3B82F6]/25 flex items-center justify-center text-4xl">📝</div>
            <h3 class="text-xl font-black text-white mt-6">No sessions logged yet</h3>
            <p class="text-sm text-[#8f8f8f] mt-2 max-w-sm mx-auto leading-relaxed">
                Generate a workout, complete it, and it will land here — every set, rep and kilogram tracked for you.
            </p>
            <a href="{{ route('workouts.generate') }}" class="btn-primary inline-flex items-center gap-2 mt-6 px-6 py-3 text-sm font-bold">
                Start your first session
            </a>
        </div>
    </div>
@else
    <div class="space-y-5 mt-8">
        @foreach($logs as $index => $log)
            <div class="card p-5 sm:p-6 animate-fade-up" style="animation-delay:{{ $index * 40 }}ms">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#3B82F6] to-[#1E90FF]/70 flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-lg shadow-[#3B82F6]/25">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $log['plan_title'] ?? 'Workout session' }}</p>
                            <p class="text-[11px] text-[#666666] mt-0.5">{{ $log['completed_at'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-[12px] text-[#B3B3B3]">
                        @if($log['duration'])
                            <span class="flex items-center gap-1.5"><span class="text-[#60A5FA]">⏱</span> {{ $log['duration'] }} min</span>
                        @endif
                        <span class="flex items-center gap-1.5"><span class="text-emerald-400">🏋</span> {{ number_format($log['volume']) }} kg</span>
                        <span class="flex items-center gap-1.5"><span class="text-purple-400">🔁</span> {{ $log['sets'] }} sets</span>
                    </div>
                </div>

                @if(!empty($log['exercises']))
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-1.5 sm:gap-2">
                        @foreach($log['exercises'] as $exercise)
                            <div class="flex items-center gap-2.5 py-2 px-3 rounded-xl bg-black/20 border border-white/5">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#3B82F6] flex-shrink-0"></span>
                                <p class="text-[12.5px] font-semibold text-[#d7dde8] truncate flex-1">{{ $exercise['name'] }}</p>
                                <p class="text-[11px] text-[#8f8f8f] flex-shrink-0">{{ $exercise['sets'] }} × {{ $exercise['reps'] }}{{ $exercise['weight'] ? ' @ ' . rtrim(rtrim(number_format($exercise['weight'], 1), '0'), '.') . 'kg' : '' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($log['notes'])
                    <p class="text-[12px] text-[#7a8ba8] italic mt-3.5">“{{ $log['notes'] }}”</p>
                @endif
            </div>
        @endforeach
    </div>
@endif

@endsection