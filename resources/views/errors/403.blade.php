<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center">
                <h1 class="display-1">403</h1>
                <h2 class="mb-4">Akses Tidak Diizinkan</h2>
                <p class="lead mb-4">Maaf, Anda tidak memiliki hak akses untuk mengakses halaman ini.</p>
                <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
                <a href="{{ route('login') }}" class="btn btn-secondary">Masuk</a>
            </div>
        </div>
    </div>
</body>
</html> 