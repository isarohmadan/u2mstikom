<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dasbor')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/U2M.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- google font roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Lato', sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background: #212529;
            color: #fff;
            transition: transform 0.3s ease-in-out;
            position: fixed;
            width: 250px;
            z-index: 1040;
            top: 0;
            left: 0;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        .sidebar-header {
            padding: 20px;
            background: #1a1e21;
        }
        .sidebar-menu {
            padding: 20px 0;
        }
        .sidebar-menu a {
            padding: 10px 20px;
            color: #adb5bd;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: #fff;
            background: #343a40;
        }
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Backdrop overlay for mobile */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1030;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        .topbar {
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #495057;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }
        .sidebar-toggle:hover {
            background-color: #f8f9fa;
        }
        .user-dropdown .dropdown-menu {
            right: 0;
            left: auto;
        }
        
        /* Mobile styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Backdrop overlay for mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header text-center">
                <img src="{{ asset('storage/U2M-removebg-preview.png') }}" alt="U2M" width="60">
            </div>
            <div class="sidebar-menu">
                @yield('navigation')
                <div class="mt-4 px-3">
          
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('settings') }}" class="btn btn-outline-light w-100 mb-2 py-2">
                            <i class="bi bi-gear me-2"></i> Pengaturan
                        </a>
                        <button type="submit" class="btn btn-outline-light w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content w-100" id="main-content">
            <div class="topbar mb-4">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('settings') }}">
                                <i class="bi bi-gear me-2"></i>Pengaturan
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            // Toggle sidebar on button click
            sidebarToggle.addEventListener('click', function() {
                const isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    // Mobile: toggle show class and backdrop
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                } else {
                    // Desktop: toggle collapsed class
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });

            // Close sidebar when clicking backdrop
            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });

            // Close sidebar when clicking a link on mobile
            const sidebarLinks = sidebar.querySelectorAll('.sidebar-menu a, .sidebar-menu button');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        setTimeout(() => {
                            sidebar.classList.remove('show');
                            backdrop.classList.remove('show');
                        }, 200);
                    }
                });
            });

            // Handle window resize
            function handleResize() {
                const isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    // Mobile: ensure sidebar is hidden and backdrop is removed
                    sidebar.classList.remove('collapsed', 'show');
                    backdrop.classList.remove('show');
                    mainContent.classList.add('expanded');
                } else {
                    // Desktop: reset to default state
                    sidebar.classList.remove('collapsed', 'show');
                    backdrop.classList.remove('show');
                    mainContent.classList.remove('expanded');
                }
            }

            // Initialize
            handleResize();
            window.addEventListener('resize', handleResize);
        });
    </script>
    @stack('scripts')
</body>