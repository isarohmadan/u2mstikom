@extends('layouts.app')

@section('title', 'Tambah Peran')

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah Peran Baru</h5>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="form-label">Nama Peran</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Hak Akses</label>
                    <div class="row">
                        @foreach($permissions as $group => $groupPermissions)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header bg-light py-2">
                                    <strong>{{ translatePermissionGroup($group) }}</strong>
                                </div>
                                <div class="card-body">
                                    @foreach($groupPermissions as $permission)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                               id="perm_{{ $permission->id }}" class="form-check-input"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <label for="perm_{{ $permission->id }}" class="form-check-label">
                                            {{ translatePermission($permission->name) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
