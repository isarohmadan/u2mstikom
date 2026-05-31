<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicAttachment extends Model
{
    protected $fillable = [
        'topic_id',
        'uploaded_by',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topics::class, 'topic_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}


