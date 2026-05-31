@extends('layouts.app')

@section('title', 'Dasbor Admin')

@section('role', 'Dasbor Administrator')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    :root {
        --primary: #4e73df;
        --secondary: #858796;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --light: #f8f9fc;
        --dark: #5a5c69;
    }

    .stat-card {
        border-radius: 12px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        opacity: 0;
        transform: translateY(20px);
        background: white;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: relative;
        border: none;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: all 0.3s ease;
    }
    
    .stat-card.primary::before { background: linear-gradient(90deg, #4e73df 0%, #224abe 100%); }
    .stat-card.success::before { background: linear-gradient(90deg, #1cc88a 0%, #13855c 100%); }
    .stat-card.info::before { background: linear-gradient(90deg, #36b9cc 0%, #258391 100%); }
    .stat-card.warning::before { background: linear-gradient(90deg, #f6c23e 0%, #dda20a 100%); }
    
    .stat-card.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 12px 30px 0 rgba(0, 0, 0, 0.12) !important;
    }
    
    .stat-card:hover::before {
        height: 6px;
    }

    .stat-card .card-body {
        padding: 1.75rem 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .card-body {
        transform: translateY(-2px);
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin: 15px 0 8px;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #2c3e50 0%, #4a6b8a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
        letter-spacing: -0.5px;
    }
    
    .stat-card.primary .stat-value { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
    .stat-card.success .stat-value { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
    .stat-card.info .stat-value { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
    .stat-card.warning .stat-value { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
    
    .stat-card.primary .stat-value,
    .stat-card.success .stat-value,
    .stat-card.info .stat-value,
    .stat-card.warning .stat-value {
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }

    .stat-card .stat-label {
        color: var(--secondary);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-label {
        letter-spacing: 1px;
    }

    .stat-card .stat-icon {
        position: absolute;
        right: 1.5rem;
        top: 1.5rem;
        font-size: 3rem;
        opacity: 0.1;
        transition: all 0.4s ease;
    }
    
    .stat-card.primary .stat-icon { color: var(--primary); }
    .stat-card.success .stat-icon { color: var(--success); }
    .stat-card.info .stat-icon { color: var(--info); }
    .stat-card.warning .stat-icon { color: var(--warning); }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
        opacity: 0.15;
    }

    .top-members {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .top-members:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px 0 rgba(0, 0, 0, 0.12);
        border-color: rgba(78, 115, 223, 0.1);
    }

    .top-members .member {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
        transition: all 0.2s ease;
    }

    .top-members .member:hover {
        background-color: rgba(0, 0, 0, 0.02);
        transform: translateX(5px);
    }

    .top-members .member:last-child {
        border-bottom: none;
    }

    .member-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        margin-right: 15px;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .member-avatar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
        border-radius: 50%;
    }
    
    .member:hover .member-avatar {
        transform: scale(1.1);
        box-shadow: 0 6px 15px rgba(78, 115, 223, 0.4);
    }

    .member-info {
        flex: 1;
    }

    .member-name {
        font-weight: 600;
        margin-bottom: 0.2rem;
        color: #2c3e50;
    }

    .member-classes {
        display: flex;
        align-items: center;
        color: var(--secondary);
        font-size: 0.85rem;
    }

    .badge-classes {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 0.25rem 0.5rem;
        border-radius: 10rem;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #2c3e50;
        position: relative;
        padding-left: 15px;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 10px;
        color: var(--primary);
    }

    .section-title:before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 70%;
        background: var(--primary);
        border-radius: 2px;
    }

    .activity-item {
        position: relative;
        padding-left: 2rem;
        padding-bottom: 1.5rem;
        border-left: 1px solid #e3e6f0;
    }

    .activity-item:last-child {
        padding-bottom: 0;
        border-left-color: transparent;
    }

    .activity-badge {
        position: absolute;
        left: -8px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: var(--primary);
        border: 3px solid #fff;
    }

    .activity-time {
        font-size: 0.8rem;
        color: var(--secondary);
    }

    .activity-content {
        background: #f8f9fc;
        border-radius: 0.35rem;
        padding: 1rem;
        margin-top: 0.5rem;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('navigation')
    @include('fragments.navigation')
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dasbor</h1>
        </div>
        <div class="text-muted">
            <i class="bi bi-calendar3 me-2"></i>
            <span id="current-date">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
        </div>
    </div>
    
    <!-- Announcement Row -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="mb-3 mb-md-0 flex-grow-1">
                        <h5 class="section-title mb-2">
                            <i class="bi bi-megaphone-fill text-danger me-2"></i>Pemberitahuan
                        </h5>
                        @if(isset($announcements) && count($announcements) > 0)
                            <div class="announcement-list">
                                @foreach($announcements as $announcement)
                                    <div class="mb-3 p-3 bg-light rounded" style="position: relative;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="fw-bold">{{ $announcement->title }}</span>
                                                <span class="mx-1">·</span>
                                                <span class="text-muted small">{{ $announcement->created_at->diffForHumans() }}</span>
                                                @if($announcement->user)
                                                    <span class="text-muted small">oleh {{ $announcement->user->name }}</span>
                                                @endif
                                            </div>
                                            @can('announcements.manage')
                                            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemberitahuan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm p-0" title="Hapus" style="background: none; border: none; outline: none;">
                                                    <i class="bi bi-trash" style="color: #dc3545;"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                        <div class="text-body">{!! $announcement->content !!}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted">Belum ada pemberitahuan.</div>
                        @endif
                    </div>
                    @can('announcements.manage')
                    <div class="d-flex align-items-start">
                        <!-- Button to trigger modal create pemberitahuan -->
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Pemberitahuan
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    @can('announcements.manage')
    <!-- Modal Create Pemberitahuan -->
    <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('announcements.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAnnouncementModalLabel">Buat Pemberitahuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required maxlength="255" placeholder="Masukkan judul pemberitahuan">
                            <div class="form-text">Maksimal 255 karakter</div>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Isi Pemberitahuan <span class="text-danger">*</span></label>
                            <div id="editor" style="height: 300px;"></div>
                            <textarea name="content" id="content" style="display: none;" required>{{ old('content') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Kirim</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card border-0 shadow-sm warning">
                <div class="card-body">
                    <div class="stat-label"><i class="bi bi-people me-2"></i>Total Pengurus</div>
                    <div class="stat-value">{{ number_format($total_pengurus ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card border-0 shadow-sm primary">
                <div class="card-body">
                    <div class="stat-label"><i class="bi bi-people me-2"></i>Total Anggota</div>
                    <div class="stat-value">{{ number_format($total_anggota ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Top Contributors Chart -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="section-title mb-4">
                        <i class="bi bi-star-fill text-warning me-2"></i>Top 5 Kontributor
                    </h5>
                    <div class="chart-container">
                        <canvas id="topContributorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Topics Chart -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="section-title mb-4">
                        <i class="bi bi-graph-up text-primary me-2"></i>Topik Mingguan
                    </h5>
                    <div class="chart-container">
                        <canvas id="weeklyTopicsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Weekly Downloads Chart -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="section-title mb-4">
                        <i class="bi bi-download text-success me-2"></i>Download Template Mingguan
                    </h5>
                    <div class="chart-container">
                        <canvas id="weeklyDownloadsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill Editor
        let quill;
        const editorElement = document.getElementById('editor');
        const contentTextarea = document.getElementById('content');
        
        if (editorElement && contentTextarea) {
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'blockquote', 'code-block'],
                        [{ 'align': [] }],
                        ['clean']
                    ]
                },
                placeholder: 'Masukkan isi pemberitahuan...'
            });

            // Set initial content if exists
            if (contentTextarea.value) {
                quill.root.innerHTML = contentTextarea.value;
            }

            // Update textarea on text change
            quill.on('text-change', function() {
                contentTextarea.value = quill.root.innerHTML;
            });

            // Also update on form submit
            const form = document.querySelector('#createAnnouncementModal form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    contentTextarea.value = quill.root.innerHTML;
                });
            }
        }
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Animate stat cards on scroll
        const statCards = document.querySelectorAll('.stat-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    // Add delay based on index for staggered animation
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 150);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        statCards.forEach(card => observer.observe(card));

        // Update current date with live time
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
        }

        // Update time every second
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Top Contributors Chart (Bar Chart)
        const topContributorsCtx = document.getElementById('topContributorsChart');
        if (topContributorsCtx) {
            const topContributorsData = @json($top_contributors ?? []);
            const contributorNames = topContributorsData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name);
            const contributorCounts = topContributorsData.map(item => item.topics_count ?? 0);

            const topContributorsChart = new Chart(topContributorsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: contributorNames,
                    datasets: [{
                        label: 'Jumlah Topik',
                        data: contributorCounts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(199, 199, 199, 0.8)',
                            'rgba(83, 102, 255, 0.8)',
                            'rgba(255, 99, 255, 0.8)',
                            'rgba(99, 255, 132, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(199, 199, 199, 1)',
                            'rgba(83, 102, 255, 1)',
                            'rgba(255, 99, 255, 1)',
                            'rgba(99, 255, 132, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const fullName = topContributorsData[context.dataIndex].name;
                                    return fullName + ': ' + context.raw + ' topik';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // Weekly Topics Chart
        const weeklyTopicsCtx = document.getElementById('weeklyTopicsChart');
        if (weeklyTopicsCtx) {
            const weeklyTopicsChart = new Chart(weeklyTopicsCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($weekly_topics_labels ?? []),
                    datasets: [{
                        label: 'Jumlah Topik',
                        data: @json($weekly_topics ?? []),
                        fill: true,
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointHoverBorderColor: '#fff',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return 'Topik: ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // Weekly Downloads Chart
        const weeklyDownloadsCtx = document.getElementById('weeklyDownloadsChart');
        if (weeklyDownloadsCtx) {
            const weeklyDownloadsChart = new Chart(weeklyDownloadsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($weekly_downloads_labels ?? []),
                    datasets: [{
                        label: 'Jumlah Download',
                        data: @json($weekly_downloads ?? []),
                        backgroundColor: 'rgba(28, 200, 138, 0.8)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return 'Download: ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // Add hover effect to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
                this.style.boxShadow = '0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.1)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1)';
            });
        });

        // Auto close modal after successful form submission
        @if (session('success'))
            const modal = bootstrap.Modal.getInstance(document.getElementById('createAnnouncementModal'));
            if (modal) {
                modal.hide();
                // Reset form and clear Quill editor
                document.querySelector('#createAnnouncementModal form').reset();
                if (quill) {
                    quill.setContents([]);
                    contentTextarea.value = '';
                }
            }
        @endif

        // Reset Quill editor when modal is closed
        const announcementModal = document.getElementById('createAnnouncementModal');
        if (announcementModal && quill) {
            announcementModal.addEventListener('hidden.bs.modal', function() {
                quill.setContents([]);
                contentTextarea.value = '';
            });
        }
    });
</script>
@endpush

@endsection