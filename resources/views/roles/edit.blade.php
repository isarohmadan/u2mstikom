@extends('layouts.app')

@section('title', 'Edit Peran')

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Edit Peran: {{ $role->name }}</h4>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="name" class="form-label">Nama Peran</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $role->name) }}" required 
                           {{ $role->name === 'administrator' ? 'readonly' : '' }}>
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
                                               {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
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

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Perbarui Peran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
