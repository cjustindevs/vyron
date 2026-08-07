<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        'exercise_name',
        'muscle_group',
        'equipment',
        'difficulty',
        'instructions',
        'image_url',
    ];

    public function workoutPlans()
    {
        return $this->belongsToMany(WorkoutPlan::class, 'workout_plan_exercises')
                    ->withPivot('day', 'sets', 'repetitions', 'rest_seconds', 'notes')
                    ->withTimestamps();
    }
}