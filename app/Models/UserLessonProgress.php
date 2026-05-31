<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLessonProgress extends Model
{
    protected $table = 'user_lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'progress',
        'scroll_position',
        'time_spent',
        'is_completed',
        'started_at',
        'completed_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public static function updateProgress(int $userId, int $lessonId, array $data)
    {
        $progress = self::firstOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId],
            ['started_at' => now()]
        );

        $progress->last_accessed_at = now();

        if (isset($data['progress'])) {
            $progress->progress = max($progress->progress, $data['progress']);
        }

        if (isset($data['scroll_position'])) {
            $progress->scroll_position = $data['scroll_position'];
        }

        if (isset($data['time_spent'])) {
            $progress->time_spent += $data['time_spent'];
        }

        // Mark as completed if progress reaches 100%
        if ($progress->progress >= 100 && !$progress->is_completed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        return $progress;
    }
}
