@extends('layouts.app')

@section('title', 'Edit Kuis - ' . $quiz->title)

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Edit Kuis</h5>
                    <small class="text-muted">Untuk materi: {{ $quiz->lesson->title }}</small>
                </div>
                <a href="{{ route('lessons.show', $quiz->lesson) }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('quizzes.update', $quiz) }}" method="POST" id="quizForm">
                    @csrf
                    @method('PUT')

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Informasi Kuis</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Judul Kuis <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title', $quiz->title) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passing_score" class="form-label">Nilai Lulus (%) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="passing_score" name="passing_score"
                                        value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="time_limit" class="form-label">Batas Waktu (menit)</label>
                                    <input type="number" class="form-control" id="time_limit" name="time_limit"
                                        value="{{ old('time_limit', $quiz->time_limit) }}" min="1"
                                        placeholder="Kosongkan jika tidak ada batas">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="{{ old('description', $quiz->description) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="allow_retry" name="allow_retry"
                                            value="1" {{ old('allow_retry', $quiz->allow_retry) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_retry">
                                            Izinkan mengulang kuis
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_published"
                                            name="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">
                                            Publikasikan sekarang
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Soal Kuis</h5>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addQuestion()">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Soal
                            </button>
                        </div>
                        <div class="card-body" id="questionsContainer">
                            <!-- Existing questions will be loaded here -->
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('lessons.show', $quiz->lesson) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let questionCount = 0;
        const existingQuestions = @json($quiz->questions);

        function addQuestion(data = null) {
            const container = document.getElementById('questionsContainer');
            const html = `
            <div class="question-item border rounded p-3 mb-3" id="question_${questionCount}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Soal #${questionCount + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(${questionCount})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                ${data ? `<input type="hidden" name="questions[${questionCount}][id]" value="${data.id}">` : ''}
                <div class="mb-3">
                    <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="questions[${questionCount}][question]" rows="2" required>${data ? data.question : ''}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opsi A <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="questions[${questionCount}][option_a]" value="${data ? data.option_a : ''}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opsi B <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="questions[${questionCount}][option_b]" value="${data ? data.option_b : ''}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opsi C</label>
                        <input type="text" class="form-control" name="questions[${questionCount}][option_c]" value="${data ? (data.option_c || '') : ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opsi D</label>
                        <input type="text" class="form-control" name="questions[${questionCount}][option_d]" value="${data ? (data.option_d || '') : ''}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jawaban Benar <span class="text-danger">*</span></label>
                        <select class="form-select" name="questions[${questionCount}][correct_answer]" required>
                            <option value="a" ${data && data.correct_answer === 'a' ? 'selected' : ''}>A</option>
                            <option value="b" ${data && data.correct_answer === 'b' ? 'selected' : ''}>B</option>
                            <option value="c" ${data && data.correct_answer === 'c' ? 'selected' : ''}>C</option>
                            <option value="d" ${data && data.correct_answer === 'd' ? 'selected' : ''}>D</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Poin</label>
                        <input type="number" class="form-control" name="questions[${questionCount}][points]" value="${data ? data.points : 1}" min="1">
                    </div>
                </div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', html);
            questionCount++;
        }

        function removeQuestion(id) {
            const element = document.getElementById(`question_${id}`);
            if (element) {
                element.remove();
            }
        }

        // Load existing questions on page load
        document.addEventListener('DOMContentLoaded', function () {
            existingQuestions.forEach(q => addQuestion(q));
            if (existingQuestions.length === 0) {
                addQuestion();
            }
        });
    </script>
@endpush