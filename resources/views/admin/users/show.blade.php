@extends('layouts.app')

@section('title', 'Detail User')

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="mb-3">
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                        style="width: 65px; height: 65px; font-size: 1.6rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $user->name }}</h4>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted">Nama:</span>
                    <strong class="ms-2">{{ $user->name }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted">Email:</span>
                    <strong class="ms-2">{{ $user->email }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted">Role:</span>
                    @foreach($user->roles as $role)
                        <span class="badge bg-primary ms-2">{{ ucfirst($role->name) }}</span>
                    @endforeach
                </div>
                <div class="mb-3">
                    <span class="text-muted">ID User:</span>
                    <code class="ms-2">#{{ $user->id }}</code>
                </div>

                <div class="border-top pt-3 mb-3">
                    <div class="mb-2">
                        <small class="text-muted">Dibuat:</small>
                        <div>{{ $user->created_at->format('d M Y') }} <span class="text-muted">pukul {{ $user->created_at->format('H:i') }}</span></div>
                    </div>
                    <div>
                        <small class="text-muted">Terakhir diupdate:</small>
                        <div>{{ $user->updated_at->format('d M Y') }}</div>
                    </div>
                </div>

                @if($user->email_verified_at)
                    <div class="mb-3">
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle"></i> Email sudah diverifikasi
                        </span>
                        <small class="text-muted ms-2">{{ $user->email_verified_at->format('d M Y H:i') }}</small>
                    </div>
                @else
                    <div class="mb-3">
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-x-circle"></i> Email belum diverifikasi
                        </span>
                    </div>
                @endif

                <div class="border-top pt-3 mb-3">
                    <div class="mb-2">
                        <span class="text-muted">Hak Akses:</span>
                    </div>
                    @php
                        $permissions = $user->getAllPermissions();
                    @endphp
                    
                    @if($permissions->count() > 0)
                        <div class="mb-2">
                            @foreach($permissions as $permission)
                                <span class="badge bg-secondary me-1 mb-1" style="font-size: 0.75rem;">
                                    {{ translatePermission($permission->name) }}
                                </span>
                            @endforeach
                        </div>
                        <small class="text-muted">Total: {{ $permissions->count() }} hak akses</small>
                    @else
                        <span class="text-muted">Belum ada hak akses</span>
                    @endif
                </div>

                <div class="border-top pt-3">
                    @can('users.edit')
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endcan
                    @can('users.delete')
                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection