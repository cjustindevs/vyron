{{--
    Plan card partial.
    Variables: $plan (array, normalised), $source ('workout_generator'|'ai_coach'), $plan_encoded (base64 JSON)
--}}
<div class="plan-card relative overflow-hidden rounded-2xl border border-[#2a3a5c] bg-gradient-to-br from-[#101a2e] via-[#0e1526] to-[#0b1020] animate-in">
    <input type="hidden" class="plan-payload" value="{{ $plan_encoded }}">

    <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-[#3B82F6]/20 blur-[90px] pointer-events-none"></div>
    <div class="absolute -bottom-28 -left-14 w-56 h-56 rounded-full bg-[#1E90FF]/12 blur-[90px] pointer-events-none"></div>

    <div class="relative p-5 sm:p-6">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#60A5FA] bg-[#3B82F6]/10 border border-[#3B82F6]/25 px-2.5 py-1 rounded-full">
                        AI Generated
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full
                                {{ $plan['difficulty'] === 'beginner' ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/25'
                                    : ($plan['difficulty'] === 'advanced' ? 'bg-red-500/10 text-red-300 border border-red-500/25'
                                    : 'bg-purple-500/10 text-purple-300 border border-purple-500/25') }}">
                        {{ ucfirst($plan['difficulty']) }}
                    </span>
                </div>
                <h4 class="text-lg sm:text-xl font-black text-white mt-2.5 tracking-tight leading-snug">{{ $plan['title'] }}</h4>
            </div>
        </div>

        {{-- Meta --}}
        <div class="flex flex-wrap gap-x-5 gap-y-2 mt-3.5 text-[12.5px] text-[#a8a8a8]">
            <span class="flex items-center gap-1.5"><span class="text-[#60A5FA]">⏱</span> {{ $plan['duration'] }} min</span>
            <span class="flex items-center gap-1.5"><span class="text-orange-400">🔥</span> {{ $plan['calories'] }} kcal</span>
            <span class="flex items-center gap-1.5"><span class="text-emerald-400">📈</span> {{ $plan['days_per_week'] }} days/wk</span>
            <span class="flex items-center gap-1.5"><span class="text-purple-400">🏋</span> {{ count($plan['exercises']) }} exercises</span>
            @if(!empty($plan['focus']) && $plan['focus'] !== $plan['title'])
                <span class="flex items-center gap-1.5"><span class="text-cyan-400">🎯</span> {{ $plan['focus'] }}</span>
            @endif
        </div>

        @if(!empty($plan['summary']))
            <p class="text-[13px] text-[#9aa7c4] leading-relaxed mt-4">{{ $plan['summary'] }}</p>
        @endif

        {{-- Exercises --}}
        <div class="mt-5 space-y-1">
            @foreach($plan['exercises'] as $index => $exercise)
                <div class="flex items-center gap-3.5 py-2.5 px-3 rounded-xl hover:bg-white/[0.03] transition group/ex">
                    <div class="w-7 h-7 rounded-lg bg-[#3B82F6]/12 border border-[#3B82F6]/25 flex items-center justify-center text-[11px] font-black text-[#60A5FA] flex-shrink-0">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ $exercise['name'] }}
                            @if(!empty($exercise['muscle']))
                                <span class="ml-1.5 text-[10px] font-bold uppercase tracking-wider text-[#6f8ab8]">{{ $exercise['muscle'] }}</span>
                            @endif
                        </p>
                        @if(!empty($exercise['notes']))
                            <p class="text-[11px] text-[#7a8ba8] mt-0.5 italic truncate">{{ $exercise['notes'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-xs text-[#B3B3B3] flex-shrink-0">
                        <span class="bg-[#1c2434] border border-[#2c3a52] px-2 py-1 rounded-lg font-semibold">{{ $exercise['sets'] }} × {{ $exercise['reps'] }}</span>
                        <span class="text-[#6f7f9c] hidden sm:inline">⏳ {{ $exercise['rest'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tips --}}
        @if(!empty($plan['tips']))
            <div class="mt-4 pt-4 border-t border-white/5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-[#5f6f8f] mb-2">Coach's tips</p>
                <ul class="space-y-1.5">
                    @foreach($plan['tips'] as $tip)
                        <li class="flex items-start gap-2 text-[12.5px] text-[#9aa7c4]">
                            <span class="timeline-dot mt-1.5 flex-shrink-0"></span>{{ $tip }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2.5 mt-5 pt-5 border-t border-white/5">
            @if($showSave ?? true)
                <button type="button" data-save-plan data-plan-title="{{ $plan['title'] }}"
                        class="btn-primary px-5 py-2.5 text-[13px] inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Save Plan
                </button>
            @endif
            @if(($source ?? '') === 'workout_generator')
                <button type="button" onclick="document.getElementById('generatorForm')?.requestSubmit?.()"
                        class="btn-ghost px-5 py-2.5 text-[13px] inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                    Regenerate
                </button>
                <button type="button" onclick="Vyron.exportPlan(this)"
                        class="btn-ghost px-5 py-2.5 text-[13px] inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 12h6m-6 0a2.25 2.25 0 01-2.25-2.25V4.5m5.25 0v6a1.5 1.5 0 001.5 1.5h6m5.25 3.75V16.5A2.25 2.25 0 0118.75 18.75H5.25a2.25 2.25 0 01-2.25-2.25V9m13.5 12h6"/>
                    </svg>
                    Export PDF
                </button>
            @endif
        </div>
    </div>
</div>
