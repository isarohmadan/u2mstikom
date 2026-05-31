<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - LMS STIKOM</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/U2M.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left Side - Reset Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Atur Ulang Kata Sandi</h1>
                    <p class="text-gray-600">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
                </div>
                
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form class="space-y-6" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan alamat email Anda"
                                value="{{ old('email') }}"
                                autofocus>
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-white font-medium btn-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Kirim Tautan Atur Ulang Kata Sandi
                        </button>
                    </div>
                    
                    <!-- Back to Login -->
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Masuk
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Side - Image -->
        <div class="hidden md:flex md:w-1/2 bg-login items-center justify-center p-12">
            <div class="text-center text-white max-w-md">
                <div class="bg-white bg-opacity-20 p-8 rounded-2xl">
                    <div class="bg-white rounded-full w-32 h-32 overflow-hidden flex items-center justify-center mx-auto mb-6">
                        <img class="w-full h-full object-cover" src="{{ asset('storage/U2M.png') }}" alt="U2M Logo">
                    </div>
                    <h2 class="text-2xl font-bold mb-2">KMS U2M</h2>
                    <p class="text-blue-100">Knowledge Management System Untuk UKM U2M</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Footer -->
    <div class="md:hidden bg-login py-8 px-4">
        <div class="text-center text-white">
            <h2 class="text-2xl font-bold mb-2">KMS U2M</h2>
            <p class="text-blue-100">Knowledge Management System Untuk UKM U2M</p>
        </div>
    </div>
</body>
</html>
