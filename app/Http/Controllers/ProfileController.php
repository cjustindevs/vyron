<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information (account + fitness profile).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // ---------- Fitness profile (UserProfile) ----------
        $profile = $request->user()->profile()->firstOrNew();
        $profile->fill([
            'date_of_birth' => $request->input('date_of_birth'),
            'sex' => $request->input('sex'),
            'height' => $request->input('height'),
            'weight' => $request->input('weight'),
            'fitness_goal' => $request->input('fitness_goal'),
            'activity_level' => $request->input('activity_level'),
            'experience_level' => $request->input('experience_level'),
            'workout_location' => $request->input('workout_location'),
            'available_equipment' => $request->input('available_equipment', []),
            'profile_photo' => $request->input('profile_photo'),
        ]);
        $profile->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
