<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutPlanExercise extends Model
{
    protected $table = 'workout_plan_exercises';

    protected $fillable = [
        'workout_plan_id',
        'exercise_id',
        'day',
        'sets',
        'repetitions',
        'rest_seconds',
        'notes',
    ];
}