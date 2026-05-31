<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMS UKM U2M - Knowledge Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .hero {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 6rem 0;
            margin-bottom: 3rem;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .how-it-works {
            background-color: #f8f9fc;
            padding: 4rem 0;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #2e59d9;
        }
        
        .btn-outline-light {
            border: 2px solid white;
            font-weight: 600;
            padding: 0.75rem 2rem;
        }
        
        .navbar {
            padding: 1rem 0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">KMS UKM U2M</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">Cara Kerja</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">Masuk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Tingkatkan Pengalaman Belajar Anda</h1>
                    <p class="lead mb-4">Akses materi pembelajaran, kirim tugas, dan lacak progres Anda di satu tempat dengan Knowledge Management System kami yang komprehensif.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg">Mulai</a>
                        <a href="#features" class="btn btn-outline-light btn-lg">Pelajari Lebih Lanjut</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="https://illustrations.popsy.co/white/online-learning.svg" alt="Online Learning" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Fitur Unggulan</h2>
                <p class="text-muted">Semua yang Anda butuhkan untuk pengalaman belajar yang efektif</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h4>Materi Pembelajaran</h4>
                            <p class="text-muted">Akses semua materi pembelajaran Anda di satu tempat, tersedia 24/7 dari perangkat apa pun.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h4>Pengiriman Tugas</h4>
                            <p class="text-muted">Kirim tugas secara online dan terima umpan balik tepat waktu dari instruktur Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4>Pelacakan Progres</h4>
                            <p class="text-muted">Pantau progres pembelajaran Anda dan capai tujuan akademik Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Cara Kerja</h2>
                <p class="text-muted">Mulai dalam beberapa langkah sederhana</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="step-number">1</div>
                        <h4>Masuk</h4>
                        <p>Akses akun Anda menggunakan kredensial Anda atau hubungi administrator untuk memulai.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="step-number">2</div>
                        <h4>Daftar Kelas</h4>
                        <p>Jelajahi kursus yang tersedia dan daftarkan diri pada kursus yang sesuai dengan kebutuhan akademik Anda.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="step-number">3</div>
                        <h4>Mulai Belajar</h4>
                        <p>Akses materi pembelajaran, selesaikan tugas, dan lacak progres Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center py-5">
            <h2 class="fw-bold mb-4">Siap untuk memulai?</h2>
            <p class="lead mb-4">Bergabunglah dengan ribuan siswa yang sudah menggunakan platform kami untuk meningkatkan pengalaman belajar mereka.</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5">Mulai Belajar Sekarang</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>KMS UKM U2M</h5>
                    <p class="text-muted">Memperkuat pendidikan melalui teknologi</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; {{ date('Y') }} KMS UKM U2M. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>