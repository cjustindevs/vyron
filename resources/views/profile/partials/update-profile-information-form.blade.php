<section>
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#3B82F6] to-[#1E90FF] flex items-center justify-center text-white font-black text-lg border border-white/10 shadow-lg shadow-[#3B82F6]/25">
                {{ $user->profile_photo ? '' : strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="font-bold text-white">{{ __('Profile Information') }}</h2>
                <p class="text-[12px] text-[#8f8f8f] mt-0.5">
                    {{ __("Update your account details and fitness profile.") }}
                </p>
            </div>
        </div>
        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
               class="text-xs font-semibold text-emerald-300 bg-emerald-500/10 border border-emerald-500/25 px-3.5 py-2 rounded-full flex items-center gap-2">
                ✓ Saved
            </p>
        @endif
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <x-input-label for="name" :value="__('Name')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <input id="name" name="name" type="text" class="field" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <input id="email" name="email" type="email" class="field" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="date_of_birth" :value="__('Date of Birth')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <input id="date_of_birth" name="date_of_birth" type="date" class="field" value="{{ old('date_of_birth', $user->profile?->date_of_birth) }}" />
            </div>

            <div>
                <x-input-label for="sex" :value="__('Sex')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <select id="sex" name="sex" class="field">
                    <option value="">Prefer not to say</option>
                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" {{ old('sex', $user->profile?->sex) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="height" :value="__('Height (cm)')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <input id="height" name="height" type="number" step="0.01" min="100" max="250" class="field" value="{{ old('height', $user->profile?->height) }}" placeholder="e.g. 175" />
            </div>

            <div>
                <x-input-label for="weight" :value="__('Weight (kg)')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <input id="weight" name="weight" type="number" step="0.01" min="30" max="300" class="field" value="{{ old('weight', $user->profile?->weight) }}" placeholder="e.g. 72" />
            </div>

            <div>
                <x-input-label for="fitness_goal" :value="__('Fitness Goal')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <select id="fitness_goal" name="fitness_goal" class="field">
                    <option value="">Select a goal</option>
                    @foreach(['weight_loss' => 'Lose Fat', 'muscle_gain' => 'Build Muscle', 'maintenance' => 'Stay Fit', 'endurance' => 'Improve Endurance', 'strength' => 'Gain Strength'] as $value => $label)
                        <option value="{{ $value }}" {{ old('fitness_goal', $user->profile?->fitness_goal) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="activity_level" :value="__('Activity Level')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <select id="activity_level" name="activity_level" class="field">
                    <option value="">Select activity level</option>
                    @foreach(['sedentary' => 'Sedentary', 'light' => 'Lightly Active', 'moderate' => 'Moderate', 'active' => 'Active', 'very_active' => 'Very Active'] as $value => $label)
                        <option value="{{ $value }}" {{ old('activity_level', $user->profile?->activity_level) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="experience_level" :value="__('Experience Level')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <select id="experience_level" name="experience_level" class="field">
                    <option value="">Select experience level</option>
                    @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label)
                        <option value="{{ $value }}" {{ old('experience_level', $user->profile?->experience_level) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="workout_location" :value="__('Workout Location')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
                <select id="workout_location" name="workout_location" class="field">
                    <option value="">Select location</option>
                    @foreach(['home' => 'Home', 'gym' => 'Gym', 'outdoor' => 'Outdoor'] as $value => $label)
                        <option value="{{ $value }}" {{ old('workout_location', $user->profile?->workout_location) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Available equipment --}}
        <div>
            <x-input-label :value="__('Available Equipment')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-3" />
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                @php($equipment = old('available_equipment', $user->profile?->available_equipment ?? []))
                @foreach([
                    ['gym', '🏛️ Full Gym'], ['dumbbells', '🏋️ Dumbbells'], ['barbell', '⏏️ Barbell'],
                    ['bodyweight', '🤸 Bodyweight'], ['resistance_bands', '🌀 Bands'], ['kettlebells', '⚙️ Kettlebells'],
                ] as [$value, $label])
                    <label class="seg-label flex items-center justify-between gap-1 {{ in_array($value, (array) $equipment) ? '' : 'text-[#8a8a8a]' }}">
                        <span>{{ $label }}</span>
                        <input type="checkbox" name="available_equipment[]" value="{{ $value }}" {{ in_array($value, (array) $equipment) ? 'checked' : '' }}>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Profile photo (optional) --}}
        <div>
            <x-input-label for="profile_photo" :value="__('Profile Photo URL (optional)')" class="text-[#8f8f8f] text-[11px] font-bold uppercase tracking-wider mb-2" />
            <input id="profile_photo" name="profile_photo" type="url" class="field" value="{{ old('profile_photo', $user->profile?->profile_photo) }}" placeholder="https://… (image link)" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary px-6 py-3 text-sm font-bold rounded-xl">Save Changes</button>
            <span class="text-[11px] text-[#555555]">Updates apply to future plans & insights</span>
        </div>
    </form>
</section>