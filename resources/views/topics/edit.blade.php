@extends('layouts.app')

@section('title', 'Edit Topik')

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Topik</h5>
            <a href="{{ route('topics.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('topics.update', $topic) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('topics._form')
            </form>
        </div>
    </div>
</div>
@endsection
