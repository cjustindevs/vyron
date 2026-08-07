<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressRecord extends Model
{
    protected $fillable = [
        'user_id',
        'weight',
        'body_fat_percentage',
        'bmi',
        'recorded_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}