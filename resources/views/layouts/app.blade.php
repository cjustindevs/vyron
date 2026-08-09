<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon_io/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon_io/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon_io/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('favicon_io/site.webmanifest') }}">
    <meta name="msapplication-TileColor" content="#0A0A0A">
    <meta name="theme-color" content="#0A0A0A">

    <title>VYRON</title>

    <meta name="description" content="VYRON — AI-powered fitness intelligence. Train smart. Progress stronger.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#0A0A0A] text-white font-sans antialiased">
    <div class="min-h-screen flex relative">

        <!-- ============================================ -->
        <!-- GLOBAL VYRON SIDEBAR (fixed, desktop)        -->
        <!-- ============================================ -->
        <aside class="hidden lg:flex w-64 bg-[#0A0A0A]/95 backdrop-blur border-r border-[#1d1d1d] fixed inset-y-0 left-0 z-50 flex-col">
            <!-- Logo -->
            <div class="px-6 py-6 border-b border-[#171717]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 group">
                    <span class="text-2xl font-black text-white tracking-tight group-hover:text-[#3B82F6] transition">VYRON</span>
                    <span class="text-2xl font-black bg-gradient-to-r from-[#3B82F6] to-[#1E90FF] bg-clip-text text-transparent">.</span>
                </a>
                <p class="text-[10px] text-[#555555] tracking-[0.2em] uppercase mt-1.5">Train Smart. Progress Stronger.</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                @foreach([
                    ['route' => 'app.home',          'label' => 'Home',              'icon' => 'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                    ['route' => 'ai.coach',          'label' => 'AI Coach',          'icon' => 'M9.813 15.904L11.75 18L9.25 15.25M15 15L20 20M11.1 7.6l.7 7.4h3.3l.6-4.1 2.6 1.4 1.2.1M3 3l1 12.5M10.5 10.5L7.5 8.75M8.5 17.5l-1 4.5'],
                    ['route' => 'workouts.generate', 'label' => 'Workout Generator', 'icon' => 'M20.42 4.58a5.4 5.4 0 00-7.65 0L9.83 7.52l7.65 7.65 2.94-2.94a5.4 5.4 0 000-7.65zM4.5 19.5L3 21l5.5-1.5 9.5-9.5-4.5-4.5-9.5 9.5-1.5 5.5z'],
                    ['route' => 'programs.index',    'label' => 'My Programs',       'icon' => 'M8 2H5a2 2 0 00-2 2v16a2 2 0 002 2h14a2 2 0 002-2V7.5L16.5 2H8zM16 22v-6h5'],
                    ['route' => 'progress.index',    'label' => 'Progress Tracker',  'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                    ['route' => 'logs.index',         'label' => 'Workout Logging',   'icon' => 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v8.25c0 .621.504 1.125 1.125 1.125h6.75a.75.75 0 01.75.75v.568M11.35 3.836a.75.75 0 01.65-.606M7.5 14.25h7.5'],
                    ['route' => 'profile.edit',       'label' => 'Profile',          'icon' => 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z'],
                ] as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[#828282] hover:text-white hover:bg-white/[0.04] group transition-all duration-200 {{ request()->routeIs($item['route']) ? 'bg-white/[0.06] text-white border border-white/[0.06]' : 'border border-transparent' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"
                             class="w-5 h-5 {{ request()->routeIs($item['route']) ? 'text-[#3B82F6]' : 'text-[#5a5a5a] group-hover:text-[#3B82F6] transition' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="font-medium text-[13px]">{{ $item['label'] }}</span>
                        @if(request()->routeIs($item['route']))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#3B82F6] shadow-[0_0_8px_#3B82F6]"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <!-- User Footer -->
            <div class="p-4 border-t border-[#171717] bg-[#0d0d0d]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-white font-bold text-sm border border-white/10 shadow-lg shadow-[#3B82F6]/20">
                        {{ Auth::user() ? strtoupper(substr(Auth::user()->name, 0, 2)) : '?' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user() ? Auth::user()->name : 'Guest' }}</p>
                        <p class="text-xs text-[#666666] truncate">{{ Auth::user() ? Auth::user()->email : '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[#666666] hover:text-[#f87171] transition text-lg p-1.5 hover:bg-[#1c1e1c] rounded-lg" title="Logout">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ==================== MAIN ==================== -->
        <main class="lg:ml-64 flex-1 min-h-screen flex flex-col w-full bg-[#0A0A0A]">
            @php
                $layoutMode = trim((string) $__env->yieldContent('layout'));
                $isFullbleed = $layoutMode === 'full';
                $slotContent = ($slot ?? null) instanceof \Illuminate\Contracts\Support\Htmlable
                    ? $slot->toHtml()
                    : (string) $__env->yieldContent('content');
                $headerContent = ($header ?? null) instanceof \Illuminate\Contracts\Support\Htmlable
                    ? $header->toHtml()
                    : (string) $__env->yieldContent('header');
            @endphp

            @if($isFullbleed)
                {!! $slotContent !!}
            @else
                <div class="w-full mx-auto px-5 sm:px-8 py-8 pb-28 lg:pb-8 max-w-[1500px] animate-fade-up">
                    {{-- Optional page header --}}
                    @if(trim(strip_tags($headerContent)) !== '')
                        <div class="mb-8">
                            {!! $headerContent !!}
                        </div>
                    @endif

                    {{-- Flash messages --}}
                    @if(session('success'))
                        <div class="mb-6 flex items-start gap-3 bg-emerald-500/10 border border-emerald-500/25 text-emerald-300 px-5 py-4 rounded-2xl animate-in">
                            <span class="text-xl leading-none">✓</span>
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 flex items-start gap-3 bg-red-500/10 border border-red-500/25 text-red-300 px-5 py-4 rounded-2xl animate-in">
                            <span class="text-xl leading-none">!</span>
                            <p class="text-sm font-medium">{{ session('error') }}</p>
                        </div>
                    @endif

                    {!! $slotContent !!}
                </div>
            @endif
        </main>
    </div>

    <!-- ============================================ -->
    <!-- MOBILE BOTTOM NAV (lg:hidden)                -->
    <!-- ============================================ -->
    <nav class="fixed bottom-0 inset-x-0 z-50 lg:hidden bg-[#0d0d0d]/95 backdrop-blur-xl border-t border-[#1d1d1d] pb-[env(safe-area-inset-bottom)]">
        <div class="grid grid-cols-4">
            @php
                $mobileItems = [
                    ['route' => 'app.home',           'label' => 'Home',     'icon' => 'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                    ['route' => 'workouts.generate',  'label' => 'Category', 'icon' => 'M20.42 4.58a5.4 5.4 0 00-7.65 0L9.83 7.52l7.65 7.65 2.94-2.94a5.4 5.4 0 000-7.65zM4.5 19.5L3 21l5.5-1.5 9.5-9.5-4.5-4.5-9.5 9.5-1.5 5.5z'],
                    ['route' => 'programs.index',     'label' => 'Save',     'icon' => 'M8 2H5a2 2 0 00-2 2v16a2 2 0 002 2h14a2 2 0 002-2V7.5L16.5 2H8zM16 22v-6h5M9 12.5l.757 1.514L12 15l-2.243 1.486L9 18l-.757-1.486L6 15l2.243-1.514L9 12.5z'],
                    ['route' => 'profile.edit',       'label' => 'Profile',  'icon' => 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
            @endphp
            @foreach($mobileItems as $item)
                <a href="{{ route($item['route']) }}" class="relative flex flex-col items-center gap-1 py-2.5 px-1 transition {{ request()->routeIs($item['route']) ? 'text-[#3B82F6]' : 'text-[#8f8f8f] hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="w-[22px] h-[22px]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="text-[10px] font-bold {{ request()->routeIs($item['route']) ? '' : 'text-[#777777]' }}">{{ $item['label'] }}</span>
                    @if(request()->routeIs($item['route']))
                        <span class="absolute -top-0.5 w-1 h-1 rounded-full bg-[#3B82F6] pointer-events-none"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </nav>

    <script>
        // ============================================================
        // VYRON global UI helpers (toasts + program saving)
        // ============================================================
        window.Vyron = (function () {
            const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

            function post(url, data) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf(),
                    },
                    body: JSON.stringify(data),
                }).then((r) => r.json().catch(() => ({})));
            }

            function toast(message, type = 'success', timeout = 3800) {
                let host = document.getElementById('vyron-toasts');
                if (!host) {
                    host = document.createElement('div');
                    host.id = 'vyron-toasts';
                    host.className = 'fixed top-5 right-5 z-[999] flex flex-col gap-2.5 items-end';
                    document.body.appendChild(host);
                }

                const colors = {
                    success: 'bg-emerald-500/10 border-emerald-500/40 text-emerald-200',
                    error: 'bg-red-500/10 border-red-500/40 text-red-200',
                    info: 'bg-[#3B82F6]/10 border-[#3B82F6]/40 text-blue-200',
                };

                const el = document.createElement('div');
                el.className = `vyron-toast px-5 py-3.5 rounded-2xl border text-sm font-medium backdrop-blur-xl shadow-2xl animate-toast-in ${colors[type] || colors.info}`;
                el.textContent = message;
                host.appendChild(el);
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transition = 'opacity .3s ease';
                    setTimeout(() => el.remove(), 320);
                }, timeout);
            }

            async function saveProgram(title, planData, source = 'workout_generator', button = null) {
                if (button) {
                    button.disabled = true;
                    const original = button.innerHTML;
                    button.innerHTML = 'Saving…';
                    try {
                        const res = await post('/workouts/save', {
                            title, plan_data: planData, source,
                        });
                        if (res.success) {
                            toast(res.message, 'success');
                            button.innerHTML = '✓ Saved to Programs';
                            button.classList.remove('bg-[#3B82F6]', 'hover:bg-[#2563EB]');
                            button.classList.add('bg-emerald-500/15', 'border', 'border-emerald-500/40', 'text-emerald-300');
                        } else {
                            toast(res.message || 'Could not save the program.', 'error');
                            button.disabled = false;
                            button.innerHTML = original;
                        }
                    } catch (e) {
                        toast('Network error while saving. Try again.', 'error');
                        button.disabled = false;
                        button.innerHTML = original;
                    }
                } else {
                    try {
                        const res = await post('/workouts/save', { title, plan_data: planData, source });
                        if (res.success) toast(res.message, 'success');
                        else toast(res.message || 'Could not save the program.', 'error');
                    } catch (e) {
                        toast('Network error while saving. Try again.', 'error');
                    }
                }
            }

            async function saveCoachPlan(title, planData, button = null) {
                if (button) {
                    button.disabled = true;
                    const original = button.innerHTML;
                    button.innerHTML = 'Saving…';
                }
                try {
                    const res = await post('/ai-coach/save-plan', { title, plan_data: planData });
                    if (res.success) {
                        toast(res.message, 'success');
                        if (button) {
                            button.innerHTML = '✓ Saved to Programs';
                            button.classList.remove('bg-[#3B82F6]', 'hover:bg-[#2563EB]');
                            button.classList.add('bg-emerald-500/15', 'border', 'border-emerald-500/40', 'text-emerald-300');
                        }
                    } else {
                        toast(res.message || 'Could not save the plan.', 'error');
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = original;
                        }
                    }
                } catch (e) {
                    toast('Network error while saving. Try again.', 'error');
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = original;
                    }
                }
            }

            function exportPlan(btn) {
                let body = 'VYRON Workout Plan';
                const card = btn ? btn.closest('.plan-card') : null;
                const payloadEl = card ? card.querySelector('.plan-payload') : null;
                if (payloadEl) {
                    try {
                        const p = JSON.parse(atob(payloadEl.value));
                        body = `${p.title || 'VYRON Plan'}\n\n`
                            + `${p.duration} min · ${p.calories} kcal · ${p.days_per_week} days/week · ${(p.exercises || []).length} exercises\n\n`;
                        (p.exercises || []).forEach((e, i) => {
                            body += `${i + 1}. ${e.name} — ${e.sets} × ${e.reps} (rest ${e.rest})\n`;
                        });
                        if (p.tips && p.tips.length) {
                            body += '\nCoach tips:\n' + p.tips.map((t) => `  • ${t}`).join('\n');
                        }
                    } catch (err) { /* fall back to generic body */ }
                }
                const w = window.open('', '_blank', 'width=760,height=900');
                if (!w) return;
                w.document.write(
                    '<!DOCTYPE html><html><head><title>VYRON Plan</title>'
                    + '<style>body{font-family:Inter,system-ui,sans-serif;background:#0A0A0A;color:#E5E7EB;padding:48px;max-width:640px;margin:0 auto}'
                    + 'h1{font-size:26px;font-weight:800;color:#fff;margin-bottom:24px}'
                    + '.brand{color:#3B82F6;font-weight:900;letter-spacing:.2em;font-size:11px;text-transform:uppercase;margin-bottom:8px}'
                    + 'pre{white-space:pre-wrap;font-family:inherit;line-height:1.9;font-size:14px}'
                    + '@media print{body{background:#fff;color:#111}.brand{color:#3B82F6}}</style></head><body>'
                    + '<div class="brand">VYRON — Train Smart. Progress Stronger.</div>'
                    + `<h1>${body.split('\n')[0].replace(/[<>&]/g, '')}</h1>`
                    + '<pre>' + body.replace(/[<>&]/g, (c) => ({ '<': '&lt;', '>': '&gt;', '&': '&amp;' }[c])) + '</pre>'
                    + '<script>window.onload=function(){setTimeout(function(){window.print()},350)}<\/script>'
                    + '</body></html>'
                );
                w.document.close();
            }

            return { csrf, post, toast, saveProgram, saveCoachPlan, exportPlan };
        })();

        // Delegated handler so any `.plan-card` (static or AJAX-injected) saves itself.
        document.addEventListener('click', function (event) {
            const btn = event.target.closest('[data-save-plan]');
            if (!btn) return;
            if (btn.getAttribute('data-busy')) return;
            const card = btn.closest('.plan-card');
            const payloadEl = card && card.querySelector('.plan-payload');
            if (!payloadEl) {
                Vyron.toast('Plan payload missing — regenerate the plan.', 'error');
                return;
            }
            btn.setAttribute('data-busy', '1');
            const title = btn.getAttribute('data-plan-title') || 'My VYRON Plan';
            Vyron.saveProgram(title, payloadEl.value, 'ai_coach', btn)
                .catch(() => {})
                .finally(() => btn.removeAttribute('data-busy'));
        });
    </script>

    @stack('scripts')
</body>
</html>