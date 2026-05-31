<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'correct_answers',
        'answers',
        'started_at',
        'completed_at',
        'is_passed',
    ];

    protected $casts = [
        'answers' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_passed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function calculateScore(): void
    {
        $quiz = $this->quiz()->with('questions')->first();
        $correct = 0;
        $answers = $this->answers ?? [];

        foreach ($quiz->questions as $question) {
            if (isset($answers[$question->id]) && $question->isCorrect($answers[$question->id])) {
                $correct++;
            }
        }

        $total = $quiz->questions->count();
        $percentage = $total > 0 ? round(($correct / $total) * 100) : 0;

        $this->update([
            'correct_answers' => $correct,
            'total_questions' => $total,
            'score' => $percentage,
            'is_passed' => $percentage >= $quiz->passing_score,
            'completed_at' => now(),
        ]);
    }
}
