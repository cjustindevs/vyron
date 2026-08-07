<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedProgram extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'plan_data',
        'source',
    ];

    protected $casts = [
        'plan_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
