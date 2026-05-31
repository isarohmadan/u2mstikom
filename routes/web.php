<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TopicsController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuizController;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard - all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Settings - all authenticated users
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updateSettings'])->name('settings.update');

    // ========================================
    // USER MANAGEMENT
    // ========================================
    // Static routes first (before wildcard route)
    Route::middleware(['permission:users.create'])->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware(['permission:users.edit'])->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware(['permission:users.delete'])->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ========================================
    // ROLE MANAGEMENT
    // ========================================
    Route::middleware(['permission:roles.manage'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // ========================================
    // TOPICS
    // ========================================
    // Static routes first (before wildcard route)
    Route::middleware(['permission:topics.create'])->group(function () {
        Route::get('/topics/create', [TopicsController::class, 'create'])->name('topics.create');
        Route::post('/topics', [TopicsController::class, 'store'])->name('topics.store');
    });
    Route::middleware(['permission:topics.view'])->group(function () {
        Route::get('/topics', [TopicsController::class, 'index'])->name('topics.index');
        Route::get('/topics/my', [TopicsController::class, 'my'])->name('topics.my');
        Route::get('/topics/favorites', [TopicsController::class, 'favorites'])->name('topics.favorites');
        Route::get('/topics/{topic}', [TopicsController::class, 'show'])->name('topics.show');
        Route::post('/topics/{topic}/bookmark', [TopicsController::class, 'toggleBookmark'])->name('topics.bookmark');
    });
    Route::middleware(['permission:topics.edit||topics.my.edit'])->group(function () {
        Route::get('/topics/{topic}/edit', [TopicsController::class, 'edit'])->name('topics.edit');
        Route::put('/topics/{topic}', [TopicsController::class, 'update'])->name('topics.update');
    });
    Route::middleware(['permission:topics.delete'])->group(function () {
        Route::delete('/topics/{topic}', [TopicsController::class, 'destroy'])->name('topics.destroy');
    });
    Route::middleware(['permission:topics.approve'])->group(function () {
        Route::post('/topics/{topic}/approve', [TopicsController::class, 'approve'])->name('topics.approve');
        Route::post('/topics/{topic}/reject', [TopicsController::class, 'reject'])->name('topics.reject');
    });

    // ========================================
    // ANSWERS & COMMENTS
    // ========================================
    Route::post('/upload-image', [AnswerController::class, 'uploadImage'])->name('upload.image');
    Route::post('/upload-file', [AnswerController::class, 'uploadFile'])->name('upload.file');

    Route::middleware(['permission:answers.create'])->group(function () {
        Route::post('/topics/{topic}/answers', [AnswerController::class, 'store'])->name('topics.answers.store');
    });
    Route::middleware(['permission:comments.create'])->group(function () {
        Route::post('/answers/{answer}/comments', [AnswerController::class, 'storeComment'])->name('answers.comments.store');
    });
    Route::middleware(['permission:answers.vote'])->group(function () {
        Route::post('/answers/{answer}/vote', [AnswerController::class, 'vote'])->name('answers.vote');
    });
    Route::middleware(['permission:answers.verify'])->group(function () {
        Route::post('/answers/{answer}/verify', [AnswerController::class, 'verify'])->name('answers.verify');
    });

    // ========================================
    // TEMPLATES (KMS)
    // ========================================
    Route::middleware(['permission:templates.view'])->group(function () {
        Route::get('/templates', [TemplatesController::class, 'index'])->name('templates.index');
    });
    Route::middleware(['permission:templates.create'])->group(function () {
        Route::get('/templates/create', [TemplatesController::class, 'create'])->name('templates.create');
        Route::post('/templates', [TemplatesController::class, 'store'])->name('templates.store');
    });
    Route::middleware(['permission:templates.view'])->group(function () {
        Route::get('/templates/{template}', [TemplatesController::class, 'show'])->name('templates.show');
        Route::get('/templates/{template}/versions/{version}/download', [TemplatesController::class, 'download'])->name('templates.download');
    });
    Route::middleware(['permission:templates.manage'])->group(function () {
        Route::post('/templates/{template}/versions', [TemplatesController::class, 'uploadVersion'])->name('templates.version.upload');
        Route::post('/templates/{template}', [TemplatesController::class, 'destroy'])->name('templates.delete');
    });

    // ========================================
    // CATEGORIES
    // ========================================
    // AJAX endpoint for creating category (available to all authenticated users)
    Route::post('/categories/ajax', [CategoriesController::class, 'storeAjax'])->name('categories.store.ajax');
    
    Route::middleware(['permission:categories.manage'])->group(function () {
        Route::resource('categories', CategoriesController::class);
    });

    // ========================================
    // ANNOUNCEMENTS
    // ========================================
    Route::middleware(['permission:announcements.manage'])->group(function () {
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

    // ========================================
    // LESSONS (E-LEARNING)
    // ========================================
    Route::middleware(['permission:lessons.view'])->group(function () {
        Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
        Route::get('/lessons/{lesson}/download', [LessonController::class, 'download'])->name('lessons.download');
        Route::post('/lessons/{lesson}/progress', [LessonController::class, 'updateProgress'])->name('lessons.progress');
    });
    Route::middleware(['permission:lessons.manage'])->group(function () {
        Route::get('/lessons-create', [LessonController::class, 'create'])->name('lessons.create');
        Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    });

    // ========================================
    // QUIZZES
    // ========================================
    Route::middleware(['permission:quizzes.manage'])->group(function () {
        Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/lessons/{lesson}/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
        Route::post('/lessons/{lesson}/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
        Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
        Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    });
    Route::middleware(['permission:quizzes.take'])->group(function () {
        Route::get('/quizzes/{quiz}/take', [QuizController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quiz-attempts/{attempt}', [QuizController::class, 'result'])->name('quizzes.result');
    });
});
