<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'images',
        'is_verified',
        'verified_by',
        'vote_count',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'images' => 'array',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topics::class, 'topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AnswerComment::class, 'answer_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(AnswerVote::class, 'answer_id');
    }

    public function userVote()
    {
        return $this->hasOne(AnswerVote::class, 'answer_id')
            ->where('user_id', auth()->id());
    }
}


