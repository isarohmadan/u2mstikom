<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Category;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Base query
        $query = Lesson::with(['category', 'creator', 'quizzes']);

        // Apply permission filter
        if (!$user->can('lessons.manage')) {
            $query->published();
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sort
        $sort = $request->get('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('title', 'asc');
                break;
            case 'z-a':
                $query->orderBy('title', 'desc');
                break;
            case 'terbaru':
            default:
                $query->latest();
                break;
        }

        $lessons = $query->paginate(10)->withQueryString();

        // Get progress for current user
        $userProgress = [];
        if (auth()->check()) {
            $progressRecords = UserLessonProgress::where('user_id', auth()->id())
                ->whereIn('lesson_id', $lessons->pluck('id'))
                ->get()
                ->keyBy('lesson_id');
            $userProgress = $progressRecords;
        }

        // Get categories for filter
        $categories = Category::orderBy('name')->get();

        // Admin statistics
        $stats = null;
        if ($user->can('lessons.manage')) {
            $stats = [
                'total_lessons' => Lesson::count(),
                'published_lessons' => Lesson::where('is_published', true)->count(),
                'total_users_learning' => UserLessonProgress::distinct('user_id')->count('user_id'),
                'completed_lessons' => UserLessonProgress::where('is_completed', true)->count(),
                'total_quiz_attempts' => \App\Models\UserQuizAttempt::count(),
                'passed_quizzes' => \App\Models\UserQuizAttempt::whereNotNull('completed_at')
                    ->whereRaw('(correct_answers * 100 / total_questions) >= 70')->count(),
                'avg_progress' => round(UserLessonProgress::avg('progress') ?? 0, 1),
                'total_time_spent' => UserLessonProgress::sum('time_spent'), // in seconds
            ];
        }

        return view('lessons.index', compact('lessons', 'userProgress', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('lessons.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'file' => 'required|mimes:pdf,mp4|max:102400', // Max 100MB for videos
            'is_published' => 'boolean',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        
        // Upload file menggunakan helper (kompatibel dengan shared hosting)
        $filePath = FileUploadHelper::upload($file, 'lessons');
        $fileType = $file->getClientOriginalExtension();

        $lesson = Lesson::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'created_by' => auth()->id(),
            'is_published' => $request->boolean('is_published', true), // Default true agar langsung terlihat
        ]);

        return redirect()->route('lessons.index')
            ->with('success', 'Materi pembelajaran berhasil ditambahkan dan langsung terlihat di sistem.');
    }

    public function show(Lesson $lesson)
    {
        $lesson->load(['category', 'creator', 'quizzes.questions']);

        // Check if user can view unpublished lessons
        if (!$lesson->is_published && !auth()->user()->can('lessons.manage')) {
            abort(403);
        }

        // Get or create progress record
        $progress = null;
        if (auth()->check()) {
            $progress = UserLessonProgress::firstOrCreate(
                ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
                ['started_at' => now(), 'last_accessed_at' => now()]
            );
            $progress->last_accessed_at = now();
            $progress->save();
            $progress->refresh(); // Refresh to get latest data including scroll_position
        }

        // Get user's quiz attempts for this lesson
        $userAttempts = [];
        if (auth()->check()) {
            foreach ($lesson->quizzes as $quiz) {
                $userAttempts[$quiz->id] = $quiz->attempts()
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->first();
            }
        }

        // Admin statistics for this lesson
        $lessonStats = null;
        $usersProgress = null;
        $usersNoProgress = null;
        $quizAttempts = collect(); // Initialize empty collection

        if (auth()->user()->can('lessons.manage')) {
            // Get all users with progress on this lesson
            $usersProgress = UserLessonProgress::where('lesson_id', $lesson->id)
                ->with('user')
                ->orderByDesc('progress')
                ->get();

            // Get users who started this lesson
            $usersWithProgress = $usersProgress->pluck('user_id')->toArray();

            // Get all users who haven't started this lesson
            $usersNoProgress = \App\Models\User::whereNotIn('id', $usersWithProgress)
                ->orderBy('name')
                ->get();

            // Get quiz attempts for this lesson
            $quizIds = $lesson->quizzes->pluck('id')->toArray();
            $quizAttempts = \App\Models\UserQuizAttempt::whereIn('quiz_id', $quizIds)
                ->with('user', 'quiz')
                ->orderByDesc('created_at')
                ->get();

            $lessonStats = [
                'total_viewers' => $usersProgress->count(),
                'completed' => $usersProgress->where('is_completed', true)->count(),
                'in_progress' => $usersProgress->where('is_completed', false)->count(),
                'not_started' => $usersNoProgress->count(),
                'avg_progress' => round($usersProgress->avg('progress') ?? 0, 1),
                'total_time' => $usersProgress->sum('time_spent'),
                'quiz_attempts' => $quizAttempts->count(),
                'quiz_passed' => $quizAttempts->filter(function ($a) {
                    return $a->total_questions > 0 && ($a->correct_answers / $a->total_questions * 100) >= 70;
                })->count(),
            ];
        }

        return view('lessons.show', compact('lesson', 'userAttempts', 'progress', 'lessonStats', 'usersProgress', 'usersNoProgress', 'quizAttempts'));
    }

    public function edit(Lesson $lesson)
    {
        $categories = Category::orderBy('name')->get();
        return view('lessons.edit', compact('lesson', 'categories'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'file' => 'nullable|mimes:pdf,mp4|max:102400', // Max 100MB for videos
            'is_published' => 'boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'is_published' => $request->boolean('is_published'),
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($lesson->file_path) {
                FileUploadHelper::delete($lesson->file_path);
            }

            $file = $request->file('file');
            $data['file_path'] = FileUploadHelper::upload($file, 'lessons');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        $lesson->update($data);

        return redirect()->route('lessons.index')
            ->with('success', 'Materi pembelajaran berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson)
    {
        // Delete file
        if ($lesson->file_path) {
            FileUploadHelper::delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('lessons.index')
            ->with('success', 'Materi pembelajaran berhasil dihapus.');
    }

    public function download(Lesson $lesson)
    {
        if (!$lesson->is_published && !auth()->user()->can('lessons.manage')) {
            abort(403);
        }

        $path = FileUploadHelper::path($lesson->file_path);
        return response()->download($path, $lesson->file_name);
    }

    // Update progress via AJAX
    public function updateProgress(Request $request, Lesson $lesson)
    {
        $request->validate([
            'progress' => 'nullable|integer|min:0|max:100',
            'scroll_position' => 'nullable|integer|min:0',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        $progress = UserLessonProgress::updateProgress(
            auth()->id(),
            $lesson->id,
            $request->only(['progress', 'scroll_position', 'time_spent'])
        );

        return response()->json([
            'success' => true,
            'progress' => $progress->progress,
            'is_completed' => $progress->is_completed,
        ]);
    }
}
