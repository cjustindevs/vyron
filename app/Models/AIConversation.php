<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIConversation extends Model
{
    protected $table = 'ai_conversations';

    protected $fillable = [
        'user_id',
        'prompt',
        'response',
        'feature_used',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}