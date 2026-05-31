<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LMS STIKOM</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/U2M.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left Side - Login Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang Kembali!</h1>
                    <p class="text-gray-600">Silakan masuk ke akun Anda</p>
                </div>
                
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan email Anda"
                                value="{{ old('email') }}">
                            </div>
                        </div>
                        
                        <!-- Password Input -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="password" name="password" type="password" required 
                                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Masukkan kata sandi Anda">
                                <button type="button" id="toggle-password" tabindex="-1"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none"
                                    onclick="togglePassword()">
                                    <i id="eye-icon" class="fas fa-eye"></i>
                                </button>
                                <script>
                                    function togglePassword() {
                                        const passwordField = document.getElementById('password');
                                        const eyeIcon = document.getElementById('eye-icon');
                                        if (passwordField.type === 'password') {
                                            passwordField.type = 'text';
                                            eyeIcon.classList.remove('fa-eye');
                                            eyeIcon.classList.add('fa-eye-slash');
                                        } else {
                                            passwordField.type = 'password';
                                            eyeIcon.classList.remove('fa-eye-slash');
                                            eyeIcon.classList.add('fa-eye');
                                        }
                                    }
                                </script>
                        </div>
                    </div>
                    
                    @if ($errors->any())
                        <div class="text-red-500 text-sm p-3 bg-red-50 rounded-lg">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-white font-medium btn-login focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Side - Image -->
        <div class="hidden md:flex md:w-1/2 bg-login items-center justify-center p-12">
            <div class="text-center text-white max-w-md">
                <div class="bg-white bg-opacity-20 p-8 rounded-2xl">
                    <div class="bg-white rounded-full w-32 h-32 overflow-hidden flex items-center justify-center mx-auto mb-6">
                        <img class="w-full h-full object-cover"  src="{{ asset('storage/U2M.png') }}" alt="U2M Logo">
                    </div>
                    <h2 class="text-3xl font-bold mb-4">KMS U2M</h2>
                    <p class="text-blue-100 mb-8">Knowledge Management System Untuk UKM U2M </p>
                    <div class="w-16 h-1 bg-white bg-opacity-50 mx-auto"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile View - Bottom Image -->
    <div class="md:hidden bg-login py-8 px-4">
        <div class="text-center text-white">
            <h2 class="text-2xl font-bold mb-2">KMS U2M</h2>
            <p class="text-blue-100">Knowledge Management System Untuk UKM U2M</p>
        </div>
    </div>
</body>
</html> 