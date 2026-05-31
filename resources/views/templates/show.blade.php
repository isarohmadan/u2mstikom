@extends('layouts.app')

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h1 class="mb-0">{{ $template->name }}</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Detail Template -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">Detail Template</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-semibold text-muted small">Nama Template</label>
                    <p class="mb-0 fs-5">{{ $template->name }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-semibold text-muted small">Deskripsi</label>
                    <p class="mb-0">{{ $template->description ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-muted small">Versi Terbaru</label>
                    <p class="mb-0">
                        <span class="badge bg-primary">{{ $template->latest_version_number ?? '-' }}</span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-muted small">Total Versi</label>
                    <p class="mb-0">{{ $versions->count() }} versi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Versi -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Daftar Versi</h5>
            @can('templates.manage')
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadVersionModal">
                <i class="bi bi-plus-circle"></i> Upload Versi Baru
            </button>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto; max-height: none;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">Versi</th>
                        <th class="px-4 py-3 fw-semibold">Nama File</th>
                        <th class="px-4 py-3 fw-semibold">Tipe File</th>
                        <th class="px-4 py-3 fw-semibold text-center">Ukuran</th>
                        <th class="px-4 py-3 fw-semibold">Tanggal Upload</th>
                        <th class="px-4 py-3 fw-semibold text-center" style="width: 120px;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($versions->sortBy('version_number')->reverse() as $version)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $version->version_number == $template->latest_version_number ? 'primary' : 'secondary' }}">
                                    v{{ $version->version_number }}
                                    @if($version->version_number == $template->latest_version_number)
                                        <span class="badge bg-light text-dark ms-1">Terbaru</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="fw-medium">{{ $version->original_filename }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-muted">{{ $version->mime_type }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-muted">{{ number_format($version->file_size / 1024, 2) }} KB</span>
                            </td>
                            <td class="px-4 py-3">
                                <small class="text-muted">{{ $version->created_at->format('d M Y H:i') }}</small>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('templates.download', [$template, $version]) }}" class="btn btn-sm btn-outline-primary" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Tidak ada versi tersedia</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('templates.manage')
    <!-- Modal Upload Versi -->
    <div class="modal fade" id="uploadVersionModal" tabindex="-1" aria-labelledby="uploadVersionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadVersionModalLabel">Upload Versi Baru - {{ $template->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('templates.version.upload', $template) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">File (pdf/docx/pptx/xlsx)</label>
                            <input type="file" name="file" class="form-control" accept=".pdf,.docx,.pptx,.xlsx" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Versi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>

<style>
    .table-responsive .dropdown {
        position: static !important;
    }
    
    .table-responsive .dropdown-menu {
        position: relative !important;
        margin-top: 0.125rem;
        margin-bottom: 0.125rem;
        transform: none !important;
        left: auto !important;
        right: auto !important;
        top: auto !important;
        bottom: auto !important;
    }
    
    .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
    }
    
    .card-body {
        overflow: visible !important;
    }
</style>
@endsection