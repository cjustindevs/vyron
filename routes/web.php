<?php

use App\Http\Controllers\AICoachController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\SavedProgramController;
use App\Http\Controllers\WorkoutGeneratorController;
use App\Http\Controllers\WorkoutLogController;
use App\Http\Controllers\WorkoutSessionController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES
// ============================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ============================================
// AUTHENTICATED ROUTES
// ============================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/home', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('app.home');

Route::middleware('auth')->group(function () {

    // ---------- Profile ----------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- My Programs ----------
    Route::get('/my-programs', [SavedProgramController::class, 'index'])->name('programs.index');
    Route::delete('/my-programs/{program}', [SavedProgramController::class, 'destroy'])->name('programs.destroy');

    // ---------- Workout Session ----------
    Route::get('/workout-session/{program}', [WorkoutSessionController::class, 'start'])->name('workouts.session.start');
    Route::post('/workout-session/log-set', [WorkoutSessionController::class, 'logSet'])->name('workouts.session.log');
    Route::post('/workout-session/{program}/complete', [WorkoutSessionController::class, 'complete'])->name('workouts.session.complete');

    // ---------- Progress Tracker ----------
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    // ---------- Workout Logging ----------
    Route::get('/workout-logging', [WorkoutLogController::class, 'index'])->name('logs.index');

    // ---------- Workout Generator ----------
    Route::get('/workouts/generate', [WorkoutGeneratorController::class, 'index'])->name('workouts.generate');
    Route::post('/workouts/generate', [WorkoutGeneratorController::class, 'generate'])->name('workouts.generate.submit');
    Route::post('/workouts/save', [WorkoutGeneratorController::class, 'save'])->name('workouts.save');

    // ---------- AI Coach ----------
    Route::get('/ai-coach', [AICoachController::class, 'index'])->name('ai.coach');
    Route::post('/ai-coach/send', [AICoachController::class, 'send'])->name('ai.coach.send');
    Route::post('/ai-coach/save-plan', [AICoachController::class, 'savePlan'])->name('ai.coach.savePlan');
    Route::post('/ai-coach/clear', [AICoachController::class, 'clear'])->name('ai.coach.clear');
});

// ============================================
// AUTHENTICATION ROUTES (Breeze)
// ============================================
require __DIR__.'/auth.php';