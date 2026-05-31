@extends('layouts.app')

@section('title', $lesson->title)

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('lessons.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <div class="d-flex gap-2">
                        @can('lessons.edit')
                            <a href="{{ route('lessons.edit', $lesson) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i> Ubah
                            </a>
                        @endcan
                        @can('quizzes.create')
                            <a href="{{ route('quizzes.create', $lesson) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Kuis
                            </a>
                        @endcan
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-4">
                            @if($lesson->file_type === 'pdf')
                                <i class="bi bi-file-pdf text-danger display-4 me-3"></i>
                            @else
                                <i class="bi bi-file-word text-primary display-4 me-3"></i>
                            @endif
                            <div class="flex-grow-1">
                                <h3 class="mb-1">{{ $lesson->title }}</h3>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @if($lesson->category)
                                        <span class="badge bg-secondary">{{ $lesson->category->name }}</span>
                                    @endif
                                    @if(!$lesson->is_published)
                                        <span class="badge bg-warning">Draft</span>
                                    @else
                                        <span class="badge bg-success">Dipublikasikan</span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-person me-1"></i>{{ $lesson->creator->name }} •
                                    <i class="bi bi-calendar me-1"></i>{{ $lesson->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        @if($lesson->description)
                            <div class="mb-4">
                                <h6>Deskripsi</h6>
                                <p class="text-muted">{{ $lesson->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content Viewer Card -->
                <div class="card shadow-sm mb-4" id="documentCard">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        @if($lesson->file_type === 'pdf')
                            <span><i class="bi bi-file-pdf me-2 text-danger"></i>Dokumen PDF</span>
                            <div>
                                <span class="badge bg-secondary me-2" id="pageIndicator">Halaman 1</span>
                                <a href="{{ route('lessons.download', $lesson) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download me-1"></i> Unduh
                                </a>
                            </div>
                        @else
                            <span><i class="bi bi-play-circle me-2 text-primary"></i>Video Pembelajaran</span>
                            <div>
                                <span class="badge bg-secondary me-2" id="videoTime">00:00 / 00:00</span>
                                <a href="{{ route('lessons.download', $lesson) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download me-1"></i> Unduh
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if($lesson->file_type === 'pdf')
                            <!-- PDF Preview with PDF.js -->
                            <div id="pdfViewerContainer" style="height: 85vh; overflow-y: auto; background: #525659;">
                                <div id="pdfPages"
                                    style="display: flex; flex-direction: column; align-items: center; padding: 10px 0;"></div>
                            </div>
                        @else
                            <!-- MP4 Video Player -->
                            <div class="bg-dark">
                                <video id="videoPlayer" class="w-100" style="max-height: 85vh;" controls preload="auto">
                                    <source src="{{ asset('storage/' . $lesson->file_path) }}" type="video/mp4">
                                    Browser Anda tidak mendukung video HTML5.
                                </video>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Progress Card -->
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Progress Belajar</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="display-4 fw-bold text-primary" id="progressPercent">{{ $progress->progress ?? 0 }}%
                            </div>
                            <small class="text-muted">Selesai dibaca</small>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-primary" id="progressBar" role="progressbar"
                                style="width: {{ $progress->progress ?? 0 }}%"></div>
                        </div>
                        <div class="row text-center small mb-3">
                            <div class="col-6">
                                <i class="bi bi-clock text-muted"></i>
                                <div id="timeSpent">{{ floor(($progress->time_spent ?? 0) / 60) }} menit</div>
                            </div>
                            <div class="col-6">
                                <i class="bi bi-check-circle text-muted"></i>
                                <div id="statusText">{{ ($progress->is_completed ?? false) ? 'Selesai' : 'Belum selesai' }}
                                </div>
                            </div>
                        </div>

                        @if($progress && $progress->is_completed)
                            <div class="text-center">
                                <span class="badge bg-success fs-6 py-2 px-3"><i class="bi bi-check-circle-fill me-1"></i>Sudah
                                    Selesai Dibaca</span>
                            </div>
                        @else
                            <div class="text-center">
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Progress tercatat otomatis
                                    berdasarkan waktu membaca</small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Informasi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Dibuat oleh</small>
                            <strong>{{ $lesson->creator->name }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Tanggal dibuat</small>
                            <strong>{{ $lesson->created_at->format('d M Y H:i') }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Terakhir diperbarui</small>
                            <strong>{{ $lesson->updated_at->format('d M Y H:i') }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Jumlah Kuis</small>
                            <strong>{{ $lesson->quizzes->count() }} kuis</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Status</small>
                            @if($lesson->is_published)
                                <span class="badge bg-success">Dipublikasikan</span>
                            @else
                                <span class="badge bg-warning">Draft</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quiz Section -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Kuis</h6>
                    </div>
                    <div class="card-body">
                        @if($lesson->quizzes->isEmpty())
                            <div class="text-center py-3">
                                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                                <p class="mt-2 text-muted small">Belum ada kuis.</p>
                            </div>
                        @else
                            @foreach($lesson->quizzes as $quiz)
                                <div class="border rounded p-3 mb-2">
                                    <h6 class="mb-1">{{ $quiz->title }}</h6>
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-list-ol me-1"></i>{{ $quiz->questions->count() }} Soal
                                        @if($quiz->time_limit)
                                            • <i class="bi bi-clock me-1"></i>{{ $quiz->time_limit }} menit
                                        @endif
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-check-circle me-1"></i>Nilai lulus: {{ $quiz->passing_score }}%
                                    </div>
                                    @if(!$quiz->is_published)
                                        <span class="badge bg-warning mb-2">Draft</span>
                                    @endif
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        {{-- Button Kerjakan Kuis untuk semua user yang punya permission --}}
                                        @can('quizzes.take')
                                            @if(isset($userAttempts[$quiz->id]) && $userAttempts[$quiz->id] && $userAttempts[$quiz->id]->completed_at)
                                                {{-- User sudah mengerjakan --}}
                                                <a href="{{ route('quizzes.result', $userAttempts[$quiz->id]) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-bar-chart me-1"></i>Lihat Hasil
                                                </a>
                                                @if($quiz->allow_retry && $quiz->is_published)
                                                    <a href="{{ route('quizzes.take', $quiz) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-arrow-repeat me-1"></i>Ulang Kuis
                                                    </a>
                                                @endif
                                            @else
                                                {{-- User belum mengerjakan atau ada attempt yang belum selesai --}}
                                                @if($quiz->is_published || auth()->user()->can('quizzes.manage'))
                                                    <a href="{{ route('quizzes.take', $quiz) }}" class="btn btn-primary">
                                                        <i class="bi bi-play-fill me-2"></i>Kerjakan Kuis
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">Menunggu Publikasi</span>
                                                @endif
                                            @endif
                                        @endcan
                                        @can('quizzes.edit')
                                            <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('quizzes.delete')
                                            <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus kuis ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Statistics Section --}}
        @can('lessons.manage')
            @if($lessonStats)
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-bar-chart me-2"></i>Statistik Pembelajaran</h5>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $lessonStats['total_viewers'] }}</h3>
                                <small>User Mulai Belajar</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $lessonStats['completed'] }}</h3>
                                <small>Selesai (100%)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $lessonStats['avg_progress'] }}%</h3>
                                <small>Rata-rata Progress</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $lessonStats['quiz_attempts'] }}</h3>
                                <small>Kuis Dikerjakan ({{ $lessonStats['quiz_passed'] }} lulus)</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Progress List --}}
                <div class="row mt-3">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-success text-white py-2">
                                <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>User dengan Progress
                                    (<span id="progressCount">{{ $usersProgress->count() }}</span>)</h6>
                            </div>
                            <div class="card-body p-3">
                                {{-- Search and Filter --}}
                                <div class="mb-3">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" id="progressSearch" placeholder="Cari nama user...">
                                    </div>
                                    <select class="form-select form-select-sm" id="progressFilter">
                                        <option value="all">Semua Status</option>
                                        <option value="completed">Selesai</option>
                                        <option value="in-progress">Proses</option>
                                    </select>
                                </div>
                                <div style="max-height: 400px; overflow-y: auto;" id="progressTableContainer">
                                    @if($usersProgress->count() > 0)
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="py-2 px-3" style="width: 45%">Nama</th>
                                                    <th class="py-2 px-3 text-center" style="width: 35%">Progress</th>
                                                    <th class="py-2 px-3 text-center" style="width: 20%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="progressTableBody">
                                                @foreach($usersProgress as $up)
                                                    <tr data-name="{{ strtolower($up->user->name) }}" 
                                                        data-status="{{ $up->is_completed ? 'completed' : 'in-progress' }}"
                                                        data-progress="{{ $up->progress }}">
                                                        <td class="py-2 px-3 align-middle">{{ $up->user->name }}</td>
                                                        <td class="py-2 px-3 align-middle">
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar {{ $up->progress >= 100 ? 'bg-success' : 'bg-primary' }}"
                                                                    style="width: {{ $up->progress }}%">
                                                                    {{ $up->progress }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="py-2 px-3 text-center align-middle">
                                                            @if($up->is_completed)
                                                                <span class="badge bg-success">Selesai</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Proses</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox display-6"></i>
                                            <p class="mt-2 mb-0">Belum ada user yang mulai belajar</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-secondary text-white py-2">
                                <h6 class="mb-0"><i class="bi bi-person-x me-2"></i>User Belum Mulai
                                    (<span id="noProgressCount">{{ $usersNoProgress->count() }}</span>)</h6>
                            </div>
                            <div class="card-body p-3">
                                {{-- Search --}}
                                <div class="mb-3">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" id="noProgressSearch" placeholder="Cari nama atau email...">
                                    </div>
                                </div>
                                <div style="max-height: 400px; overflow-y: auto;" id="noProgressTableContainer">
                                    @if($usersNoProgress->count() > 0)
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="py-2 px-3" style="width: 50%">Nama</th>
                                                    <th class="py-2 px-3" style="width: 50%">Email</th>
                                                </tr>
                                            </thead>
                                            <tbody id="noProgressTableBody">
                                                @foreach($usersNoProgress as $user)
                                                    <tr data-name="{{ strtolower($user->name) }}" 
                                                        data-email="{{ strtolower($user->email) }}">
                                                        <td class="py-2 px-3 align-middle">{{ $user->name }}</td>
                                                        <td class="py-2 px-3 align-middle text-muted">{{ $user->email }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-check-circle display-6 text-success"></i>
                                            <p class="mt-2 mb-0">Semua user sudah mulai belajar!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quiz Attempts Table --}}
                @if($lesson->quizzes->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Riwayat Pengerjaan Kuis
                                        (<span id="quizAttemptsCount">{{ $quizAttempts->count() ?? 0 }}</span>)</h6>
                                </div>
                                <div class="card-body p-3">
                                    {{-- Search and Filter --}}
                                    <div class="mb-3">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                    <input type="text" class="form-control" id="quizAttemptsSearch" placeholder="Cari nama user atau kuis...">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-select" id="quizAttemptsStatusFilter">
                                                    <option value="all">Semua Status</option>
                                                    <option value="passed">Lulus</option>
                                                    <option value="failed">Tidak Lulus</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-select" id="quizAttemptsQuizFilter">
                                                    <option value="all">Semua Kuis</option>
                                                    @if(isset($quizAttempts) && $quizAttempts->count() > 0)
                                                        @php
                                                            $uniqueQuizzes = $quizAttempts->pluck('quiz.title')->unique()->sort();
                                                        @endphp
                                                        @foreach($uniqueQuizzes as $quizTitle)
                                                            <option value="{{ strtolower($quizTitle) }}">{{ $quizTitle }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="max-height: 600px; overflow-y: auto;" id="quizAttemptsContainer">
                                        @if(isset($quizAttempts) && $quizAttempts->count() > 0)
                                            @php
                                                // Kelompokkan attempts berdasarkan user
                                                $groupedAttempts = $quizAttempts->groupBy('user_id');
                                            @endphp
                                            <div class="row g-3" id="quizAttemptsRow">
                                                @foreach($groupedAttempts as $userId => $userAttempts)
                                                @php
                                                    $user = $userAttempts->first()->user;
                                                    $latestAttempt = $userAttempts->sortByDesc('created_at')->first();
                                                    $score = $latestAttempt->total_questions > 0
                                                        ? round(($latestAttempt->correct_answers / $latestAttempt->total_questions) * 100)
                                                        : 0;
                                                    $passed = $score >= $latestAttempt->quiz->passing_score;
                                                    $attemptCount = $userAttempts->count();
                                                @endphp
                                                <div class="col-12 col-md-6 col-lg-4 quiz-attempt-card" 
                                                     data-user-name="{{ strtolower($user->name) }}"
                                                     data-quiz-title="{{ strtolower($latestAttempt->quiz->title) }}"
                                                     data-status="{{ $passed ? 'passed' : 'failed' }}">
                                                    <div class="card border h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-start mb-3">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="card-title mb-1 fw-bold">
                                                                        <i class="bi bi-person-circle me-2 text-primary"></i>{{ $user->name }}
                                                                    </h6>
                                                                    <p class="text-muted small mb-0">
                                                                        <i class="bi bi-file-earmark-text me-1"></i>{{ $latestAttempt->quiz->title }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <div>
                                                                    <div class="text-muted small mb-1">Skor Terakhir</div>
                                                                    <span class="badge {{ $passed ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2">
                                                                        {{ $score }}%
                                                                    </span>
                                                                </div>
                                                                <div class="text-end">
                                                                    <div class="text-muted small mb-1">Status</div>
                                                                    @if($passed)
                                                                        <span class="badge bg-success">Lulus</span>
                                                                    @else
                                                                        <span class="badge bg-danger">Tidak Lulus</span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="d-grid">
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-outline-info btn-sm w-100 dropdown-toggle" 
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="bi bi-list-ul me-1"></i>
                                                                        {{ $attemptCount }}x Pengerjaan
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end w-100" style="min-width: 100%; max-height: 500px; overflow-y: auto;">
                                                                        <li class="dropdown-header">
                                                                            <div class="fw-bold">Riwayat Pengerjaan: {{ $user->name }}</div>
                                                                            <small class="text-muted">Total: {{ $attemptCount }}x</small>
                                                                        </li>
                                                                        <li><hr class="dropdown-divider"></li>
                                                                        @foreach($userAttempts->sortByDesc('created_at') as $attempt)
                                                                            @php
                                                                                $attemptScore = $attempt->total_questions > 0
                                                                                    ? round(($attempt->correct_answers / $attempt->total_questions) * 100)
                                                                                    : 0;
                                                                                $attemptPassed = $attemptScore >= $attempt->quiz->passing_score;
                                                                            @endphp
                                                                            <li>
                                                                                <a class="dropdown-item py-2" href="{{ route('quizzes.result', $attempt) }}">
                                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                                        <div class="flex-grow-1">
                                                                                            <div class="fw-semibold mb-1">Pengerjaan #{{ $loop->iteration }}</div>
                                                                                            <div class="text-muted small">
                                                                                                <i class="bi bi-calendar3 me-1"></i>{{ $attempt->created_at->format('d/m/Y') }}
                                                                                                <i class="bi bi-clock ms-2 me-1"></i>{{ $attempt->created_at->format('H:i') }}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="text-end ms-2">
                                                                                            <span class="badge {{ $attemptPassed ? 'bg-success' : 'bg-danger' }} d-block mb-1">
                                                                                                {{ $attemptScore }}%
                                                                                            </span>
                                                                                            @if($attemptPassed)
                                                                                                <small class="text-success"><i class="bi bi-check-circle"></i> Lulus</small>
                                                                                            @else
                                                                                                <small class="text-danger"><i class="bi bi-x-circle"></i> Tidak Lulus</small>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                            @if(!$loop->last)
                                                                                <li><hr class="dropdown-divider"></li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4 text-muted">
                                                <i class="bi bi-clipboard-x display-6"></i>
                                                <p class="mt-2 mb-0">Belum ada user yang mengerjakan kuis</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endcan
    </div>
@endsection



@push('scripts')
    <!-- PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Set PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Progress tracking variables
        let startTime = Date.now();
        let sessionStartTime = Date.now();
        let currentProgress = {{ $progress->progress ?? 0 }};
        let lastSavedProgress = currentProgress;
        let maxScrollProgress = currentProgress;
        const lessonId = {{ $lesson->id }};
        const progressUrl = '{{ route('lessons.progress', $lesson) }}';
        const csrfToken = '{{ csrf_token() }}';

        // PDF state
        let pdfDoc = null;
        let totalPages = 0;
        let renderedPages = 0;

        // Update time display every 10 seconds
        setInterval(function () {
            const sessionTime = Math.floor((Date.now() - sessionStartTime) / 1000);
            const totalMinutes = Math.floor(({{ ($progress->time_spent ?? 0) }} + sessionTime) / 60);
            const el = document.getElementById('timeSpent');
            if (el) el.textContent = totalMinutes + ' menit';
        }, 10000);

        // Track scroll on PDF container
        function trackScroll() {
            const container = document.getElementById('pdfViewerContainer');
            if (!container) return;

            const scrollTop = container.scrollTop;
            const scrollHeight = container.scrollHeight - container.clientHeight;

            if (scrollHeight > 0) {
                const scrollPercent = Math.round((scrollTop / scrollHeight) * 100);

                if (scrollPercent > maxScrollProgress) {
                    maxScrollProgress = scrollPercent;
                    updateProgress(scrollPercent);
                }

                // Update page indicator
                if (totalPages > 0) {
                    const currentPage = Math.ceil((scrollPercent / 100) * totalPages) || 1;
                    const pageIndicator = document.getElementById('pageIndicator');
                    if (pageIndicator) {
                        pageIndicator.textContent = 'Halaman ' + currentPage + ' / ' + totalPages;
                    }
                }
            }
        }

        // Update progress UI
        function updateProgress(progress) {
            progress = Math.min(100, Math.max(0, Math.round(progress)));

            if (progress > currentProgress) {
                currentProgress = progress;

                const percentEl = document.getElementById('progressPercent');
                const barEl = document.getElementById('progressBar');
                const statusEl = document.getElementById('statusText');

                if (percentEl) percentEl.textContent = progress + '%';
                if (barEl) barEl.style.width = progress + '%';

                if (progress >= 100) {
                    if (statusEl) statusEl.textContent = 'Selesai';
                    if (barEl) {
                        barEl.classList.remove('bg-primary');
                        barEl.classList.add('bg-success');
                    }
                }
            }
        }

        // Save progress to server
        function saveProgress(callback) {
            if (currentProgress <= lastSavedProgress) return;

            const timeSpent = Math.floor((Date.now() - startTime) / 1000);

            fetch(progressUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    progress: currentProgress,
                    time_spent: timeSpent
                })
            }).then(response => response.json())
                .then(data => {
                    lastSavedProgress = currentProgress;
                    startTime = Date.now();
                    if (callback) callback(data);
                }).catch(err => console.log('Progress save error:', err));
        }

        // Auto-save progress every 10 seconds
        setInterval(saveProgress, 10000);

        // Save before leaving page
        window.addEventListener('beforeunload', function () {
            if (currentProgress > lastSavedProgress) {
                const data = JSON.stringify({
                    progress: currentProgress,
                    time_spent: Math.floor((Date.now() - startTime) / 1000)
                });
                navigator.sendBeacon(progressUrl + '?_token=' + csrfToken, new Blob([data], { type: 'application/json' }));
            }
        });

        // Render PDF page
        async function renderPage(page, pageNum) {
            const scale = 1.5;
            const viewport = page.getViewport({ scale });

            const canvas = document.createElement('canvas');
            canvas.className = 'pdf-page';
            canvas.style.marginBottom = '10px';
            canvas.style.boxShadow = '0 2px 8px rgba(0,0,0,0.3)';
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            const context = canvas.getContext('2d');

            await page.render({
                canvasContext: context,
                viewport: viewport
            }).promise;

            document.getElementById('pdfPages').appendChild(canvas);
            renderedPages++;

            // Update page indicator after first page
            if (renderedPages === 1) {
                const pageIndicator = document.getElementById('pageIndicator');
                if (pageIndicator) {
                    pageIndicator.textContent = 'Halaman 1 / ' + totalPages;
                }
            }
        }

        // Load PDF
        @if($lesson->file_type === 'pdf')
            document.addEventListener('DOMContentLoaded', async function () {
                const pdfUrl = '{{ asset('storage/' . $lesson->file_path) }}';
                const container = document.getElementById('pdfViewerContainer');

                try {
                    // Show loading
                    document.getElementById('pdfPages').innerHTML = '<div class="text-white text-center py-5"><div class="spinner-border"></div><p class="mt-2">Memuat PDF...</p></div>';

                    pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
                    totalPages = pdfDoc.numPages;

                    // Clear loading
                    document.getElementById('pdfPages').innerHTML = '';

                    // Render all pages
                    for (let i = 1; i <= totalPages; i++) {
                        const page = await pdfDoc.getPage(i);
                        await renderPage(page, i);
                    }

                    // Add scroll listener
                    container.addEventListener('scroll', trackScroll);

                    // Initial scroll check
                    trackScroll();

                } catch (error) {
                    console.error('Error loading PDF:', error);
                    document.getElementById('pdfPages').innerHTML = '<div class="text-white text-center py-5"><i class="bi bi-exclamation-triangle display-4"></i><p class="mt-2">Gagal memuat PDF. Silakan download file.</p></div>';
                }
            });
        @else
            // For MP4 video, track based on video playback progress
            document.addEventListener('DOMContentLoaded', function () {
                const video = document.getElementById('videoPlayer');
                if (!video) return;

                let maxWatchedTime = 0;
                let hasResumed = false;

                // Format time as MM:SS
                function formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = Math.floor(seconds % 60);
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                }

                // Initial setup
                function initVideo() {
                    const duration = video.duration;
                    if (isNaN(duration)) return;
                    document.getElementById('videoTime').textContent = '00:00 / ' + formatTime(duration);

                    // Initialize maxWatchedTime based on current progress percentage
                    if (currentProgress > 0) {
                        maxWatchedTime = (currentProgress / 100) * duration;
                    }
                }

                if (video.readyState >= 1) {
                    initVideo();
                } else {
                    video.addEventListener('loadedmetadata', initVideo);
                }

                // Track video progress
                video.addEventListener('timeupdate', function () {
                    const currentTime = video.currentTime;
                    const duration = video.duration;

                    // Update time display
                    const badge = document.getElementById('videoTime');
                    if (badge) {
                        badge.textContent = formatTime(currentTime) + ' / ' + formatTime(duration);
                    }

                    // Track max watched time (prevent skipping ahead to cheat progress)
                    if (currentTime > maxWatchedTime && currentTime <= maxWatchedTime + 5) {
                        maxWatchedTime = currentTime;
                    }
                    // Allow revisiting parts already watched
                    else if (currentTime < maxWatchedTime) {
                        // Do nothing, just playing previously watched part
                    }
                    // If user seeks way ahead of maxWatchedTime, we don't update maxWatchedTime
                    // This effectively prevents progress cheating

                    // Calculate progress based on watched time
                    if (duration > 0) {
                        const watchedPercent = Math.round((maxWatchedTime / duration) * 100);
                        if (watchedPercent > currentProgress) {
                            updateProgress(watchedPercent);
                        }
                    }
                });

                // Mark complete when video ends
                video.addEventListener('ended', function () {
                    updateProgress(100);
                });

                // Track initial load
                video.addEventListener('canplay', function () {
                    if (currentProgress > 0 && video.duration > 0) {
                        maxWatchedTime = Math.max(maxWatchedTime, (currentProgress / 100) * video.duration);
                    }
                });
            });
        @endif

        // Search and Filter Functions
        // User dengan Progress
        const progressSearch = document.getElementById('progressSearch');
        const progressFilter = document.getElementById('progressFilter');
        const progressTableBody = document.getElementById('progressTableBody');
        const progressCount = document.getElementById('progressCount');

        function filterProgress() {
            if (!progressTableBody) return;
            
            const searchTerm = progressSearch ? progressSearch.value.toLowerCase() : '';
            const filterStatus = progressFilter ? progressFilter.value : 'all';
            const rows = progressTableBody.querySelectorAll('tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const status = row.getAttribute('data-status') || '';
                const matchesSearch = name.includes(searchTerm);
                const matchesFilter = filterStatus === 'all' || status === filterStatus;

                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (progressCount) {
                progressCount.textContent = visibleCount;
            }
        }

        if (progressSearch) {
            progressSearch.addEventListener('input', filterProgress);
        }
        if (progressFilter) {
            progressFilter.addEventListener('change', filterProgress);
        }

        // User Belum Mulai
        const noProgressSearch = document.getElementById('noProgressSearch');
        const noProgressTableBody = document.getElementById('noProgressTableBody');
        const noProgressCount = document.getElementById('noProgressCount');

        function filterNoProgress() {
            if (!noProgressTableBody) return;
            
            const searchTerm = noProgressSearch ? noProgressSearch.value.toLowerCase() : '';
            const rows = noProgressTableBody.querySelectorAll('tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);

                if (matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noProgressCount) {
                noProgressCount.textContent = visibleCount;
            }
        }

        if (noProgressSearch) {
            noProgressSearch.addEventListener('input', filterNoProgress);
        }

        // Riwayat Pengerjaan Kuis
        const quizAttemptsSearch = document.getElementById('quizAttemptsSearch');
        const quizAttemptsStatusFilter = document.getElementById('quizAttemptsStatusFilter');
        const quizAttemptsQuizFilter = document.getElementById('quizAttemptsQuizFilter');
        const quizAttemptsRow = document.getElementById('quizAttemptsRow');
        const quizAttemptsCount = document.getElementById('quizAttemptsCount');

        function filterQuizAttempts() {
            if (!quizAttemptsRow) return;
            
            const searchTerm = quizAttemptsSearch ? quizAttemptsSearch.value.toLowerCase() : '';
            const filterStatus = quizAttemptsStatusFilter ? quizAttemptsStatusFilter.value : 'all';
            const filterQuiz = quizAttemptsQuizFilter ? quizAttemptsQuizFilter.value : 'all';
            const cards = quizAttemptsRow.querySelectorAll('.quiz-attempt-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const userName = card.getAttribute('data-user-name') || '';
                const quizTitle = card.getAttribute('data-quiz-title') || '';
                const status = card.getAttribute('data-status') || '';
                
                const matchesSearch = userName.includes(searchTerm) || quizTitle.includes(searchTerm);
                const matchesStatus = filterStatus === 'all' || status === filterStatus;
                const matchesQuiz = filterQuiz === 'all' || quizTitle === filterQuiz;

                if (matchesSearch && matchesStatus && matchesQuiz) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (quizAttemptsCount) {
                quizAttemptsCount.textContent = visibleCount;
            }
        }

        if (quizAttemptsSearch) {
            quizAttemptsSearch.addEventListener('input', filterQuizAttempts);
        }
        if (quizAttemptsStatusFilter) {
            quizAttemptsStatusFilter.addEventListener('change', filterQuizAttempts);
        }
        if (quizAttemptsQuizFilter) {
            quizAttemptsQuizFilter.addEventListener('change', filterQuizAttempts);
        }
    </script>
@endpush