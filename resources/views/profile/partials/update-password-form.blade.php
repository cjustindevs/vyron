<section>
    <header>
        <h2 class="font-bold text-white">{{ __('Update Password') }}</h2>
        <p class="mt-1 text-[12px] text-[#8f8f8f]">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2 block">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="field" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2 block">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="field" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2 block">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="field" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary px-6 py-3 text-sm font-bold rounded-xl">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs font-semibold text-emerald-300 bg-emerald-500/10 border border-emerald-500/25 px-3.5 py-2 rounded-full">
                    ✓ Saved
                </p>
            @endif
        </div>
    </form>
</section>