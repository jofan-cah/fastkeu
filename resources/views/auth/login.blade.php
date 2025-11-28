<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FASTKEU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-500 to-blue-700 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-block bg-white p-4 rounded-full shadow-lg mb-4">
                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">FASTKEU</h1>
            <p class="text-blue-100">Sistem Administrasi & Dokumen FiberOne</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login</h2>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    <i class='bx bx-check-circle mr-2'></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="admin@fastkeu.id">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input id="remember"
                           type="checkbox"
                           name="remember"
                           {{ old('remember') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-700">
                        Ingat saya
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    <i class='bx bx-log-in mr-2'></i>
                    Masuk
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-center text-sm text-gray-600 mb-2">
                    <i class='bx bx-info-circle'></i> Demo Account:
                </p>
                <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-600 space-y-1">
                    <p><strong>Admin:</strong> admin@fastkeu.id / admin123</p>
                    <p><strong>Finance:</strong> finance@fastkeu.id / finance123</p>
                    <p><strong>CS:</strong> cs@fastkeu.id / cs123</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-blue-100 text-sm">
                &copy; {{ date('Y') }} FASTKEU - FiberOne Indonesia
            </p>
        </div>
    </div>

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</body>
</html>
