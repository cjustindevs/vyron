@extends('layouts.guest')

@section('title', 'Log In – VYRON')

@section('content')
<div class="text-center mb-8">
    <div class="inline-flex items-center space-x-1">
        <span class="text-3xl font-extrabold text-white">VYRON</span>
        <span class="text-[#3B82F6] text-3xl font-extrabold">.</span>
    </div>
    <p class="text-[#B3B3B3] text-sm mt-2">Welcome back – log in to continue your fitness journey.</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-[#B3B3B3] mb-1">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full bg-[#222222] border border-[#333333] rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-[#3B82F6] transition placeholder-[#666666]">
        @error('email')
            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-[#B3B3B3] mb-1">Password</label>
        <input id="password" type="password" name="password" required
               class="w-full bg-[#222222] border border-[#333333] rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-[#3B82F6] transition placeholder-[#666666]">
        @error('password')
            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="flex items-center justify-between mt-4">
        <label class="flex items-center">
            <input type="checkbox" name="remember" class="rounded border-[#333333] bg-[#222222] text-[#3B82F6] focus:ring-[#3B82F6]">
            <span class="ml-2 text-sm text-[#B3B3B3]">Remember me</span>
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-[#3B82F6] hover:text-[#60A5FA] transition">
                Forgot password?
            </a>
        @endif
    </div>

    <button type="submit" class="w-full bg-[#3B82F6] text-white font-semibold py-3 rounded-xl hover:bg-[#2563EB] transition duration-200 mt-6">
        Log In
    </button>

    <div class="mt-6 text-center">
        <p class="text-sm text-[#666666]">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-[#3B82F6] hover:text-[#60A5FA] transition font-medium">
                Sign up
            </a>
        </p>
    </div>
</form>
@endsection