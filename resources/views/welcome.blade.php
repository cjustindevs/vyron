<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #0A0A0A; color: #FFFFFF; overflow-x: hidden; }

        .gradient-hero {
            background: radial-gradient(ellipse at 70% 50%, rgba(59, 130, 246, 0.12) 0%, transparent 60%);
        }
        .gradient-card {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(22, 22, 22, 1) 100%);
        }
        .stat-number {
            font-weight: 800;
            background: linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        html { scroll-behavior: smooth; }

        .hero-image-wrapper {
            position: relative;
            border-radius: 1.5rem;
            overflow: hidden;
            background: #161616;
            border: 1px solid #222222;
        }
        .hero-image-wrapper img {
            width: 100%;
            height: auto;
            max-height: 480px;
            object-fit: cover;
            transition: transform 0.8s ease;
        }
        .hero-image-wrapper:hover img {
            transform: scale(1.02);
        }
        .hero-image-wrapper .placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 320px;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #3B82F6;
            font-size: 4rem;
        }

        .glass-badge {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease forwards;
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem !important; line-height: 1.2 !important; }
            .hero-subtitle { font-size: 0.95rem !important; }
            .hero-image-wrapper img { max-height: 250px; }
        }
        @media (max-width: 480px) {
            .hero-title { font-size: 1.8rem !important; }
            .stat-number { font-size: 2rem !important; }
        }
    </style>
</head>
<body>

    <!-- ============================================ -->
    <!-- NAVIGATION -->
    <!-- ============================================ -->
    <nav class="fixed top-0 w-full z-50 bg-[#0A0A0A]/90 backdrop-blur-md border-b border-[#222222]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center space-x-1 group">
                    <span class="text-2xl font-extrabold text-white tracking-tight">VYRON</span>
                    <span class="text-[#3B82F6] text-2xl font-extrabold transition-transform group-hover:scale-110">.</span>
                </a>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="#features" class="text-sm text-[#B3B3B3] hover:text-white transition font-medium">Features</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm bg-[#3B82F6] text-white px-5 py-2 rounded-xl font-semibold hover:bg-[#2563EB] transition shadow-lg shadow-[#3B82F6]/20">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-[#B3B3B3] hover:text-white transition font-medium">Log In</a>
                            <a href="{{ route('register') }}" class="text-sm bg-[#3B82F6] text-white px-5 py-2 rounded-xl font-semibold hover:bg-[#2563EB] transition shadow-lg shadow-[#3B82F6]/20">Get Started</a>
                        @endauth
                    @endif
                </div>

                <button id="mobileMenuBtn" class="md:hidden text-[#B3B3B3] hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>

            <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-3">
                <a href="#features" class="block text-[#B3B3B3] hover:text-white transition font-medium">Features</a>
                <a href="#cta" class="block text-[#B3B3B3] hover:text-white transition font-medium">Get Started</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block text-center bg-[#3B82F6] text-white px-5 py-2 rounded-xl font-semibold hover:bg-[#2563EB] transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-center text-[#B3B3B3] hover:text-white transition font-medium">Log In</a>
                        <a href="{{ route('register') }}" class="block text-center bg-[#3B82F6] text-white px-5 py-2 rounded-xl font-semibold hover:bg-[#2563EB] transition">Get Started</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });
    </script>

    <!-- ============================================ -->
    <!-- HERO SECTION – Side by Side with Editable Image -->
    <!-- ============================================ -->
    @php
        $heroImage = asset('images/vyron.jpg');
    @endphp

    <section class="relative min-h-screen flex items-center overflow-hidden pt-20">
        <div class="absolute inset-0 gradient-hero"></div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
            <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
                <!-- Left Content -->
                <div class="w-full lg:w-1/2 text-center lg:text-left fade-in-up">
                    <div class="inline-flex items-center gap-2 bg-[#222222] border border-[#333333] rounded-full px-4 py-1.5 mb-5">
                        <span class="w-2 h-2 bg-[#3B82F6] rounded-full animate-pulse"></span>
                        <span class="text-xs font-medium text-[#B3B3B3]">AI-Powered Fitness Platform</span>
                    </div>

                    <h1 class="hero-title text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white leading-tight">
                        The Apex of<br>
                        <span class="text-[#3B82F6]">AI Fitness</span>
                    </h1>

                    <p class="hero-subtitle text-base md:text-lg text-[#B3B3B3] mt-5 leading-relaxed max-w-xl lg:max-w-full">
                        An AI-powered web-based fitness platform that provides personalized workout recommendations, 
                        intelligent fitness assistance, and progress monitoring to support users in achieving their fitness goals.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-[#3B82F6] text-white px-7 py-3.5 rounded-xl font-semibold text-base hover:bg-[#2563EB] transition shadow-lg shadow-[#3B82F6]/25 flex items-center justify-center gap-2">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="bg-[#3B82F6] text-white px-7 py-3.5 rounded-xl font-semibold text-base hover:bg-[#2563EB] transition shadow-lg shadow-[#3B82F6]/25 flex items-center justify-center gap-2">
                                Get Started 
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                            <a href="#features" class="bg-[#222222] text-white px-7 py-3.5 rounded-xl font-semibold text-base hover:bg-[#333333] transition border border-[#333333] flex items-center justify-center gap-2">
                                Learn More
                            </a>
                        @endauth
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-4 text-xs text-[#666666] justify-center lg:justify-start">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            Personalized Workouts
                        </span>
                        <span class="w-px h-4 bg-[#333333] hidden xs:block"></span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            AI-Powered Coaching
                        </span>
                        <span class="w-px h-4 bg-[#333333] hidden xs:block"></span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            Progress Tracking
                        </span>
                    </div>
                </div>

                <!-- Right Column – Editable Image Slot -->
                <div class="w-full lg:w-1/2 hero-image-wrapper fade-in-up delay-1">
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" alt="VYRON AI Fitness Platform" loading="lazy">
                    @else
                        <div class="placeholder">
                            <span></span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- FEATURES SECTION -->
    <!-- ============================================ -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-sm font-semibold text-[#3B82F6] tracking-widest uppercase">Core Features</span>
                <h2 class="text-3xl md:text-5xl font-extrabold text-white mt-2">Intelligent Fitness<br><span class="text-[#3B82F6]">at Your Fingertips</span></h2>
                <p class="text-lg text-[#B3B3B3] mt-4 max-w-2xl mx-auto">VYRON combines AI-powered recommendations, workout management, and progress monitoring — all in one platform.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#161616] border border-[#222222] rounded-2xl p-8 hover:border-[#3B82F6] transition gradient-card group">
                    <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-[#3B82F6]/20 transition">
                        <span class="text-3xl">🧠</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">AI Workout Generator</h3>
                    <p class="text-[#B3B3B3] mt-2 text-sm leading-relaxed">Get personalized workout plans based on your fitness goals, experience level, activity level, and available equipment. Powered by Google Gemini AI.</p>
                    <ul class="mt-3 space-y-1 text-sm text-[#666666]">
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Personalized to your goals</li>
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Adjusts to your experience level</li>
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Considers your available equipment</li>
                    </ul>
                </div>
                <div class="bg-[#161616] border border-[#222222] rounded-2xl p-8 hover:border-[#3B82F6] transition gradient-card group">
                    <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-[#3B82F6]/20 transition">
                        <span class="text-3xl">📊</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">Workout & Progress Tracking</h3>
                    <p class="text-[#B3B3B3] mt-2 text-sm leading-relaxed">Log completed workouts, monitor exercise history, and track your fitness progress with visual analytics and performance reports.</p>
                    <ul class="mt-3 space-y-1 text-sm text-[#666666]">
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Log sets, reps, and weight used</li>
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Visual progress analytics</li>
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Monitor workout consistency</li>
                    </ul>
                </div>
                <div class="bg-[#161616] border border-[#222222] rounded-2xl p-8 hover:border-[#3B82F6] transition gradient-card group">
                    <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-[#3B82F6]/20 transition">
                        <span class="text-3xl">💬</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">AI Fitness Assistant</h3>
                    <p class="text-[#B3B3B3] mt-2 text-sm leading-relaxed">Get instant answers to your fitness questions, exercise technique explanations, and educational guidance through a conversational AI interface.</p>
                    <ul class="mt-3 space-y-1 text-sm text-[#666666]">
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Conversational Q&A</li>
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Exercise technique explanations</li>
                        <li class="flex items-center gap-2"><svg class="w-3 h-3 text-[#3B82F6]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg> Educational fitness guidance</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- CTA SECTION -->
    <!-- ============================================ -->
    <section id="cta" class="py-20 border-t border-[#222222] bg-[#0D0D0D]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white">
                Every Great Transformation Starts <br><span class="text-[#3B82F6]">with One Decision.</span>
            </h2>
            <p class="text-lg text-[#B3B3B3] mt-4 max-w-2xl mx-auto">
                Experience AI-powered workout recommendations, intelligent coaching, and data-driven progress tracking built for your fitness journey.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                @auth
                    <a href="{{ route('ai.coach') }}" class="bg-[#3B82F6] text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-[#2563EB] transition shadow-lg shadow-[#3B82F6]/30 flex items-center justify-center gap-2">
                        🤖 Talk to AI Coach
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-[#3B82F6] text-white px-10 py-4 rounded-xl font-semibold text-lg hover:bg-[#2563EB] transition shadow-lg shadow-[#3B82F6]/30">Get Started Free</a>
                    <a href="{{ route('login') }}" class="bg-[#222222] text-white px-10 py-4 rounded-xl font-semibold text-lg hover:bg-[#333333] transition border border-[#333333]">Log In</a>
                @endauth
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->
    <footer class="border-t border-[#222222] py-8 bg-[#0A0A0A]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-1">
                    <span class="text-xl font-extrabold text-white tracking-tight">VYRON</span>
                    <span class="text-[#3B82F6] text-xl font-extrabold">.</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4 md:gap-6 text-sm text-[#444444]">
                    <a href="#" class="hover:text-[#666666] transition">Privacy Policy</a>
                    <a href="#" class="hover:text-[#666666] transition">Terms of Service</a>
                    <a href="#" class="hover:text-[#666666] transition">Support</a>
                    <span class="text-[#333333] hidden sm:inline">|</span>
                    <span>© {{ date('Y') }} VYRON. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobileMenu').classList.add('hidden');
            });
        });
    </script>

</body>
</html>