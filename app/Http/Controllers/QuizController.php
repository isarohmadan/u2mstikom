<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\QuizQuestion;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $quizzes = Quiz::with(['lesson', 'questions'])
            ->latest()
            ->paginate(10);

        return view('quizzes.index', compact('quizzes'));
    }

    public function create(Lesson $lesson)
    {
        return view('quizzes.create', compact('lesson'));
    }

    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'allow_retry' => 'boolean',
            'is_published' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'nullable|string',
            'questions.*.option_d' => 'nullable|string',
            'questions.*.correct_answer' => 'required|in:a,b,c,d',
            'questions.*.points' => 'nullable|integer|min:1',
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
            'allow_retry' => $request->boolean('allow_retry'),
            'is_published' => $request->boolean('is_published'),
        ]);

        foreach ($request->questions as $index => $questionData) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $questionData['question'],
                'option_a' => $questionData['option_a'],
                'option_b' => $questionData['option_b'],
                'option_c' => $questionData['option_c'] ?? null,
                'option_d' => $questionData['option_d'] ?? null,
                'correct_answer' => $questionData['correct_answer'],
                'points' => $questionData['points'] ?? 1,
                'order' => $index,
            ]);
        }

        return redirect()->route('lessons.show', $lesson)
            ->with('success', 'Kuis berhasil ditambahkan.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['lesson', 'questions', 'attempts.user']);
        return view('quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions');
        return view('quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'allow_retry' => 'boolean',
            'is_published' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:quiz_questions,id',
            'questions.*.question' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'nullable|string',
            'questions.*.option_d' => 'nullable|string',
            'questions.*.correct_answer' => 'required|in:a,b,c,d',
            'questions.*.points' => 'nullable|integer|min:1',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
            'allow_retry' => $request->boolean('allow_retry'),
            'is_published' => $request->boolean('is_published'),
        ]);

        // Get existing question IDs
        $existingIds = collect($request->questions)
            ->pluck('id')
            ->filter()
            ->toArray();

        // Delete removed questions
        $quiz->questions()->whereNotIn('id', $existingIds)->delete();

        // Update or create questions
        foreach ($request->questions as $index => $questionData) {
            $quiz->questions()->updateOrCreate(
                ['id' => $questionData['id'] ?? null],
                [
                    'question' => $questionData['question'],
                    'option_a' => $questionData['option_a'],
                    'option_b' => $questionData['option_b'],
                    'option_c' => $questionData['option_c'] ?? null,
                    'option_d' => $questionData['option_d'] ?? null,
                    'correct_answer' => $questionData['correct_answer'],
                    'points' => $questionData['points'] ?? 1,
                    'order' => $index,
                ]
            );
        }

        return redirect()->route('lessons.show', $quiz->lesson)
            ->with('success', 'Kuis berhasil diperbarui.');
    }

    public function destroy(Quiz $quiz)
    {
        $lesson = $quiz->lesson;
        $quiz->delete();

        return redirect()->route('lessons.show', $lesson)
            ->with('success', 'Kuis berhasil dihapus.');
    }

    // User takes the quiz
    public function take(Quiz $quiz)
    {
        // Allow admin/pengurus to take draft quizzes for testing
        if (!$quiz->is_published && !auth()->user()->can('quizzes.manage')) {
            abort(403, 'Kuis belum dipublikasikan.');
        }

        // Check if user already has an incomplete attempt
        $existingAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if ($existingAttempt) {
            // Resume existing attempt
            $quiz->load('questions');
            return view('quizzes.take', compact('quiz', 'existingAttempt'));
        }

        // Check if retry is allowed
        $lastAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->latest()
            ->first();

        if ($lastAttempt && !$quiz->allow_retry) {
            return redirect()->route('quizzes.result', $lastAttempt)
                ->with('error', 'Anda sudah mengerjakan kuis ini dan tidak diizinkan mengulang.');
        }

        // Create new attempt
        $attempt = UserQuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'total_questions' => $quiz->questions()->count(),
            'started_at' => now(),
        ]);

        $quiz->load('questions');
        return view('quizzes.take', compact('quiz', 'attempt'));
    }

    // User submits quiz answers
    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => 'required|exists:user_quiz_attempts,id',
            'answers' => 'nullable|array', // Allow empty answers when time runs out
        ]);

        $attempt = UserQuizAttempt::findOrFail($request->attempt_id);

        // Verify ownership
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        // Save answers and calculate score
        $attempt->answers = $request->answers;
        $attempt->save();
        $attempt->calculateScore();

        return redirect()->route('quizzes.result', $attempt)
            ->with('success', 'Jawaban berhasil disimpan!');
    }

    // Show quiz result
    public function result(UserQuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() && !auth()->user()->can('quizzes.manage')) {
            abort(403);
        }

        $attempt->load(['quiz.questions', 'quiz.lesson']);

        return view('quizzes.result', compact('attempt'));
    }
}
