@extends('layouts.app')

@section('title', 'Progress Tracker')

@section('content')

{{-- ============ HEADER ============ --}}
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 animate-fade-up">
    <div>
        <p class="text-[13px] text-[#666666] font-medium uppercase tracking-[0.18em] mb-2">Data-driven gains</p>
        <h1 class="text-3xl font-black tracking-tight text-white">
            Progress <span class="text-gradient">Tracker</span>
        </h1>
        <p class="text-sm text-[#8f8f8f] mt-2 max-w-lg">
            Your training in numbers — weight, frequency, volume and muscle balance over time.
        </p>
    </div>

    <div class="inline-flex items-center gap-1 p-1 bg-[#161616] border border-[#262626] rounded-2xl self-start md:self-auto">
        @foreach(['week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $value => $label)
            <a href="{{ route('progress.index', ['period' => $value]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $period === $value ? 'bg-gradient-to-r from-[#3B82F6] to-[#1E90FF] text-white shadow-lg shadow-[#3B82F6]/30' : 'text-[#8f8f8f] hover:text-white hover:bg-white/5' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- ============ METRICS ============ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Current weight</p>
        <p class="text-2xl font-black text-white mt-2">
            {{ $currentWeight !== null ? $currentWeight . ' ' : '—' }}<span class="text-sm font-bold text-[#8f8f8f]">{{ $currentWeight !== null ? 'kg' : '' }}</span>
        </p>
        @if($weightDelta !== null)
            <p class="text-[11px] font-semibold mt-1.5 {{ $weightDelta <= 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ $weightDelta <= 0 ? '▼' : '▲' }} {{ abs($weightDelta) }} kg {{ $weightDelta <= 0 ? 'down' : 'up' }}
            </p>
        @else
            <p class="text-[11px] text-[#555555] mt-1.5">Log a weight entry to start tracking</p>
        @endif
    </div>

    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Total workouts</p>
        <p class="text-2xl font-black text-white mt-2">{{ $totalWorkouts }}</p>
        <p class="text-[11px] text-[#8f8f8f] mt-1.5">{{ $totalMinutes > 0 ? round($totalMinutes / 60, 1) . ' hrs in the gym' : 'No sessions logged yet' }}</p>
    </div>

    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Total volume</p>
        <p class="text-2xl font-black text-white mt-2">{{ number_format($totalLiftVolume) }} <span class="text-sm font-bold text-[#8f8f8f]">kg</span></p>
        <p class="text-[11px] text-[#8f8f8f] mt-1.5">weight × sets × reps</p>
    </div>

    <div class="card p-5 animate-fade-up">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#666666]">Best lift</p>
        @php($bestLift = collect($bestLifts)->first())
        @if($bestLift)
            <p class="text-2xl font-black text-white mt-2">{{ $bestLift['weight'] }} <span class="text-sm font-bold text-[#8f8f8f]">kg</span></p>
            <p class="text-[11px] text-[#8f8f8f] mt-1.5 truncate">{{ array_key_first($bestLifts) }} · {{ $bestLift['reps'] }} reps</p>
        @else
            <p class="text-2xl font-black text-[#444] mt-2">—</p>
            <p class="text-[11px] text-[#555555] mt-1.5">Lift heavy to unlock</p>
        @endif
    </div>
</div>

{{-- ============ CHARTS ============ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Weight trend --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-white">⚖️ Weight trend</h3>
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#666666]">{{ ucfirst($period) }}ly</span>
        </div>
        <div class="relative h-64">
            <canvas id="weightChart"></canvas>
        </div>
        @if(empty($weightTrend['labels']))
            <p class="absolute text-center text-[12px] text-[#666666] inset-x-0 top-1/2 -mt-6 pointer-events-none">No weight records yet</p>
        @endif
    </div>

    {{-- Workout frequency --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-white">📅 Workout frequency</h3>
            <span class="text-[10px] font-bold tracking-wider text-[#666666]">{{ ucfirst($period) }}ly</span>
        </div>
        <div class="relative h-64">
            <canvas id="frequencyChart"></canvas>
        </div>
        @if(empty($frequency['labels']))
            <p class="absolute inset-x-0 top-1/2 -mt-6 text-center text-[12px] text-[#666666] pointer-events-none">No workouts logged — generate one and get moving</p>
        @endif
    </div>

    {{-- Volume trend --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-white">🏋️ Training volume trend</h3>
            <span class="text-[10px] font-bold tracking-wider text-[#666666]">{{ ucfirst($period) }}ly</span>
        </div>
        <div class="relative h-64">
            <canvas id="volumeChart"></canvas>
        </div>
        @if(empty($volumeTrend['labels']))
            <p class="absolute inset-x-0 top-1/2 -mt-6 text-center text-[12px] text-[#666666] pointer-events-none">No volume data yet</p>
        @endif
    </div>

    {{-- Muscle distribution --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-white">🎯 Muscle group distribution</h3>
            <span class="text-[10px] font-bold tracking-wider text-[#666666]">by volume</span>
        </div>
        <div class="relative h-64 flex items-center justify-center">
            <canvas id="muscleChart" class="max-h-64"></canvas>
        </div>
        @if(empty($muscleDistribution['labels']))
            <p class="absolute inset-x-0 top-1/2 -mt-6 text-center text-[12px] text-[#666666] pointer-events-none">Workout to build this breakdown</p>
        @endif
    </div>
</div>

{{-- ============ BEST LIFTS ============ --}}
@if(count($bestLifts) > 0)
    <div class="card p-6 mt-6">
        <h3 class="text-sm font-bold text-white mb-5">🏆 Best lifts <span class="text-[10px] font-semibold text-[#666666] uppercase tracking-wider ml-2">All-time top loads</span></h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach($bestLifts as $name => $lift)
                <div class="rounded-xl bg-black/20 border border-white/5 px-4 py-3.5 transition hover:border-[#3B82F6]/40">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[13px] font-semibold text-white truncate">{{ $name }}</p>
                        <span class="text-[9px] font-bold uppercase tracking-wider text-[#6f7f9c] flex-shrink-0">{{ $lift['muscle'] }}</span>
                    </div>
                    <p class="text-lg font-black text-[#60A5FA] mt-1.5">{{ $lift['weight'] }} kg <span class="text-[11px] font-semibold text-[#8f8f8f]">× {{ $lift['reps'] }} reps</span></p>
                    @if($lift['date'])
                        <p class="text-[10px] text-[#555555] mt-0.5">{{ $lift['date'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.color = '#9aa7c4';
    Chart.defaults.font.family = "'Inter', ui-sans-serif, system-ui, sans-serif";
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.boxWidth = 8;
    Chart.defaults.plugins.legend.labels.padding = 14;
    Chart.defaults.plugins.tooltip.backgroundColor = '#1c1c1c';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(59,130,246,0.35)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.titleColor = '#fff';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 10;

    const grid = { color: 'rgba(255,255,255,0.05)' };
    const ticks = { color: '#777777', font: { size: 11 } };

    const weightData = @json($weightTrend);
    if (weightData.labels.length) {
        new Chart(document.getElementById('weightChart'), {
            type: 'line',
            data: {
                labels: weightData.labels,
                datasets: [{
                    label: 'Weight (kg)',
                    data: weightData.values,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59,130,246,0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#0A0A0A',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { grid }, y: { grid, ticks } },
                plugins: { legend: { display: false } },
            },
        });
    }

    const freqData = @json($frequency);
    if (freqData.labels.length) {
        new Chart(document.getElementById('frequencyChart'), {
            type: 'bar',
            data: {
                labels: freqData.labels,
                datasets: [{
                    label: 'Workouts',
                    data: freqData.values,
                    backgroundColor: 'rgba(59,130,246,0.75)',
                    hoverBackgroundColor: '#3B82F6',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 38,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false }, ticks },
                    y: { grid, ticks: { ...ticks, precision: 0 } },
                },
                plugins: { legend: { display: false } },
            },
        });
    }

    const volData = @json($volumeTrend);
    if (volData.labels.length) {
        new Chart(document.getElementById('volumeChart'), {
            type: 'line',
            data: {
                labels: volData.labels,
                datasets: [{
                    label: 'Volume (kg)',
                    data: volData.values,
                    borderColor: '#22C55E',
                    backgroundColor: 'rgba(34,197,94,0.1)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#22C55E',
                    pointBorderColor: '#0A0A0A',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { grid }, y: { grid, ticks } },
                plugins: { legend: { display: false } },
            },
        });
    }

    const muscleData = @json($muscleDistribution);
    if (muscleData.labels.length) {
        const palette = ['#3B82F6', '#1E90FF', '#22C55E', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#F43F5E', '#84CC16', '#F97316'];
        new Chart(document.getElementById('muscleChart'), {
            type: 'doughnut',
            data: {
                labels: muscleData.labels,
                datasets: [{
                    data: muscleData.values,
                    backgroundColor: palette.slice(0, muscleData.labels.length),
                    borderColor: '#141414',
                    borderWidth: 3,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: { legend: { position: 'right' } },
            },
        });
    }
});
</script>
@endpush

@endsection