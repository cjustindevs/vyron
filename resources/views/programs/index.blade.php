@extends('layouts.app')

@section('title', 'My Programs')

@section('content')

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 animate-fade-up">
    <div>
        <h1 class="text-3xl font-black tracking-tight text-white">
            My <span class="text-gradient">Programs</span>
        </h1>
        <p class="text-sm text-[#8f8f8f] mt-2 max-w-lg">
            Every plan VYRON generated for you — saved, organized and always ready to re-run.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-2 text-[11px] font-bold text-[#a3a3a3] bg-[#161616] border border-[#262626] px-3.5 py-2 rounded-full">
            📚 {{ $programs->total() }} saved {{ Str::plural('program', $programs->total()) }}
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
            <div class="animate-fade-up">
                @include('workouts.partials.plan-card', [
                    'plan' => $program->plan_data,
                    'source' => $program->source,
                    'showSave' => false,
                    'plan_encoded' => base64_encode(json_encode($program->plan_data)),
                ])
                <div class="flex items-center justify-between mt-2.5 px-1 text-[11px] text-[#666666]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-[#3B82F6]"></span>
                        Saved {{ $program->created_at->format('M j, Y \a\t g:i A') }}
                    </span>
                    <span class="font-bold uppercase tracking-wider text-[#555555]">{{ $program->source === 'ai_coach' ? 'Via AI Coach' : 'Workout Generator' }}</span>
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

@endsection