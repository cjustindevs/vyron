<x-app-layout>
    <x-slot name="header">
        <p class="text-[13px] text-[#666666] font-medium uppercase tracking-[0.18em]">Your account</p>
        <h2 class="font-black text-white text-3xl tracking-tight mt-1">Profile <span class="text-gradient">Settings</span></h2>
        <p class="text-sm text-[#8f8f8f] mt-2">Keep your details up to date — VYRON tunes every plan and insight around them.</p>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <div class="p-6 sm:p-8 flex items-center justify-between gap-4 flex-col sm:flex-row">
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-white">Sign out</h3>
                    <p class="text-sm text-[#8f8f8f] mt-1">End this session on this device.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 text-sm font-bold text-red-300 hover:text-red-200 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 hover:border-red-500/50 px-6 py-2.5 rounded-xl transition-all duration-200 active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

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