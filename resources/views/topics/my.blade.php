@extends('layouts.app')

@section('title', 'Topik Saya')

@section('navigation')
    @include('fragments.navigation')
@endsection

@push('styles')
<style>
    .topic-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .topic-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-left-color: var(--bs-primary);
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }
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
                <i class="bi bi-person-lines-fill me-2 text-primary"></i>Topik Saya
            </h4>
            <p class="text-muted mb-0">Kelola topik yang Anda buat</p>
        </div>
        <a href="{{ route('topics.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Buat Topik Baru
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-journal-text me-2"></i>Daftar Topik Anda
                </h5>
                <span class="badge bg-primary">Total: {{ $query->total() }}</span>
            </div>
        </div>
        
        <div class="card-body">
            {{-- Search and Filter --}}
            <form method="GET" action="{{ route('topics.my') }}" id="filterForm">
                <div class="filter-card">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-search me-1"></i>Cari Topik
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control text-xs border-start-0" 
                                       name="search" 
                                       placeholder="Cari judul atau konten..." 
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
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>📝 Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-folder me-1"></i>Kategori
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="category_id" id="categoryFilter">
                                <option value="">Semua</option>
                                @if(isset($categories) && is_iterable($categories))
                                    @foreach($categories as $category)
                                        @if(is_object($category) && property_exists($category, 'id') && property_exists($category, 'name'))
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endif
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
                            <th width="35%">Judul</th>
                            <th width="20%">Konten</th>
                            <th width="15%">Status</th>
                            <th width="13%">Dibuat</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($query as $index => $item)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark rounded-pill">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="fw-bold mb-1">
                                    <a href="{{ route('topics.show', $item) }}" class="text-decoration-none text-dark">
                                        {{ Str::limit($item->title, 50) }}
                                    </a>
                                    @if($item->is_edited)
                                        <span class="badge bg-secondary status-badge ms-1">
                                            <i class="bi bi-pencil-fill"></i> Diedit
                                        </span>
                                    @endif
                                </div>
                                <div class="small text-muted mb-1">
                                    <code class="small">{{ Str::limit($item->slug ?? '-', 30) }}</code>
                                </div>
                                @if($item->category)
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-tag me-1"></i>{{ $item->category->name }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php $content = trim(strip_tags((string)($item->content ?? ''))); @endphp
                                @if($content !== '')
                                    <div class="text-truncate small text-muted" style="max-width: 200px;">
                                        {{ Str::limit($content, 60) }}
                                    </div>
                                    <span class="badge bg-info-subtle text-info mt-1">
                                        {{ $item->view_count ?? 0 }} <i class="bi bi-eye"></i>
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Tidak ada konten</span>
                                @endif
                            </td>
                            <td>
                                @php 
                                    $status = $item->status ?? 'submitted';
                                    $statusConfig = [
                                        'approved' => ['bg' => 'success', 'icon' => 'check-circle', 'label' => 'Disetujui'],
                                        'submitted' => ['bg' => 'warning', 'icon' => 'clock-history', 'label' => 'Menunggu'],
                                        'rejected' => ['bg' => 'danger', 'icon' => 'x-circle', 'label' => 'Ditolak'],
                                    ];
                                    $config = $statusConfig[$status] ?? $statusConfig['submitted'];
                                @endphp
                                <span class="badge bg-{{ $config['bg'] }} status-badge">
                                    <i class="bi bi-{{ $config['icon'] }} me-1"></i>{{ $config['label'] }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="fw-medium">{{ optional($item->created_at)->format('d M Y') ?? '-' }}</div>
                                    <div class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ optional($item->created_at)->format('H:i') ?? '' }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- View Button --}}
                                    <a href="{{ route('topics.show', $item) }}" 
                                       class="btn btn-outline-info action-btn-icon" 
                                       data-bs-toggle="tooltip" 
                                       title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    {{-- Edit Button - Owner with topics.my.edit OR admin/staff with topics.edit --}}
                                    @if(($item->user_id == auth()->id() && auth()->user()->can('topics.my.edit')) || auth()->user()->can('topics.edit'))
                                    <a href="{{ route('topics.edit', $item) }}" 
                                       class="btn btn-outline-warning action-btn-icon" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit Topik">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    
                                    {{-- Delete Button - Owner with topics.my.delete OR admin/staff with topics.delete --}}
                                    @if(($item->user_id == auth()->id() && auth()->user()->can('topics.my.delete')) || auth()->user()->can('topics.delete'))
                                    <form action="{{ route('topics.destroy', $item) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus topik ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger action-btn-icon" 
                                                data-bs-toggle="tooltip" 
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                    <h5 class="text-muted mb-2">Anda belum membuat topik</h5>
                                    <p class="text-muted small mb-3">Mulai dengan membuat topik pertama Anda</p>
                                    <a href="{{ route('topics.create') }}" class="btn btn-primary shadow-sm">
                                        <i class="bi bi-plus-circle me-2"></i>Buat Topik Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($query->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Menampilkan <strong>{{ $query->firstItem() }}</strong> - <strong>{{ $query->lastItem() }}</strong> dari <strong>{{ $query->total() }}</strong> topik
                </div>
                <nav aria-label="Page navigation">
                    {{ $query->links('pagination::bootstrap-5') }}
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

    // Auto-submit form when dropdowns change
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const filterForm = document.getElementById('filterForm');

    if (statusFilter && filterForm) {
        statusFilter.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (categoryFilter && filterForm) {
        categoryFilter.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (sortFilter && filterForm) {
        sortFilter.addEventListener('change', function() {
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
