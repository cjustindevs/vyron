@extends('layouts.guest')

@section('title', 'Sign Up – VYRON')

@section('content')
<div class="text-center mb-8">
    <div class="inline-flex items-center space-x-1">
        <span class="text-3xl font-extrabold text-white">VYRON</span>
        <span class="text-[#3B82F6] text-3xl font-extrabold">.</span>
    </div>
    <p class="text-[#B3B3B3] text-sm mt-2">Start your fitness journey – create your free account.</p>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Name -->
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-[#B3B3B3] mb-1">Full Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
               class="w-full bg-[#222222] border border-[#333333] rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-[#3B82F6] transition placeholder-[#666666]">
        @error('name')
            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-[#B3B3B3] mb-1">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
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

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="block text-sm font-medium text-[#B3B3B3] mb-1">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="w-full bg-[#222222] border border-[#333333] rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-[#3B82F6] transition placeholder-[#666666]">
    </div>

    <button type="submit" class="w-full bg-[#3B82F6] text-white font-semibold py-3 rounded-xl hover:bg-[#2563EB] transition duration-200 mt-6">
        Create Account
    </button>

    <div class="mt-6 text-center">
        <p class="text-sm text-[#666666]">
            Already have an account?
            <a href="{{ route('login') }}" class="text-[#3B82F6] hover:text-[#60A5FA] transition font-medium">
                Log in
            </a>
        </p>
    </div>
</form>
@endsection