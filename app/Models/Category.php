<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function topics(): HasMany
    {
        return $this->hasMany(Topics::class, 'category_id');
    }

    public function topicsMany(): BelongsToMany
    {
        return $this->belongsToMany(Topics::class, 'category_topic', 'category_id', 'topic_id')->withTimestamps();
    }
}


