<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLogDetail extends Model
{
    protected $fillable = [
        'workout_log_id',
        'exercise_id',
        'sets_completed',
        'repetitions_completed',
        'weight_used',
        'rest_time',
    ];

    public function workoutLog()
    {
        return $this->belongsTo(WorkoutLog::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}