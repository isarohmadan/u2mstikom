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
                <i class="bi bi-tags me-2 text-primary"></i>Kategori
            </h4>
            <p class="text-muted mb-0">Kelola kategori untuk topik diskusi</p>
        </div>
        @can('categories.create')
            <a href="{{ route('categories.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Buat Kategori
            </a>
        @endcan
    </div>

    {{-- Alert Messages --}}
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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-tags me-2"></i>Daftar Kategori
                </h5>
                <span class="badge bg-primary">Total: {{ $categories->total() }}</span>
            </div>
        </div>
        <div class="card-body">
            {{-- Search and Filter --}}
            <form method="GET" action="{{ route('categories.index') }}" id="filterForm">
                <div class="filter-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-search me-1"></i>Cari Kategori
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
                                <i class="bi bi-funnel me-1"></i>Topik Min
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg shadow-sm" 
                                   name="topics_min" 
                                   placeholder="Min" 
                                   value="{{ request('topics_min') }}"
                                   min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-funnel me-1"></i>Topik Max
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg shadow-sm" 
                                   name="topics_max" 
                                   placeholder="Max" 
                                   value="{{ request('topics_max') }}"
                                   min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-sort-down me-1"></i>Urutkan
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="sort_by" id="sortBy">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                                <option value="topics_count" {{ request('sort_by') == 'topics_count' ? 'selected' : '' }}>Jumlah Topik</option>
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
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">Nama</th>
                        <th class="px-4 py-3 fw-semibold d-none d-md-table-cell">Slug</th>
                        <th class="px-4 py-3 fw-semibold text-center">Topik</th>
                        <th class="px-4 py-3 fw-semibold d-none d-lg-table-cell">Diupdate</th>
                        <th class="px-4 py-3 fw-semibold text-center" style="width: 120px;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-4 py-3">
                                <div>
                                    <span class="fw-medium">{{ $category->name }}</span>
                                    @if($category->description)
                                        <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($category->description, 50) }}</small>
                                    @endif
                                    <div class="d-md-none mt-1">
                                        <code class="text-muted small">{{ $category->slug }}</code>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 d-none d-md-table-cell">
                                <code class="text-muted small">{{ $category->slug }}</code>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-secondary">{{ $category->topics_count ?? 0 }}</span>
                            </td>
                            <td class="px-4 py-3 d-none d-lg-table-cell">
                                <small class="text-muted">{{ $category->updated_at->diffForHumans() }}</small>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown{{ $category->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i> Aksi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown{{ $category->id }}">
                                        @can('categories.view')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('categories.show', $category) }}">
                                                    <i class="bi bi-eye me-2"></i> Lihat
                                                </a>
                                            </li>
                                        @endcan
                                        @can('categories.edit')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('categories.edit', $category) }}">
                                                    <i class="bi bi-pencil me-2"></i> Edit
                                                </a>
                                            </li>
                                        @endcan
                                        @can('categories.delete')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">
                                    @if(request()->hasAny(['search', 'topics_min', 'topics_max']))
                                        Tidak ada kategori yang sesuai dengan filter
                                    @else
                                        Tidak ada kategori tersedia
                                    @endif
                                </p>
                                @if(request()->hasAny(['search', 'topics_min', 'topics_max']))
                                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Hapus Filter
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Menampilkan <strong>{{ $categories->firstItem() }}</strong> sampai <strong>{{ $categories->lastItem() }}</strong> dari <strong>{{ $categories->total() }}</strong> kategori
                    </div>
                    <nav aria-label="Page navigation">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .dropdown-menu {
        position: absolute !important;
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
    .card-footer {
        background-color: #f8f9fa;
    }
    @media (max-width: 767.98px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
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

