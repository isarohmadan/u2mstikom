<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicVote extends Model
{
    protected $fillable = [
        'topic_id',
        'user_id',
        'vote',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topics::class, 'topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


