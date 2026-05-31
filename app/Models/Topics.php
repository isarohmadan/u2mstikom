<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Material;
use App\Models\Tugas;

class Topics extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'status',
        'approved_by',
        'category_id',
        'tags',
        'is_locked',
        'is_edited',
        'edited_by',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_locked' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Get the status label attribute.
     */
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    // const STATUS_ARCHIVED = 'archived';
    
    public static function getTypes()
    {
        return [
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            // self::STATUS_ARCHIVED => 'Archived'
        ];
    }
    
    
    /**
     * The materials that belong to the Kelas
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    /**
     * Scope a query to only include active classes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */



    /**
     * Check if the class has available quota.
     *
     * @return bool
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_topic', 'topic_id', 'category_id')->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TopicAttachment::class, 'topic_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'topic_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(TopicVote::class, 'topic_id');
    }

    /**
     * Get the status label attribute.
     *
     * @return string
     */
    // Additional domain helpers can be added here
}
