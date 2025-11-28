<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - FASTKEU</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Alpine Wrapper with sidebarOpen state -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"
             style="display: none;">
        </div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Navbar/Header -->
            @include('layouts.navbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
