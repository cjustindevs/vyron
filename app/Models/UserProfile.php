<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'sex',
        'height',
        'weight',
        'fitness_goal',
        'activity_level',
        'experience_level',
        'workout_location',
        'available_equipment',
        'profile_photo',
    ];

    protected $casts = [
        'available_equipment' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}