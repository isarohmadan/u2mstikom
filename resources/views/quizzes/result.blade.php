@extends('layouts.app')

@section('title', 'Hasil Kuis - ' . $attempt->quiz->title)

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('lessons.show', $attempt->quiz->lesson) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Materi
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Result Summary -->
                <div class="card shadow-sm mb-4 {{ $attempt->is_passed ? 'border-success' : 'border-danger' }}">
                    <div
                        class="card-header {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }} text-white text-center py-4">
                        @if($attempt->is_passed)
                            <i class="bi bi-trophy display-3 mb-2"></i>
                            <h3 class="mb-0">Selamat! Anda Lulus</h3>
                        @else
                            <i class="bi bi-x-circle display-3 mb-2"></i>
                            <h3 class="mb-0">Maaf, Anda Belum Lulus</h3>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row text-center py-4">
                            <div class="col-md-4 border-end">
                                <h2 class="mb-0 {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                    {{ $attempt->score }}%</h2>
                                <p class="text-muted mb-0">Nilai Anda</p>
                            </div>
                            <div class="col-md-4 border-end">
                                <h2 class="mb-0">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</h2>
                                <p class="text-muted mb-0">Jawaban Benar</p>
                            </div>
                            <div class="col-md-4">
                                <h2 class="mb-0">{{ $attempt->quiz->passing_score }}%</h2>
                                <p class="text-muted mb-0">Nilai Minimum</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Kuis:</strong> {{ $attempt->quiz->title }}</p>
                                <p class="mb-1"><strong>Materi:</strong> {{ $attempt->quiz->lesson->title }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Mulai:</strong> {{ $attempt->started_at->format('d M Y H:i') }}</p>
                                <p class="mb-1"><strong>Selesai:</strong> {{ $attempt->completed_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        @if($attempt->quiz->allow_retry && !$attempt->is_passed)
                            <div class="text-center mt-4">
                                <a href="{{ route('quizzes.take', $attempt->quiz) }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i> Coba Lagi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Answer Review -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Review Jawaban</h5>
                    </div>
                    <div class="card-body">
                        @foreach($attempt->quiz->questions as $index => $question)
                            @php
                                $userAnswer = $attempt->answers[$question->id] ?? null;
                                $isCorrect = $userAnswer && strtolower($userAnswer) === strtolower($question->correct_answer);
                            @endphp
                            <div class="card mb-3 {{ $isCorrect ? 'border-success' : 'border-danger' }}">
                                <div class="card-header {{ $isCorrect ? 'bg-success' : 'bg-danger' }} text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><strong>Soal {{ $index + 1 }}</strong></span>
                                        @if($isCorrect)
                                            <span class="badge bg-white text-success"><i class="bi bi-check-circle"></i>
                                                Benar</span>
                                        @else
                                            <span class="badge bg-white text-danger"><i class="bi bi-x-circle"></i> Salah</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3"><strong>{{ $question->question }}</strong></p>

                                    @foreach(['a', 'b', 'c', 'd'] as $option)
                                        @php
                                            $optionField = 'option_' . $option;
                                            $optionValue = $question->$optionField;
                                        @endphp
                                        @if($optionValue)
                                            <div
                                                class="mb-2 p-2 rounded {{ $option === $question->correct_answer ? 'bg-success bg-opacity-25' : ($userAnswer === $option && !$isCorrect ? 'bg-danger bg-opacity-25' : 'bg-light') }}">
                                                <strong>{{ strtoupper($option) }}.</strong> {{ $optionValue }}
                                                @if($option === $question->correct_answer)
                                                    <span class="badge bg-success float-end"><i class="bi bi-check"></i> Jawaban
                                                        Benar</span>
                                                @elseif($userAnswer === $option)
                                                    <span class="badge bg-danger float-end"><i class="bi bi-x"></i> Jawaban Anda</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection