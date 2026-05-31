@extends('layouts.app')

@section('navigation')
    @include('fragments.navigation')
@endsection

@push('styles')
<style>
    .filter-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>Dokumen Template
            </h4>
            <p class="text-muted mb-0">Kelola dokumen template untuk Knowledge Management System</p>
        </div>
        @can('templates.create')
            <a href="{{ route('templates.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Buat Template
            </a>
        @endcan
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif



    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-file-earmark-text me-2"></i>Daftar Template
                </h5>
                <span class="badge bg-primary">Total: {{ $templates->total() }}</span>
            </div>
        </div>
        <div class="card-body">
            {{-- Search and Filter --}}
            <form method="GET" action="{{ route('templates.index') }}" id="filterForm">
                <div class="filter-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-search me-1"></i>Cari Template
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control text-xs border-start-0" 
                                       name="search" 
                                       placeholder="Cari nama, slug, atau deskripsi..." 
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
                                <i class="bi bi-funnel me-1"></i>Versi Min
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg shadow-sm" 
                                   name="version_min" 
                                   placeholder="Min" 
                                   value="{{ request('version_min') }}"
                                   min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-funnel me-1"></i>Versi Max
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg shadow-sm" 
                                   name="version_max" 
                                   placeholder="Max" 
                                   value="{{ request('version_max') }}"
                                   min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-sort-down me-1"></i>Urutkan
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="sort_by" id="sortBy">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                                <option value="latest_version_number" {{ request('sort_by') == 'latest_version_number' ? 'selected' : '' }}>Versi</option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                                <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Terakhir Diupdate</option>
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
        </div>
        <div class="card-body p-0">
            <!-- Header -->
            <div class="row g-0 border-bottom bg-light fw-semibold text-muted small">
                <div class="col-md-3 px-4 py-3">Nama</div>
                <div class="col-md-3 px-4 py-3">Deskripsi</div>
                <div class="col-md-2 px-4 py-3 text-center">Versi Terbaru</div>
                <div class="col-md-2 px-4 py-3">Diupdate</div>
                <div class="col-md-2 px-4 py-3 text-center">Aksi</div>
            </div>
            
            <!-- Content -->
            @forelse($templates as $t)
                <div class="row g-0 border-bottom hover-row align-items-center">
                    <div class="col-md-3 px-4 py-3">
                        <span class="fw-medium">{{ $t->name }}</span>
                    </div>
                    <div class="col-md-3 px-4 py-3">
                        <span class="text-muted">{{ $t->description ?? '-' }}</span>
                    </div>
                    <div class="col-md-2 px-4 py-3 text-center">
                        <span class="badge bg-primary">{{ $t->latest_version_number ?? '-' }}</span>
                    </div>
                    <div class="col-md-2 px-4 py-3">
                        <small class="text-muted">{{ $t->updated_at?->diffForHumans() ?? '-' }}</small>
                    </div>
                    <div class="col-md-2 px-4 py-3 text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown{{ $t->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i> Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown{{ $t->id }}">
                                @if($t->latestVersion)
                                <li>
                                    <a class="dropdown-item" href="{{ route('templates.download', [$t, $t->latestVersion]) }}">
                                        <i class="bi bi-download me-2"></i> Unduh
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('templates.show', $t) }}">
                                        <i class="bi bi-eye me-2"></i> Lihat
                                    </a>
                                </li>
                                @can('templates.upload')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadVersionModal{{ $t->id }}">
                                            <i class="bi bi-upload me-2"></i> Upload Versi
                                        </a>
                                    </li>
                                @endcan
                                @can('templates.delete')
                                    <li>
                                        <form action="{{ route('templates.delete', $t) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?');" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                @endcan
                            </ul>
                        </div>

                        @can('templates.upload')
                        <!-- Modal Upload Versi -->
                        <div class="modal fade" id="uploadVersionModal{{ $t->id }}" tabindex="-1" aria-labelledby="uploadVersionModalLabel{{ $t->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="uploadVersionModalLabel{{ $t->id }}">Upload Versi Baru - {{ $t->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <form action="{{ route('templates.version.upload', $t) }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">File (pdf/docx/pptx/xlsx)</label>
                                                <input type="file" name="file" class="form-control" accept=".pdf,.docx,.pptx,.xlsx" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Upload Versi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">
                        @if(request()->hasAny(['search', 'version_min', 'version_max']))
                            Tidak ada template yang sesuai dengan filter
                        @else
                            Tidak ada template tersedia
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'version_min', 'version_max']))
                        <a href="{{ route('templates.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Hapus Filter
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
        @if($templates->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Menampilkan <strong>{{ $templates->firstItem() }}</strong> sampai <strong>{{ $templates->lastItem() }}</strong> dari <strong>{{ $templates->total() }}</strong> template
                    </div>
                    <nav aria-label="Navigasi halaman">
                        {{ $templates->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        @endif
    </div>

@push('styles')
<style>
    .hover-row {
        transition: background-color 0.15s ease-in-out;
    }
    .hover-row:hover {
        background-color: #f8f9fa;
    }
    .dropdown-menu {
        position: absolute !important;
        min-width: 180px;
    }
    .pagination {
        margin-bottom: 0;
    }
    .pagination .page-link {
        color: #495057;
        border-color: #dee2e6;
        padding: 0.5rem 0.75rem;
    }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .pagination .page-link:hover {
        color: #0d6efd;
        background-color: #e9ecef;
    }
    @media (max-width: 767.98px) {
        .card-footer {
            padding: 0.75rem !important;
        }
        .pagination {
            font-size: 0.875rem;
        }
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when dropdowns change
        const sortBy = document.getElementById('sortBy');
        const sortOrder = document.getElementById('sortOrder');
        const filterForm = document.getElementById('filterForm');

        if (sortBy && filterForm) {
            sortBy.addEventListener('change', function() {
                filterForm.submit();
            });
        }

        if (sortOrder && filterForm) {
            sortOrder.addEventListener('change', function() {
                filterForm.submit();
            });
        }

        // Handle enter key in search input
        const searchInput = document.getElementById('searchInput');
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
