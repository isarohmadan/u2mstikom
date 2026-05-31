@extends('layouts.app')

@section('title', 'Edit Materi')

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Materi Pembelajaran</h5>
                <a href="{{ route('lessons.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Materi <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title', $lesson->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="3">{{ old('description', $lesson->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                            name="category_id">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $lesson->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($lesson->file_name)
                        <div class="mb-3">
                            <label class="form-label">File Saat Ini</label>
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                @if($lesson->file_type === 'pdf')
                                    <i class="bi bi-file-pdf text-danger fs-4 me-2"></i>
                                @else
                                    <i class="bi bi-play-circle text-primary fs-4 me-2"></i>
                                @endif
                                <span>{{ $lesson->file_name }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="file" class="form-label">{{ $lesson->file_name ? 'Ganti File (Opsional)' : 'File Materi' }} <span class="text-danger">{{ $lesson->file_name ? '' : '*' }}</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                            name="file" accept=".pdf,.mp4" {{ $lesson->file_name ? '' : 'required' }}>
                        <div class="form-text">Format yang diizinkan: PDF, MP4 (Video). Maksimal 100MB.</div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_published" name="is_published"
                                value="1" {{ old('is_published', $lesson->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                Publikasikan sekarang (file akan langsung terlihat di halaman pembelajaran)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('lessons.index') }}" class="btn btn-secondary">
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