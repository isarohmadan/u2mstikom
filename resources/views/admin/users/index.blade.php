@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

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
                    <i class="bi bi-people me-2 text-primary"></i>Manajemen Pengguna
                </h4>
                <p class="text-muted mb-0">Kelola pengguna dan hak akses sistem</p>
            </div>
            @can('users.create')
                <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pengguna
                </a>
            @endcan
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
                        <i class="bi bi-people me-2"></i>Daftar Pengguna
                    </h5>
                    <span class="badge bg-primary">Total: {{ $users->total() }}</span>
                </div>
            </div>
            <div class="card-body">
                {{-- Search and Filter --}}
                <form method="GET" action="{{ route('users.index') }}" id="filterForm">
                    <div class="filter-card">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="bi bi-search me-1"></i>Cari Pengguna
                                </label>
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control text-xs border-start-0" 
                                           name="search" 
                                           placeholder="Cari nama, email, atau peran..." 
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
                                    <i class="bi bi-funnel me-1"></i>Peran
                                </label>
                                <select class="form-select form-select-lg shadow-sm" name="role" id="role">
                                    <option value="">Semua</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="bi bi-sort-down me-1"></i>Urutkan
                                </label>
                                <select class="form-select form-select-lg shadow-sm" name="sort_by" id="sort_by">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                                    <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted mb-2">
                                    <i class="bi bi-arrow-up-down me-1"></i>Urutan
                                </label>
                                <select class="form-select form-select-lg shadow-sm" name="sort_order" id="sort_order">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terlama</option>
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
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Peran</th>
                            <th class="px-4 py-3">Dibuat</th>
                            <th class="px-4 py-3 text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                            style="width: 35px; height: 35px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex gap-1 justify-content-center">
                                        @can('users.view')
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Lihat Profil">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan
                                        @can('users.edit')
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('users.delete')
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus pengguna ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">Tidak ada pengguna yang ditemukan.</p>
                                        @if(request('search') || request('role'))
                                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary mt-2">
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
            @if($users->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            Menampilkan <strong>{{ $users->firstItem() }}</strong> sampai <strong>{{ $users->lastItem() }}</strong> dari <strong>{{ $users->total() }}</strong> pengguna
                        </div>
                        <nav aria-label="Navigasi halaman">
                            {{ $users->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection