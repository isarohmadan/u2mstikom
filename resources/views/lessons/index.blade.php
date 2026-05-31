@extends('layouts.app')

@section('title', 'Pembelajaran')

@section('navigation')
    @include('fragments.navigation')
@endsection

@push('styles')
<style>
    .action-btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
        border: 1px solid;
    }
    .action-btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .avatar-initial {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        font-size: 14px;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }
    .table-row-hover {
        transition: all 0.2s ease;
    }
    .table-row-hover:hover {
        background-color: #f8f9fa;
    }
    .filter-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-book me-2 text-primary"></i>Pembelajaran
                </h4>
                <p class="text-muted mb-0">Kelola materi pembelajaran dan kuis</p>
            </div>
            @can('lessons.create')
                <a href="{{ route('lessons.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Materi
                </a>
            @endcan
        </div>

        {{-- Admin Statistics Dashboard --}}
        @can('lessons.manage')
            @if($stats)
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-white-50">Total Materi</h6>
                                        <h2 class="mb-0">{{ $stats['total_lessons'] }}</h2>
                                        <small>{{ $stats['published_lessons'] }} dipublikasi</small>
                                    </div>
                                    <i class="bi bi-book display-4 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-white-50">User Belajar</h6>
                                        <h2 class="mb-0">{{ $stats['total_users_learning'] }}</h2>
                                        <small>{{ $stats['completed_lessons'] }} selesai baca</small>
                                    </div>
                                    <i class="bi bi-people display-4 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-white-50">Kuis Dikerjakan</h6>
                                        <h2 class="mb-0">{{ $stats['total_quiz_attempts'] }}</h2>
                                        <small>{{ $stats['passed_quizzes'] }} lulus</small>
                                    </div>
                                    <i class="bi bi-clipboard-check display-4 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-1 text-muted">Rata-rata Progress</h6>
                                        <h2 class="mb-0">{{ $stats['avg_progress'] }}%</h2>
                                        <small>{{ floor($stats['total_time_spent'] / 3600) }}j
                                            {{ floor(($stats['total_time_spent'] % 3600) / 60) }}m total</small>
                                    </div>
                                    <i class="bi bi-graph-up display-4 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-book me-2"></i>Daftar Materi Pembelajaran
                    </h5>
                    <span class="badge bg-primary">Total: {{ $lessons->total() }}</span>
                </div>
            </div>
            
            <div class="card-body">
                {{-- Search and Filter --}}
                <form method="GET" action="{{ route('lessons.index') }}" id="filterForm">
                    <div class="filter-card">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="bi bi-search me-1"></i>Cari Materi
                                </label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control text-xs border-start-0" 
                                           name="search" 
                                           placeholder="Cari judul, deskripsi, atau nama file..." 
                                           value="{{ request('search') }}"
                                           id="searchInput">
                                    @if(request()->has('search'))
                                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('searchInput').value=''; document.getElementById('filterForm').submit();">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="bi bi-funnel me-1"></i>Status
                                </label>
                                <select class="form-select form-select-lg shadow-sm" name="status" id="statusFilter">
                                    <option value="">Semua</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>✅ Dipublikasikan</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>📝 Draft</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="bi bi-folder me-1"></i>Kategori
                                </label>
                                <select class="form-select form-select-lg shadow-sm" name="category_id" id="categoryFilter">
                                    <option value="">Semua</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="bi bi-sort-down me-1"></i>Urutkan
                                </label>
                                <select class="form-select form-select-lg shadow-sm" name="sort" id="sortFilter">
                                    <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>🕒 Terbaru</option>
                                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>🕐 Terlama</option>
                                    <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>🔤 A-Z</option>
                                    <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>🔤 Z-A</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 shadow-sm" style="height: 48px;">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="30%">Materi</th>
                                <th width="15%">Penulis</th>
                                <th width="20%">Konten</th>
                                <th width="12%">Status</th>
                                <th width="13%">Kuis & Progress</th>
                                <th width="10%">Dibuat</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lessons as $lesson)
                            <tr class="table-row-hover">
                                <td class="text-center">
                                    <span class="badge bg-light text-dark rounded-pill">{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold mb-1">
                                        <a href="{{ route('lessons.show', $lesson) }}" class="text-decoration-none text-dark">
                                            {{ Str::limit($lesson->title, 50) }}
                                        </a>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        @if($lesson->file_type === 'pdf')
                                            <i class="bi bi-file-pdf text-danger me-1"></i>
                                        @else
                                            <i class="bi bi-play-circle text-primary me-1"></i>
                                        @endif
                                        <code class="small">{{ Str::limit($lesson->file_name, 30) }}</code>
                                    </div>
                                    @if($lesson->category)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-tag me-1"></i>{{ $lesson->category->name }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($lesson->creator)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initial bg-primary text-white me-2">
                                                {{ strtoupper(substr($lesson->creator->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium small">{{ Str::limit($lesson->creator->name, 20) }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lesson->description)
                                        <div class="text-truncate small text-muted" style="max-width: 200px;">
                                            {{ Str::limit($lesson->description, 60) }}
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">Tidak ada deskripsi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lesson->is_published)
                                        <span class="badge bg-success status-badge">
                                            <i class="bi bi-check-circle me-1"></i>Dipublikasikan
                                        </span>
                                    @else
                                        <span class="badge bg-warning status-badge">
                                            <i class="bi bi-clock-history me-1"></i>Draft
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <span class="badge bg-info">
                                            <i class="bi bi-question-circle me-1"></i>{{ $lesson->quizzes->count() }} Kuis
                                        </span>
                                    </div>
                                    @php
                                        $lessonProgress = $userProgress[$lesson->id] ?? null;
                                        $progressPercent = $lessonProgress ? $lessonProgress->progress : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar {{ $progressPercent >= 100 ? 'bg-success' : 'bg-primary' }}"
                                                role="progressbar" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <small class="fw-bold {{ $progressPercent >= 100 ? 'text-success' : '' }}">
                                            {{ $progressPercent }}%
                                            @if($lessonProgress && $lessonProgress->is_completed)
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="fw-medium">{{ $lesson->created_at->format('d M Y') }}</div>
                                        <div class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $lesson->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('lessons.show', $lesson) }}" 
                                           class="btn btn-outline-info action-btn-icon" 
                                           data-bs-toggle="tooltip" 
                                           title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @can('lessons.edit')
                                        <a href="{{ route('lessons.edit', $lesson) }}" 
                                           class="btn btn-outline-warning action-btn-icon" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit Materi">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('lessons.delete')
                                        <form action="{{ route('lessons.destroy', $lesson) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger action-btn-icon" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                                        <h5 class="text-muted mb-2">Belum ada materi pembelajaran</h5>
                                        <p class="text-muted small mb-3">Mulai dengan membuat materi pertama Anda</p>
                                        @can('lessons.create')
                                        <a href="{{ route('lessons.create') }}" class="btn btn-primary shadow-sm">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Materi Pertama
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($lessons->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div class="text-muted small mb-2 mb-md-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Menampilkan <strong>{{ $lessons->firstItem() }}</strong> - <strong>{{ $lessons->lastItem() }}</strong> dari <strong>{{ $lessons->total() }}</strong> materi
                    </div>
                    <nav aria-label="Page navigation">
                        {{ $lessons->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle enter key in search input
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    
    if (searchInput && filterForm) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });
    }
});
</script>
@endpush

@endsection