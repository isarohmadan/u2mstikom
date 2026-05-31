@extends('layouts.app')

@section('title', 'Kelola Topik')

@section('role', 'Manajemen Topik')

@section('navigation')
    @include('fragments.navigation')
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
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
    .filter-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .table-row-hover {
        transition: all 0.2s ease;
    }
    .table-row-hover:hover {
        background-color: #f8f9fa;
    }
    .avatar-initial {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }
       /* CRITICAL FIX for dropdown positioning */
       .table-responsive {
        overflow: visible !important; /* Allow dropdown to overflow */
    }
    
    .card-body {
        overflow: visible !important;
    }
    
    /* If table still clips, wrap it differently */
    .table-wrapper {
        overflow-x: auto;
        overflow-y: visible;
    }
    
    /* Ensure dropdown appears above everything */
    .dropdown-menu {
        position: absolute !important;
        z-index: 1050 !important;
        min-width: 180px;
        border: none;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        animation: fadeIn 0.2s ease;
        margin-top: 4px;
    }
    
    /* Make sure the dropdown parent has position relative */
    .dropdown {
        position: relative;
    }
    
    /* Action buttons container */
    .action-buttons-wrapper {
        display: flex;
        gap: 4px;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
        position: relative; /* Important for dropdown positioning */
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
        flex-shrink: 0; /* Prevent button shrinking */
    }
    
    .action-btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .action-btn-icon i {
        font-size: 14px;
    }
    
    /* Remove dropdown toggle arrow if you want */
    .dropdown-toggle::after {
        display: none;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .dropdown-item {
        padding: 10px 16px;
        transition: all 0.2s ease;
        font-size: 14px;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        padding-left: 20px;
    }
    
    .dropdown-item i {
        width: 20px;
    }
    
    /* Table enhancements */
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Ensure table cell doesn't clip content */
    .table tbody td {
        position: relative;
        overflow: visible;
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
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .action-buttons-wrapper {
            flex-wrap: nowrap;
            gap: 4px;
        }
        
        .action-btn-icon {
            width: 30px;
            height: 30px;
        }
        
        .action-btn-icon i {
            font-size: 12px;
        }
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
                <i class="bi bi-chat-dots me-2 text-primary"></i>Daftar Topik
            </h4>
            <p class="text-muted mb-0">Kelola semua topik diskusi</p>
        </div>
        <a href="{{ route('topics.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Tambah Topik
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-muted small">Total Topik</div>
                        <div class="h4 mb-0 fw-bold text-primary">{{ $stats['total'] }}</div>
                    </div>
                    <div class="avatar-initial bg-primary text-white">
                        <i class="bi bi-collection"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-muted small">Disetujui</div>
                        <div class="h4 mb-0 fw-bold text-success">{{ $stats['approved'] }}</div>
                    </div>
                    <div class="avatar-initial bg-success text-white">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-muted small">Menunggu</div>
                        <div class="h4 mb-0 fw-bold text-warning">{{ $stats['submitted'] }}</div>
                    </div>
                    <div class="avatar-initial bg-warning text-white">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-start border-danger border-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-muted small">Ditolak</div>
                        <div class="h4 mb-0 fw-bold text-danger">{{ $stats['rejected'] }}</div>
                    </div>
                    <div class="avatar-initial bg-danger text-white">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-chat-dots me-2"></i>Daftar Topik
                </h5>
                <span class="badge bg-primary">Total: {{ $query->total() }}</span>
            </div>
        </div>
        
        <div class="card-body">
            {{-- Search and Filter --}}
            <form method="GET" action="{{ route('topics.index') }}" id="filterForm">
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
                                <option value="">Semua Status</option>
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
                                <option value="">Semua Kategori</option>
                                @isset($categories)
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-tag me-1"></i>Tag
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="tag" id="tagFilter">
                                <option value="">Semua Tag</option>
                                @isset($allTags)
                                    @foreach($allTags as $tag)
                                        <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>#{{ $tag }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-sort-down me-1"></i>Urutkan
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="sort" id="sortBy">
                                <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>🕒 Terbaru</option>
                                <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>🕐 Terlama</option>
                                <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>🔤 A-Z</option>
                                <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>🔤 Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>

        <!-- Topics Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">
                            <i class="bi bi-hash text-muted"></i>
                        </th>
                        <th width="25%">Topik</th>
                        <th width="15%">Penulis</th>
                        <th width="18%">Konten</th>
                        <th width="12%">Status</th>
                        <th width="13%">Dibuat</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($query as $index => $item)
                    <tr class="table-row-hover">
                        <td class="text-center">
                            <span class="badge bg-light text-dark rounded-pill">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <div class="fw-bold mb-1 text-dark">
                                        <a href="{{ route('topics.show', $item) }}" class="text-decoration-none text-dark">
                                            {{ Str::limit($item->title, 50) }}
                                        </a>
                                        @if($item->is_edited)
                                            <span class="badge bg-secondary status-badge ms-1" data-bs-toggle="tooltip" title="Diubah oleh {{ $item->editor->name ?? 'Admin' }}">
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
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($item->user)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial bg-primary text-white me-2">
                                        {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium small">{{ Str::limit($item->user->name, 20) }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php $content = trim(strip_tags((string)($item->content ?? ''))); @endphp
                            @if($content !== '')
                                <div class="text-truncate small text-muted" style="max-width: 200px;" data-bs-toggle="tooltip" title="{{ Str::limit($content, 150) }}">
                                    <i class="bi bi-file-text me-1"></i>
                                    {{ Str::limit($content, 60) }}
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-info-subtle text-info">
                                        {{ $item->view_count ?? 0 }} <i class="bi bi-eye"></i>
                                    </span>
                                </div>
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
                                    'archived' => ['bg' => 'secondary', 'icon' => 'archive', 'label' => 'Diarsipkan'],
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
                            <div class="action-buttons-wrapper">
                                <!-- Bookmark Button -->
                                <form action="{{ route('topics.bookmark', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn {{ auth()->user()->hasBookmarked($item->id) ? 'btn-warning text-white' : 'btn-outline-secondary' }} action-btn-icon" 
                                            data-bs-toggle="tooltip" 
                                            title="{{ auth()->user()->hasBookmarked($item->id) ? 'Hapus dari Favorit' : 'Simpan ke Favorit' }}">
                                        <i class="bi {{ auth()->user()->hasBookmarked($item->id) ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                    </button>
                                </form>

                                <!-- View Button -->
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
                                title="Ubah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                
                                <!-- More Actions Dropdown -->
                                @canany(['topics.approve', 'topics.delete'])
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary action-btn-icon dropdown-toggle" 
                                            type="button" 
                                            id="actionDropdown{{ $item->id }}" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false"
                                            title="Aksi Lainnya">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    
                                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="actionDropdown{{ $item->id }}">
                                        @can('topics.approve')
                                        @if($status !== 'approved')
                                        <li>
                                            <form action="{{ route('topics.approve', $item) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menyetujui topik ini?')"
                                                class="d-inline w-100">
                                                @csrf
                                                <button type="submit" class="dropdown-item d-flex align-items-center text-success">
                                                    <i class="bi bi-check-circle me-2"></i> Setujui
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        
                                        @if($status !== 'rejected')
                                        <li>
                                            <form action="{{ route('topics.reject', $item) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menolak topik ini?')"
                                                class="d-inline w-100">
                                                @csrf
                                                <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                                    <i class="bi bi-x-circle me-2"></i> Tolak
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        @endcan
                                        
                                        @can('topics.delete')
                                        <li>
                                            <form action="{{ route('topics.destroy', $item) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus topik ini? Tindakan ini tidak dapat dibatalkan!')"
                                                class="d-inline w-100">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                                    <i class="bi bi-trash me-2"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcanany
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted mb-2">Belum ada data topik</h5>
                                <p class="text-muted small mb-3">Mulai dengan membuat topik pertama Anda</p>
                                <a href="{{ route('topics.create') }}" class="btn btn-primary shadow-sm">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Topik Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Pagination -->
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
        </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-submit form when dropdowns change
    const statusEl = document.getElementById('statusFilter');
    const sortEl = document.getElementById('sortBy');
    const categoryEl = document.getElementById('categoryFilter');
    const tagEl = document.getElementById('tagFilter');
    const searchEl = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');

    // Init Tom Select for Category Filter
    if(categoryEl) {
        new TomSelect('#categoryFilter', {
            create: false,
            sortField: {field: "text", direction: "asc"},
            placeholder: 'Semua Kategori',
            plugins: ['dropdown_input'],
            onChange: function() {
                filterForm.submit();
            }
        });
    }

    // Init Tom Select for Tag Filter
    if(tagEl) {
        new TomSelect('#tagFilter', {
            create: false,
            sortField: {field: "text", direction: "asc"},
            placeholder: 'Semua Tag',
            plugins: ['dropdown_input'],
            onChange: function() {
                filterForm.submit();
            }
        });
    }

    if (statusEl && filterForm) {
        statusEl.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (sortEl && filterForm) {
        sortEl.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    // Handle enter key in search input
    if (searchEl && filterForm) {
        searchEl.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });

        // Add search icon click handler
        searchEl.addEventListener('input', function() {
            const searchBtn = this.closest('.input-group').querySelector('button[type="submit"]');
            if (this.value.trim() !== '') {
                searchBtn?.classList.add('btn-primary');
                searchBtn?.classList.remove('btn-outline-primary');
            } else {
                searchBtn?.classList.remove('btn-primary');
                searchBtn?.classList.add('btn-outline-primary');
            }
        });
    }

    // Add smooth scroll to top when pagination is clicked
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Add row click functionality (optional - navigate to show page)
    document.querySelectorAll('.table-row-hover').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            // Don't navigate if clicking on buttons or links
            if (!e.target.closest('a, button, form')) {
                const link = this.querySelector('td:nth-child(2) a');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
    });

    // Add animation to stat cards on load
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 100);
    });
});
</script>
@endpush

@endsection
