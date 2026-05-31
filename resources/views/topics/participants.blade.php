@extends('layouts.app')

@section('title', 'Kelola Peserta Kelas')

@section('role', 'Manajemen Kelas')

@section('navigation')
    @include('admin.mainFragments.navigation')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <a href="{{ route('admin.kelas.show', $kelas) }}" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    Kelola Peserta: {{ $kelas->nama_kelas }}
                </h5>
                <span class="badge bg-{{ $kelas->status === 'aktif' ? 'success' : 'secondary' }}">
                    {{ $kelas->status_label }}
                </span>
            </div>
        </div>
        <div class="card-body">
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

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Peserta Terdaftar (<span id="participantCount">{{ $kelas->users->count() }}</span>)</h6>
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchParticipant" class="form-control" placeholder="Cari peserta...">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($kelas->users->isEmpty())
                                <div id="noParticipants" class="p-3 text-center text-muted">
                                    Belum ada peserta yang terdaftar
                                </div>
                                <div id="noResults" class="p-3 text-center text-muted d-none">
                                    Tidak ditemukan peserta yang sesuai dengan pencarian
                                </div>
                            @else
                                <div class="list-group list-group-flush" id="participantList">
                                    @foreach($kelas->users as $user)
                                        <div class="list-group-item d-flex justify-content-between align-items-center participant-item" 
                                             data-name="{{ strtolower($user->name) }}" 
                                             data-email="{{ strtolower($user->email) }}">
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <form class="remove-participant-form d-inline" 
                                                  action="{{ route('admin.kelas.peserta.destroy', ['kelas' => $kelas, 'user' => $user]) }}" 
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger remove-participant" 
                                                        data-name="{{ $user->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <form id="addParticipantForm" action="{{ route('admin.kelas.peserta.store', $kelas) }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Tambah Peserta</h6>
                            </div>
                            <div class="card-body">
                                @if($allStudents->isEmpty())
                                    <div class="alert alert-info mb-0">
                                        Semua siswa sudah terdaftar di kelas ini.
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label class="form-label">Cari Siswa</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" id="searchStudent" class="form-control" placeholder="Cari berdasarkan nama atau email...">
                                        </div>
                                        
                                        <label class="form-label">Pilih Peserta</label>
                                        <div class="border rounded p-2 mb-3" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($allStudents as $student)
                                                <div class="form-check mb-2 student-item" 
                                                     data-name="{{ strtolower($student->name) }}" 
                                                     data-email="{{ strtolower($student->email) }}">
                                                    <input class="form-check-input student-checkbox" type="checkbox" 
                                                           name="participants[]" value="{{ $student->id }}" id="student-{{ $student->id }}">
                                                    <label class="form-check-label w-100" for="student-{{ $student->id }}">
                                                        <div class="fw-medium">{{ $student->name }}</div>
                                                        <small class="text-muted">{{ $student->email }}</small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                <span id="selectedCount">0</span> peserta terpilih
                                            </small>
                                            <button type="button" id="selectAllBtn" class="btn btn-sm btn-outline-secondary">
                                                Pilih Semua
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.kelas.show', $kelas) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-1"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="saveBtn" disabled>
                                            <i class="bi bi-plus-circle me-1"></i> Tambah Peserta
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .list-group-item {
        border-left: 0;
        border-right: 0;
        transition: all 0.2s;
    }
    .list-group-item:first-child {
        border-top: 0;
    }
    .list-group-item:last-child {
        border-bottom: 0;
    }
    .participant-item:hover, .student-item:hover {
        background-color: #f8f9fa;
    }
    .student-item {
        padding: 0.5rem;
        border-radius: 0.25rem;
        transition: all 0.2s;
    }
    .form-check-input:checked ~ .form-check-label {
        color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle participant removal
        document.querySelectorAll('.remove-participant').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const participantName = this.getAttribute('data-name');
                
                if (confirm(`Apakah Anda yakin ingin menghapus ${participantName} dari kelas ini?`)) {
                    form.submit();
                }
            });
        });
        
        // Elements
        const searchInput = document.getElementById('searchParticipant');
        const participantItems = document.querySelectorAll('.participant-item');
        const participantCount = document.getElementById('participantCount');
        const noResults = document.getElementById('noResults');
        const noParticipants = document.getElementById('noParticipants');
        const searchStudent = document.getElementById('searchStudent');
        const studentItems = document.querySelectorAll('.student-item');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const saveBtn = document.getElementById('saveBtn');
        const selectedCount = document.getElementById('selectedCount');
        const form = document.getElementById('addParticipantForm');
        
        // Function to update selected count and enable/disable save button
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.student-checkbox:checked');
            const count = checked.length;
            selectedCount.textContent = count;
            saveBtn.disabled = count === 0;
        }
        
        // Initialize selected count on page load
        updateSelectedCount();
        
        // Search participants
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                let hasVisibleItems = false;
                
                participantItems.forEach(item => {
                    const name = item.getAttribute('data-name');
                    const email = item.getAttribute('data-email');
                    const isVisible = name.includes(searchTerm) || email.includes(searchTerm);
                    
                    item.style.display = isVisible ? 'flex' : 'none';
                    if (isVisible) {
                        visibleCount++;
                        hasVisibleItems = true;
                    }
                });
                
                // Update participant count
                if (participantCount) {
                    participantCount.textContent = visibleCount;
                }
                
                // Show/hide no results message
                if (noResults) {
                    noResults.classList.toggle('d-none', !(searchTerm && !hasVisibleItems));
                    if (noParticipants) {
                        noParticipants.classList.toggle('d-none', hasVisibleItems || visibleCount > 0);
                    }
                }
            });
        }
        
        // Search students
        if (searchStudent) {
            searchStudent.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                studentItems.forEach(item => {
                    const name = item.getAttribute('data-name');
                    const email = item.getAttribute('data-email');
                    item.style.display = (name.includes(searchTerm) || email.includes(searchTerm)) ? 'block' : 'none';
                });
            });
        }
        
        // Select all functionality
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const visibleCheckboxes = Array.from(document.querySelectorAll('.student-checkbox:enabled'))
                    .filter(checkbox => {
                        const item = checkbox.closest('.student-item');
                        return item && item.style.display !== 'none';
                    });
                
                const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(checkbox => checkbox.checked);
                
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                
                updateSelectedCount();
            });
        }
        
        // Update selected count when checkboxes change
        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        
        // Form submission handling
        if (form) {
            const hiddenContainer = document.createElement('div');
            hiddenContainer.style.display = 'none';
            form.appendChild(hiddenContainer);
            
            function showMessage(message, type = 'danger') {
                const existingAlert = document.querySelector('.alert.alert-dismissible');
                if (existingAlert) existingAlert.remove();
                
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                const cardHeader = document.querySelector('.card-header');
                if (cardHeader?.nextElementSibling) {
                    cardHeader.parentNode.insertBefore(alertDiv, cardHeader.nextElementSibling);
                }
                
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alertDiv);
                    bsAlert.close();
                }, 5000);
            }
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const checkboxes = Array.from(document.querySelectorAll('.student-checkbox:checked'));
                if (checkboxes.length === 0) {
                    showMessage('Pilih setidaknya satu peserta untuk ditambahkan', 'warning');
                    return false;
                }
                
                const participantIds = [...new Set(checkboxes.map(checkbox => checkbox.value))];
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ participants: participantIds })
                    });
                    
                    const result = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(result.message || 'Terjadi kesalahan saat menambahkan peserta');
                    }
                    
                    window.location.href = result.redirect || window.location.href;
                    
                } catch (error) {
                    console.error('Error:', error);
                    showMessage(error.message || 'Terjadi kesalahan saat menambahkan peserta', 'danger');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        }
    });
</script>
@endpush

@endsection
