@extends('layouts.app')
@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h1 class="mb-0">{{ $category->name }}</h1>
        </div>
        <div>
            @can('categories.edit')
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Ubah
                </a>
            @endcan
        </div>
    </div>

    <!-- Detail Category -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">Detail Kategori</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-semibold text-muted small">Nama Kategori</label>
                    <p class="mb-0 fs-5">{{ $category->name }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-semibold text-muted small">Slug</label>
                    <p class="mb-0"><code>{{ $category->slug }}</code></p>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-semibold text-muted small">Deskripsi</label>
                    <p class="mb-0">{{ $category->description ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-muted small">Total Topik</label>
                    <p class="mb-0">
                        <span class="badge bg-primary">{{ $category->topics->count() }}</span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-muted small">Diupdate</label>
                    <p class="mb-0">{{ $category->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Topik -->
    @if($category->topics->count() > 0)
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">Daftar Topik</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">Judul</th>
                        <th class="px-4 py-3 fw-semibold">Status</th>
                        <th class="px-4 py-3 fw-semibold">Diupdate</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($category->topics as $topic)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-medium">{{ $topic->title }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusLabels = [
                                        'approved' => 'Disetujui',
                                        'submitted' => 'Menunggu',
                                        'rejected' => 'Ditolak',
                                        'archived' => 'Diarsipkan',
                                    ];
                                    $statusLabel = $statusLabels[$topic->status] ?? ucfirst($topic->status);
                                @endphp
                                <span class="badge bg-{{ $topic->status === 'approved' ? 'success' : ($topic->status === 'submitted' ? 'warning' : ($topic->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <small class="text-muted">{{ $topic->updated_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

