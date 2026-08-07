@extends('layouts.app')

@section('title', 'Workout Generator')

@section('content')

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 animate-fade-up">
    <div>
        <h1 class="text-3xl font-black tracking-tight text-white">
            Workout <span class="text-gradient">Generator</span>
        </h1>
        <p class="text-sm text-[#8f8f8f] mt-2 max-w-lg">
            Tell VYRON your constraints and our engine will program a complete, periodized session in seconds.
        </p>
    </div>
    <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-[#60A5FA] bg-[#3B82F6]/10 border border-[#3B82F6]/25 px-3.5 py-2 rounded-full self-start md:self-auto">
        <span class="w-1.5 h-1.5 rounded-full bg-[#3B82F6] animate-pulse"></span> AI Engine · Ready
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mt-8 items-start">

    {{-- ==================== LEFT: PARAMETERS ==================== --}}
    <div class="lg:col-span-2 card p-6 sm:p-7 lg:sticky lg:top-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-base font-bold text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#3B82F6]/12 border border-[#3B82F6]/25 flex items-center justify-center text-[15px]">⚙️</span>
                Set Your Constraints
            </h2>
            <button type="button" onclick="Vyron.resetGenerator()"
                    class="text-[11px] font-semibold text-[#6f6f6f] hover:text-white transition px-2.5 py-1.5 rounded-lg hover:bg-[#1c1c1c]">
                Reset
            </button>
        </div>

        <form id="generatorForm" class="space-y-6">

            {{-- Goal --}}
            <div>
                <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Goal</label>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                        ['muscle_gain', '💪 Build Muscle'],
                        ['weight_loss', '🔥 Lose Fat'],
                        ['strength', '🏋️ Strength'],
                        ['endurance', '🏃 Endurance'],
                        ['maintenance', '⚖️ Maintain'],
                    ] as [$value, $label])
                        <label class="seg-label">
                            <input type="radio" name="goal" value="{{ $value }}" {{ old('goal', $profile?->fitness_goal ?? 'muscle_gain') === $value ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Experience --}}
            <div>
                <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Experience</label>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                        ['beginner', '🌱 Beginner'],
                        ['intermediate', '⚡ Intermediate'],
                        ['advanced', '🔥 Advanced'],
                    ] as [$value, $label])
                        <label class="seg-label">
                            <input type="radio" name="experience_level" value="{{ $value }}" {{ old('experience_level', $profile?->experience_level ?? 'intermediate') === $value ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Equipment --}}
            <div>
                <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Equipment</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['gym', '🏛️ Full Gym'],
                        ['dumbbells', '🏋️ Dumbbells'],
                        ['barbell', '⏏️ Barbell'],
                        ['bodyweight', '🤸 Bodyweight'],
                        ['resistance_bands', '🌀 Bands'],
                        ['kettlebells', '⚙️ Kettlebells'],
                    ] as [$value, $label])
                        <label class="seg-label flex items-center justify-between gap-1">
                            <span>{{ $label }}</span>
                            <input type="checkbox" name="equipment[]" value="{{ $value }}" checked>
                            <span class="check-mark"></span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Location / Duration / Days --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-3">
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Training Location</label>
                    <select name="workout_location" class="field">
                        <option value="gym" {{ old('workout_location', $profile?->workout_location ?? 'gym') === 'gym' ? 'selected' : '' }}>Gym</option>
                        <option value="home" {{ old('workout_location', $profile?->workout_location) === 'home' ? 'selected' : '' }}>Home</option>
                        <option value="outdoor" {{ old('workout_location', $profile?->workout_location) === 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Duration</label>
                    <select name="duration" class="field">
                        @foreach([30, 45, 60, 75, 90] as $minutes)
                            <option value="{{ $minutes }}" {{ old('duration', 60) == $minutes ? 'selected' : '' }}>{{ $minutes }} min</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Days / Week</label>
                    <select name="days_per_week" class="field">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('days_per_week', 3) == $i ? 'selected' : '' }}>{{ $i }} day{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Difficulty</label>
                    <select name="difficulty" class="field">
                        <option value="moderate">Moderate</option>
                        <option value="hard" selected>Hard</option>
                        <option value="brutal">Brutal</option>
                    </select>
                </div>
            </div>

            {{-- Target muscle --}}
            <div>
                <label class="block text-[12px] font-bold uppercase tracking-wider text-[#8f8f8f] mb-2.5">Target Muscle</label>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                        ['full_body', '🫀 Full Body'],
                        ['upper', '💪 Upper'],
                        ['lower', '🦵 Lower'],
                        ['push', '🙌 Push'],
                        ['pull', '🎣 Pull'],
                    ] as [$value, $label])
                        <label class="seg-label">
                            <input type="radio" name="target_muscle" value="{{ $value }}" {{ old('target_muscle', 'full_body') === $value ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Generate --}}
            <button type="submit" id="generateBtn"
                    class="w-full btn-primary py-4 text-sm font-bold inline-flex items-center justify-center gap-2.5 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L11.75 18L9.25 15.25M15 15L20 20M11.1 7.6l.7 7.4h3.3l.6-4.1 2.6 1.4 1.2.1M3 3l1 12.5M10.5 10.5L7.5 8.75M8.5 17.5l-1 4.5"/>
                </svg>
                <span id="generateLabel">Generate Workout</span>
                <span id="generateSpinner" class="hidden"><span class="spinner"></span></span>
            </button>
        </form>
    </div>

    {{-- ==================== RIGHT: GENERATED PLAN ==================== --}}
    <div class="lg:col-span-3">
        <div id="planResult">
            {{-- Loading skeletons --}}
            <div id="planLoading" class="hidden space-y-4">
                <div class="card p-6 space-y-4">
                    <div class="skeleton h-6 w-1/3"></div>
                    <div class="skeleton h-4 w-2/3"></div>
                    <div class="skeleton h-4 w-1/2"></div>
                    <div class="pt-2 space-y-3">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="flex gap-3 items-center">
                                <div class="skeleton w-7 h-7 rounded-lg"></div>
                                <div class="skeleton h-4 flex-1"></div>
                                <div class="skeleton h-4 w-20 rounded-lg"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <p class="text-center text-sm text-[#6f6f6f] animate-pulse flex items-center justify-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#3B82F6] animate-pulse"></span>
                    VYRON is programming your session… calibrating volume, load and recovery.
                </p>
            </div>

            {{-- Empty state --}}
            <div id="planEmpty" class="card p-10 sm:p-14 text-center relative overflow-hidden">
                <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-[#3B82F6]/8 blur-[90px] pointer-events-none"></div>
                <div class="relative">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-[#3B82F6]/15 to-[#1E90FF]/10 border border-[#3B82F6]/25 flex items-center justify-center text-4xl glow-blue">
                        💪
                    </div>
                    <h3 class="text-xl font-black text-white mt-6">Your plan will appear here</h3>
                    <p class="text-sm text-[#8f8f8f] mt-2 max-w-sm mx-auto leading-relaxed">
                        Set your constraints on the left and hit <span class="text-[#60A5FA] font-semibold">Generate Workout</span> — VYRON will craft a complete session with sets, reps and rest.
                    </p>
                    <div class="flex items-center justify-center gap-2 mt-6 text-[11px] font-semibold uppercase tracking-wider text-[#555555]">
                        <span class="w-1 h-1 rounded-full bg-[#3B82F6]"></span> Free & unlimited
                        <span class="w-1 h-1 rounded-full bg-[#3B82F6]"></span> Save anytime
                    </div>
                </div>
            </div>

            {{-- Error state --}}
            <div id="planError" class="hidden card p-8 text-center border-red-500/25 bg-red-500/[0.03]">
                <p class="text-3xl mb-3">⚠️</p>
                <h4 class="font-bold text-white">Generation failed</h4>
                <p id="planErrorText" class="text-sm text-[#9aa7c4] mt-2"></p>
                <button onclick="document.getElementById('generatorForm')?.requestSubmit?.()"
                        class="btn-ghost mt-5 px-5 py-2.5 text-sm font-semibold">Try again</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('generatorForm');
    const btn = document.getElementById('generateBtn');
    const label = document.getElementById('generateLabel');
    const spinner = document.getElementById('generateSpinner');

    function setLoading(loading) {
        document.getElementById('planLoading').classList.toggle('hidden', !loading);
        document.getElementById('planEmpty').classList.toggle('hidden', loading);
        document.getElementById('planError').classList.toggle('hidden', true);
        btn.disabled = loading;
        label.textContent = loading ? 'Programming…' : 'Generate Workout';
        spinner.classList.toggle('hidden', !loading);
    }

    function showError(message) {
        document.getElementById('planLoading').classList.add('hidden');
        document.getElementById('planEmpty').classList.add('hidden');
        const box = document.getElementById('planError');
        document.getElementById('planErrorText').textContent = message;
        box.classList.remove('hidden');
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        setLoading(true);

        const data = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: data,
            });

            const json = await res.json().catch(() => ({}));

            if (!res.ok || !json.success) {
                showError(json.message || 'The AI engine is busy right now. Please try again.');
                return;
            }

            // Inject the rendered plan card
            const target = document.createElement('div');
            target.id = 'generatedPlan';
            target.className = 'animate-fade-up space-y-5';
            target.innerHTML = json.plan_html || '<div class="card p-6 whitespace-pre-wrap text-sm text-[#b3b3b3]">' + (json.raw || '') + '</div>';

            const existing = document.getElementById('generatedPlan');
            if (existing) existing.remove();

            document.getElementById('planLoading').classList.add('hidden');
            document.getElementById('planEmpty').classList.add('hidden');
            document.getElementById('planError').classList.add('hidden');
            document.getElementById('planResult').appendChild(target);

            Vyron.toast('Workout generated successfully!', 'success');
        } catch (err) {
            showError('Network error while contacting the AI engine. Please try again.');
        } finally {
            setLoading(false);
        }
    });
})();
</script>
@endpush

@endsection