<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-4 md:px-6 py-3 md:py-4">

        <!-- Left: Hamburger + Title -->
        <div class="flex items-center gap-3">
            <!-- Hamburger Menu (Mobile Only) -->
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden text-gray-600 hover:text-gray-800 hover:bg-gray-100 p-2 rounded-lg">
                <i class='bx bx-menu text-2xl'></i>
            </button>

            <!-- Page Title -->
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
                <p class="text-xs md:text-sm text-gray-600 hidden sm:block">@yield('subtitle', 'Welcome to FASTKEU')</p>
            </div>
        </div>

        <!-- Right: Actions & User Menu -->
        <div class="flex items-center gap-2 md:gap-4">

            <!-- Notifications -->
            <button class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                <i class='bx bx-bell text-xl md:text-2xl'></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <!-- Trigger Button -->
                <button @click="open = !open"
                        class="flex items-center gap-2 px-2 md:px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-7 h-7 md:w-8 md:h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xs md:text-sm">
                        {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                    </div>
                    <div class="text-left hidden md:block">
                        <p class="font-medium text-gray-700 text-sm">{{ auth()->user()->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->roles->first()->role_name ?? 'User' }}</p>
                    </div>
                    <i class='bx bx-chevron-down text-gray-600 hidden md:block'></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                     style="display: none;">

                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>

                    <!-- Menu Items -->
                    <a href="#" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                        <i class='bx bx-user-circle text-lg'></i>
                        <span class="text-sm">Profile</span>
                    </a>

                    <a href="#" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                        <i class='bx bx-cog text-lg'></i>
                        <span class="text-sm">Settings</span>
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-2"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition">
                            <i class='bx bx-log-out text-lg'></i>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</header>
