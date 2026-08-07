<x-app-layout>
    <x-slot name="header">
        <p class="text-[13px] text-[#666666] font-medium uppercase tracking-[0.18em]">Your account</p>
        <h2 class="font-black text-white text-3xl tracking-tight mt-1">Profile <span class="text-gradient">Settings</span></h2>
        <p class="text-sm text-[#8f8f8f] mt-2">Keep your details up to date — VYRON tunes every plan and insight around them.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <div class="p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card border-red-500/20">
            <div class="p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>