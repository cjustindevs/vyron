<section class="space-y-6">
    <header>
        <h2 class="font-bold text-red-300">{{ __('Delete Account') }}</h2>
        <p class="mt-1 text-[12px] text-[#8f8f8f]">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-300 font-bold text-sm px-5 py-2.5 rounded-xl transition hover:bg-red-500/20">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
        </svg>
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-[#141414]">
            @csrf
            @method('delete')

            <h2 class="font-bold text-white text-lg">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-2 text-[13px] text-[#9aa7c4] leading-relaxed">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-5">
                <label for="password" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2 block">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="field" placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                        class="btn-ghost px-5 py-2.5 text-sm font-semibold rounded-xl">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 hover:bg-red-500/20 transition">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>