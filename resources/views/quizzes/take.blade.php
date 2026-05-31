@extends('layouts.app')

@section('title', $quiz->title)

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('lessons.show', $quiz->lesson) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Materi
                    </a>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-question-circle me-2"></i>{{ $quiz->title }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Materi:</strong> {{ $quiz->lesson->title }}</p>
                                <p class="mb-1"><strong>Jumlah Soal:</strong> {{ $quiz->questions->count() }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Nilai Lulus:</strong> {{ $quiz->passing_score }}%</p>
                                @if($quiz->time_limit)
                                    <p class="mb-1"><strong>Batas Waktu:</strong> {{ $quiz->time_limit }} menit</p>
                                @else
                                    <p class="mb-1"><strong>Batas Waktu:</strong> Tidak ada</p>
                                @endif
                            </div>
                        </div>

                        @if($quiz->description)
                            <p class="text-muted">{{ $quiz->description }}</p>
                        @endif

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Instruksi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Kuis ini terdiri dari {{ $quiz->questions->count() }} soal pilihan ganda.</li>
                                <li>Nilai minimum untuk lulus adalah {{ $quiz->passing_score }}%.</li>
                                @if($quiz->time_limit)
                                    <li>Anda memiliki waktu {{ $quiz->time_limit }} menit untuk menyelesaikan kuis.</li>
                                @endif
                                @if($quiz->allow_retry)
                                    <li>Anda dapat mengulang kuis ini jika diperlukan.</li>
                                @else
                                    <li>Kuis ini hanya dapat dikerjakan satu kali.</li>
                                @endif
                            </ul>
                        </div>

                        <form action="{{ route('quizzes.submit', $quiz) }}" method="POST" id="quizForm">
                            @csrf
                            <input type="hidden" name="attempt_id" value="{{ $attempt->id ?? $existingAttempt->id }}">

                            @foreach($quiz->questions as $index => $question)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Soal {{ $index + 1 }}</strong>
                                        <span class="badge bg-secondary float-end">{{ $question->points }} poin</span>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">{{ $question->question }}</p>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                                                id="q{{ $question->id }}_a" value="a" required>
                                            <label class="form-check-label" for="q{{ $question->id }}_a">
                                                <strong>A.</strong> {{ $question->option_a }}
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                                                id="q{{ $question->id }}_b" value="b">
                                            <label class="form-check-label" for="q{{ $question->id }}_b">
                                                <strong>B.</strong> {{ $question->option_b }}
                                            </label>
                                        </div>
                                        @if($question->option_c)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                                                    id="q{{ $question->id }}_c" value="c">
                                                <label class="form-check-label" for="q{{ $question->id }}_c">
                                                    <strong>C.</strong> {{ $question->option_c }}
                                                </label>
                                            </div>
                                        @endif
                                        @if($question->option_d)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                                                    id="q{{ $question->id }}_d" value="d">
                                                <label class="form-check-label" for="q{{ $question->id }}_d">
                                                    <strong>D.</strong> {{ $question->option_d }}
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg"
                                    onclick="return confirm('Yakin ingin mengumpulkan jawaban? Pastikan semua soal sudah dijawab.')">
                                    <i class="bi bi-send me-2"></i>Kumpulkan Jawaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if($quiz->time_limit)
    @push('scripts')
        <script>
            (function() {
                // Timer functionality with localStorage persistence
                const attemptId = '{{ $attempt->id ?? $existingAttempt->id }}';
                const storageKey = 'quiz_timer_' + attemptId;
                const totalSeconds = {{ $quiz->time_limit }} * 60;
                
                let isSubmitted = false;
                let timerInterval = null;
                
                // Get remaining time from localStorage or use full time
                let savedTime = localStorage.getItem(storageKey);
                let timeRemaining = savedTime !== null ? parseInt(savedTime) : totalSeconds;
                
                // If saved time is invalid, use full time
                if (isNaN(timeRemaining) || timeRemaining < 0) {
                    timeRemaining = totalSeconds;
                }
                
                // Create timer display
                let timerDisplay = document.createElement('div');
                timerDisplay.id = 'quizTimer';
                timerDisplay.className = 'position-fixed top-0 end-0 m-3 p-3 bg-dark text-white rounded shadow';
                timerDisplay.style.zIndex = '9999';
                document.body.appendChild(timerDisplay);
                
                function formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return mins + ':' + secs.toString().padStart(2, '0');
                }
                
                function submitQuiz() {
                    if (isSubmitted) return;
                    isSubmitted = true;
                    
                    // Clear localStorage
                    localStorage.removeItem(storageKey);
                    
                    // Stop timer
                    if (timerInterval) {
                        clearInterval(timerInterval);
                    }
                    
                    // Update UI
                    timerDisplay.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Waktu Habis! Mengumpulkan...';
                    
                    // Disable submit button
                    const submitBtn = document.querySelector('#quizForm button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengumpulkan...';
                    }
                    
                    // Remove required from all radio buttons
                    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                        radio.removeAttribute('required');
                    });
                    
                    // Submit form
                    document.getElementById('quizForm').submit();
                }
                
                function updateTimer() {
                    if (isSubmitted) return;
                    
                    // Update display
                    timerDisplay.innerHTML = '<i class="bi bi-clock me-2"></i>Waktu: ' + formatTime(timeRemaining);
                    
                    // Warning color when less than 1 minute
                    if (timeRemaining <= 60) {
                        timerDisplay.classList.remove('bg-dark');
                        timerDisplay.classList.add('bg-danger');
                    }
                    
                    // Time's up
                    if (timeRemaining <= 0) {
                        submitQuiz();
                        return;
                    }
                    
                    // Save to localStorage
                    localStorage.setItem(storageKey, timeRemaining);
                    
                    // Decrease time
                    timeRemaining--;
                }
                
                // Start timer when DOM is ready
                document.addEventListener('DOMContentLoaded', function() {
                    // Initial update
                    updateTimer();
                    
                    // Update every second
                    timerInterval = setInterval(updateTimer, 1000);
                    
                    // Handle manual form submission
                    document.getElementById('quizForm').addEventListener('submit', function(e) {
                        if (!isSubmitted) {
                            isSubmitted = true;
                            localStorage.removeItem(storageKey);
                            if (timerInterval) {
                                clearInterval(timerInterval);
                            }
                        }
                    });
                });
                
                // Clean up on page unload
                window.addEventListener('beforeunload', function() {
                    if (!isSubmitted && timeRemaining > 0) {
                        localStorage.setItem(storageKey, timeRemaining);
                    }
                });
            })();
        </script>
    @endpush
@endif