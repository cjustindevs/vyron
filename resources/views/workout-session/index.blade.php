@extends('layouts.app')

@section('title', 'Workout Session')

@section('content')

@php
    $plan = $program->plan_data ?? [];
    $planTitle = !empty($plan['title']) ? $plan['title'] : $program->title;
    $planFocus = $plan['focus'] ?? null;
@endphp

<div class="max-w-2xl mx-auto animate-fade-up">

    {{-- ============ TOP BAR: back · title · timer ============ --}}
    <div class="flex items-center justify-between gap-3 mb-6">
        <a href="{{ route('programs.index') }}"
           class="inline-flex items-center gap-1.5 text-[12px] font-bold text-[#8f8f8f] hover:text-white transition px-3 py-2 rounded-xl hover:bg-white/[0.04]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Programs
        </a>

        <div class="text-center min-w-0 px-2">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#5f6f8f]">Live session</p>
            <p class="text-[13px] font-bold text-white truncate max-w-[160px] sm:max-w-[260px]">{{ $planTitle }}</p>
        </div>

        <div class="flex items-center gap-1.5 bg-[#161616] border border-[#262626] rounded-xl px-3.5 py-2 shadow-lg shadow-black/20">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_#ef4444]"></span>
            <span id="session-timer" class="font-mono text-lg font-black text-white tabular-nums tracking-wider">00:00</span>
        </div>
    </div>

    {{-- ============ PROGRESS ============ --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">
                Progress · <span id="progress-label">0%</span>
            </p>
            <p class="text-[11px] font-semibold text-[#8f8f8f]">
                <span id="exercise-counter">Exercise 1 of 1</span>
            </p>
        </div>
        <div class="h-2.5 rounded-full bg-[#1a1a1a] border border-[#222222] overflow-hidden">
            <div id="progress-bar" class="h-full rounded-full bg-gradient-to-r from-[#3B82F6] to-[#1E90FF] transition-all duration-500 ease-out shadow-[0_0_12px_rgba(59,130,246,0.5)]" style="width:0%"></div>
        </div>
    </div>

    {{-- ============ EXERCISE CARD ============ --}}
    <div id="exercise-card" class="relative overflow-hidden rounded-2xl border border-[#222222] bg-[#161616] p-5 sm:p-6 shadow-xl shadow-black/30">
        <div id="card-glow" class="absolute -top-20 -right-16 w-56 h-56 rounded-full bg-[#3B82F6]/8 blur-[80px] pointer-events-none transition-all duration-500"></div>

        <div class="relative">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="ex-number" class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#3B82F6] to-[#1E90FF]/70 flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-lg shadow-[#3B82F6]/25">1</div>
                    <div class="min-w-0">
                        <h2 id="ex-name" class="text-xl font-black text-white tracking-tight leading-snug truncate">—</h2>
                        <p id="ex-muscle" class="text-[11px] font-bold uppercase tracking-wider text-[#60A5FA] mt-0.5"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span id="ex-reps-prescribed" class="bg-[#1c2434] border border-[#2c3a52] px-3 py-1.5 rounded-xl font-black text-[13px] text-white"></span>
                    <span id="ex-rest" class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold text-[#8f8f8f] bg-black/20 border border-white/5 px-2.5 py-1.5 rounded-xl"></span>
                </div>
            </div>

            <p id="ex-notes" class="hidden text-[12.5px] text-[#7a8ba8] italic leading-relaxed mt-3"></p>

            {{-- Set rows --}}
            <div id="sets-container" class="mt-5 space-y-3"></div>
        </div>
    </div>

    {{-- ============ EXERCISE NAVIGATION ============ --}}
    <div class="mt-5 flex items-center gap-3">
        <button id="btn-prev"
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-[12.5px] font-bold bg-[#161616] border border-[#262626] text-[#b3b3b3] hover:text-white hover:border-[#3B82F6]/40 transition disabled:opacity-35 disabled:pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            Prev
        </button>

        <div id="ex-dots" class="flex items-center gap-1.5"></div>

        <button id="btn-next"
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-[12.5px] font-bold bg-[#161616] border border-[#262626] text-[#b3b3b3] hover:text-white hover:border-[#3B82F6]/40 transition disabled:opacity-35 disabled:pointer-events-none">
            Next
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </button>
    </div>

    {{-- ============ FINISH PANEL ============ --}}
    <div id="finish-panel" class="hidden mt-6 rounded-2xl border border-emerald-500/25 bg-emerald-500/[0.06] p-5 sm:p-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-xl flex-shrink-0">🏁</div>
            <div>
                <h3 class="text-base font-black text-white">All exercises complete!</h3>
                <p class="text-[12.5px] text-emerald-200/70 mt-0.5">Total time <span id="finish-time" class="font-mono font-bold text-emerald-200">00:00</span> — add a note and lock it in.</p>
            </div>
        </div>

        <form id="finish-form" method="POST" action="{{ route('workouts.session.complete', $program) }}" class="mt-4">
            @csrf
            <input type="hidden" name="duration" id="finish-duration">
            <textarea name="notes" rows="2" maxlength="500" placeholder="Optional note — how did it feel? (RPE, pumps, PRs…)"
                      class="w-full text-[13px] bg-[#222222] border border-[#333333] focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 rounded-xl px-4 py-3 text-[#E5E7EB] placeholder:text-[#666666] outline-none transition resize-none"></textarea>
            <button type="submit" id="btn-finish"
                    class="mt-3 w-full inline-flex items-center justify-center gap-2 px-5 py-4 rounded-xl font-black text-sm bg-gradient-to-r from-emerald-500 to-emerald-600 text-white hover:from-emerald-400 hover:to-emerald-500 transition shadow-lg shadow-emerald-500/25">
                Finish Workout & Save to Log
            </button>
        </form>
    </div>

    {{-- Hidden: if no exercises made it through (shouldn't happen) --}}
    <noscript>
        <div class="card p-8 text-center mt-6">
            <p class="text-sm text-[#8f8f8f]">JavaScript is required to run the live workout session.</p>
        </div>
    </noscript>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    // ============ LIVE STATE (bootstrapped from the server) ============
    const W = {
        programId: {{ $program->id }},
        startedAt: {{ $startedAt }},
        exercises: @json($exercises),
        logged: @json($loggedSets),           // { index: [ {weight, reps, rest}, ... ] }
        current: 0,
        logUrl: "{{ route('workouts.session.log') }}",
        busy: false,
    };

    // ============ HELPERS ============
    const $ = (sel) => document.querySelector(sel);
    const fmt = (secs) => {
        secs = Math.max(0, Math.floor(secs));
        const m = String(Math.floor(secs / 60)).padStart(2, '0');
        const s = String(secs % 60).padStart(2, '0');
        return `${m}:${s}`;
    };
    const toast = (msg, type = 'success') => window.Vyron && window.Vyron.toast(msg, type);
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
    const exCount = () => W.exercises.length;
    const ex = (i) => W.exercises[i];
    const loggedFor = (i) => Array.isArray(W.logged[i]) ? W.logged[i] : [];

    const totalPlanned = () => W.exercises.reduce((sum, e) => sum + e.sets, 0);
    const totalDone = () => W.exercises.reduce((sum, e, i) => sum + Math.min(loggedFor(i).length, e.sets), 0);
    const isComplete = () => W.exercises.every((e, i) => loggedFor(i).length >= 1);
    const exComplete = (i) => loggedFor(i).length >= ex(i).sets;
    const firstIncomplete = () => W.exercises.findIndex((e, i) => !exComplete(i));

    // ============ TIMER ============
    const timerEl = $('#session-timer');
    function tick() {
        const elapsed = (Date.now() / 1000) - W.startedAt;
        timerEl.textContent = fmt(elapsed);
        $('#finish-time') && ($('#finish-time').textContent = fmt(elapsed));
        $('#finish-duration') && ($('#finish-duration').value = Math.floor(elapsed));
    }
    tick();
    setInterval(tick, 1000);

    // ============ RENDER: exercise card ============
    const setsEl = $('#sets-container');

    function renderSets() {
        const e = ex(W.current);
        setsEl.innerHTML = '';

        for (let n = 1; n <= e.sets; n++) {
            const done = loggedFor(W.current)[n - 1];
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 p-3 rounded-xl border transition-all duration-300 ' +
                (done
                    ? 'bg-emerald-500/[0.06] border-emerald-500/25'
                    : 'bg-black/20 border-white/[0.06]');

            // Set number badge
            const badge = document.createElement('div');
            badge.className = 'w-9 h-9 rounded-lg flex items-center justify-center text-[12px] font-black flex-shrink-0 ' +
                (done ? 'bg-emerald-500/20 text-emerald-300' : 'bg-[#3B82F6]/12 border border-[#3B82F6]/25 text-[#60A5FA]');
            badge.textContent = n;
            row.appendChild(badge);

            if (done) {
                // Logged summary chip
                const chip = document.createElement('div');
                chip.className = 'flex-1 flex items-center justify-between gap-3';
                chip.innerHTML =
                    `<p class="text-[13px] font-bold text-emerald-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-emerald-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        ${done.weight} kg × ${done.reps} reps
                     </p>
                     <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/70">logged · rest ${done.rest}s</span>`;
                row.appendChild(chip);
            } else {
                // Inputs
                const inputs = document.createElement('div');
                inputs.className = 'flex-1 grid grid-cols-2 gap-2.5';

                const weight = document.createElement('div');
                weight.innerHTML =
                    `<label class="block text-[9px] font-black uppercase tracking-[0.16em] text-[#666666] mb-1">Weight (kg)</label>
                     <input type="number" inputmode="decimal" min="0" max="1000" step="0.5"
                            placeholder="e.g. 60"
                            class="set-input weight-input w-full text-[13px] font-semibold text-[#E5E7EB] bg-[#222222] border border-[#333333] focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 rounded-lg px-3 py-2.5 placeholder:text-[#666666] outline-none transition">`;

                const reps = document.createElement('div');
                reps.innerHTML =
                    `<label class="block text-[9px] font-black uppercase tracking-[0.16em] text-[#666666] mb-1">Reps · target ${e.reps}</label>
                     <input type="number" inputmode="numeric" min="1" max="100"
                            placeholder="e.g. 8"
                            class="set-input reps-input w-full text-[13px] font-semibold text-[#E5E7EB] bg-[#222222] border border-[#333333] focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 rounded-lg px-3 py-2.5 placeholder:text-[#666666] outline-none transition">`;

                inputs.appendChild(weight);
                inputs.appendChild(reps);
                row.appendChild(inputs);

                // Complete button
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'complete-set flex-shrink-0 inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-[12px] font-black bg-[#3B82F6] hover:bg-[#2563EB] text-white transition shadow-lg shadow-[#3B82F6]/25 disabled:opacity-40 disabled:pointer-events-none';
                btn.innerHTML =
                    `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                     </svg>Set ${n}`;
                btn.addEventListener('click', () => logSet(W.current, n, btn));
                btn.dataset.label = btn.innerHTML;
                row.appendChild(btn);
            }

            setsEl.appendChild(row);
        }
    }

    function renderExercise() {
        const e = ex(W.current);

        $('#ex-number').textContent = W.current + 1;
        $('#ex-name').textContent = e.name;
        $('#ex-muscle').textContent = e.muscle ? e.muscle.toUpperCase() : '';
        $('#ex-reps-prescribed').textContent = `${e.sets} × ${e.reps}`;
        $('#ex-rest').innerHTML = e.rest
            ? `<span class="w-1.5 h-1.5 rounded-full bg-[#60A5FA]/60 flex-shrink-0"></span>rest ${e.rest}`
            : '';
        $('#ex-rest').classList.toggle('hidden', !e.rest);

        const notes = $('#ex-notes');
        if (e.notes) {
            notes.textContent = '💡 ' + e.notes;
            notes.classList.remove('hidden');
        } else {
            notes.classList.add('hidden');
        }

        $('#exercise-counter').textContent = `Exercise ${W.current + 1} of ${exCount()}`;
        $('#ex-muscle').classList.toggle('opacity-0', !e.muscle);

        renderSets();
        renderDots();
        updateProgress();
        updateNav();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ============ RENDER: dots ============
    function renderDots() {
        const dots = $('#ex-dots');
        dots.innerHTML = '';
        for (let i = 0; i < exCount(); i++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.title = ex(i).name;
            dot.className = 'w-2.5 h-2.5 rounded-full transition-all duration-300 ' +
                (i === W.current
                    ? 'bg-[#3B82F6] scale-125 shadow-[0_0_8px_#3B82F6]'
                    : (exComplete(i) ? 'bg-emerald-500' : 'bg-[#2c2c2c] hover:bg-[#3a3a3a]'));
            dot.addEventListener('click', () => goTo(i));
            dots.appendChild(dot);
        }
    }

    // ============ PROGRESS ============
    function updateProgress() {
        const done = totalDone();
        const total = Math.max(1, totalPlanned());
        const pct = Math.min(100, Math.round((done / total) * 100));
        $('#progress-bar').style.width = pct + '%';
        $('#progress-label').textContent = pct + '%';
    }

    // ============ NAVIGATION ============
    function goTo(i) {
        if (i < 0 || i >= exCount()) return;
        W.current = i;
        renderExercise();
    }

    function updateNav() {
        $('#btn-prev').disabled = W.current === 0;
        $('#btn-next').disabled = W.current >= exCount() - 1;
        const finish = $('#finish-panel');
        if (isComplete()) {
            finish.classList.remove('hidden');
            $('#btn-finish').disabled = false;
        } else {
            finish.classList.add('hidden');
        }
    }

    $('#btn-prev').addEventListener('click', () => goTo(W.current - 1));
    $('#btn-next').addEventListener('click', () => goTo(W.current + 1));

    // ============ LOG A SET (AJAX) ============
    async function logSet(index, setNumber, btn) {
        const row = btn.closest('.flex');
        const weight = row.querySelector('.weight-input').value.trim();
        const reps = row.querySelector('.reps-input').value.trim();

        if (weight === '' || reps === '') {
            toast('Enter weight and reps for this set.', 'error');
            return;
        }

        if (W.busy) return;
        W.busy = true;
        const originalLabel = btn.innerHTML;
        btn.disabled = true;
        btn.textContent = '…';

        try {
            const res = await fetch(W.logUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf(),
                },
                body: JSON.stringify({
                    program_id: W.programId,
                    exercise_index: index,
                    set_number: setNumber,
                    weight: weight,
                    reps: reps,
                    rest_time: ex(index).rest_seconds,
                }),
            });

            const data = await res.json().catch(() => ({}));

            if (!data.success) {
                toast(data.message || 'Could not log that set.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalLabel;
                return;
            }

            // Store locally for instant re-render
            W.logged[index] = W.logged[index] || [];
            W.logged[index][setNumber - 1] = {
                weight: parseFloat(weight),
                reps: parseInt(reps, 10),
                rest: ex(index).rest_seconds,
            };

            toast(data.message, 'success', 2200);
            updateProgress();

            if (exComplete(index)) {
                renderSets();
                renderDots();
                const next = firstIncomplete();
                if (next === -1) {
                    toast('All exercises complete — time to finish! 🏁', 'info', 3600);
                    updateNav();
                } else {
                    toast(`${ex(index).name} complete — moving on!`, 'info', 2600);
                    goTo(next);
                }
            } else {
                renderSets();
            }
        } catch (err) {
            toast('Network error while logging. Try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalLabel;
        } finally {
            W.busy = false;
        }
    }

    // ============ FINISH (server re-validates + persists) ============
    $('#finish-form').addEventListener('submit', function (e) {
        if (!isComplete()) {
            e.preventDefault();
            const missing = W.exercises
                .filter((_, i) => loggedFor(i).length < 1)
                .map((e) => e.name)
                .join(', ');
            toast('Log at least one set for: ' + missing, 'error', 5000);
            return;
        }
        const btn = $('#btn-finish');
        btn.disabled = true;
        btn.textContent = 'Saving…';
    });

    // ============ INIT ============
    renderExercise();
})();
</script>
@endpush

@endsection
