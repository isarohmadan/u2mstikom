@extends('layouts.app')

@section('title', 'Manajemen Peran')

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
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-shield-check me-2 text-primary"></i>Manajemen Peran
            </h4>
            <p class="text-muted mb-0">Kelola peran dan hak akses pengguna</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Tambah Peran
        </a>
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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-shield-check me-2"></i>Daftar Peran
                </h5>
                <span class="badge bg-primary">Total: {{ $roles->total() }}</span>
            </div>
        </div>
        <div class="card-body">
            {{-- Search and Filter --}}
            <form method="GET" action="{{ route('roles.index') }}" id="filterForm">
                <div class="filter-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-search me-1"></i>Cari Peran
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control text-xs border-start-0" 
                                       name="search" 
                                       placeholder="Cari nama peran..." 
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
                                <i class="bi bi-funnel me-1"></i>Pengguna
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="user_filter" id="user_filter">
                                <option value="">Semua</option>
                                <option value="with_users" {{ request('user_filter') == 'with_users' ? 'selected' : '' }}>Dengan Pengguna</option>
                                <option value="without_users" {{ request('user_filter') == 'without_users' ? 'selected' : '' }}>Tanpa Pengguna</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-sort-down me-1"></i>Urutkan
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="sort_by" id="sort_by">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                                <option value="users" {{ request('sort_by') == 'users' ? 'selected' : '' }}>Jumlah Pengguna</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-arrow-up-down me-1"></i>Urutan
                            </label>
                            <select class="form-select form-select-lg shadow-sm" name="sort_order" id="sort_order">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Naik</option>
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Turun</option>
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
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Nama Peran</th>
                        <th class="px-4 py-3">Hak Akses</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3 text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($role->permissions->take(5) as $permission)
                                    <span class="badge bg-secondary" title="{{ $permission->name }}">{{ translatePermission($permission->name) }}</span>
                                @endforeach
                                @if($role->permissions->count() > 5)
                                    <span class="badge bg-info">+{{ $role->permissions->count() - 5 }} lainnya</span>
                                @endif
                                @if($role->permissions->count() == 0)
                                    <span class="badge bg-light text-dark">Tidak ada hak akses</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-dark">{{ $role->users->count() }} pengguna</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($role->name !== 'administrator')
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Hapus peran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">Tidak ada peran yang ditemukan.</p>
                                @if(request('search') || request('user_filter'))
                                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        Hapus Filter
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Menampilkan <strong>{{ $roles->firstItem() }}</strong> sampai <strong>{{ $roles->lastItem() }}</strong> dari <strong>{{ $roles->total() }}</strong> peran
                    </div>
                    <nav aria-label="Navigasi halaman">
                        {{ $roles->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
