<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutPlan extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'goal',
        'duration',
        'days_per_week',
        'difficulty',
        'ai_generated',
        'weekly_schedule',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'workout_plan_exercises')
                    ->withPivot('day', 'sets', 'repetitions', 'rest_seconds', 'notes')
                    ->withTimestamps();
    }
}