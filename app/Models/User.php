<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ========== RELATIONSHIPS ==========

    /**
     * Get the user's fitness profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's workout plans.
     */
    public function workoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    /**
     * Get the user's saved programs.
     */
    public function savedPrograms()
    {
        return $this->hasMany(SavedProgram::class);
    }

    /**
     * Get the user's workout logs.
     */
    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }

    /**
     * Get the user's progress records.
     */
    public function progressRecords()
    {
        return $this->hasMany(ProgressRecord::class);
    }

    /**
     * Get the user's AI conversations.
     */
    public function aiConversations()
    {
        return $this->hasMany(AIConversation::class);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}