<!-- Sidebar: Hidden di mobile by default, slide in when sidebarOpen = true -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-blue-800 to-blue-900 text-white flex flex-col shadow-lg transition-transform duration-300 ease-in-out">

    <!-- Logo Header -->
    <div class="p-6 border-b border-blue-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white p-2 rounded-lg shadow">
                    <i class='bx bx-file text-blue-600 text-2xl'></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">FASTKEU</h1>
                    <p class="text-xs text-blue-200">Document System</p>
                </div>
            </div>

            <!-- Close Button (Mobile Only) -->
            <button @click="sidebarOpen = false"
                    class="lg:hidden text-white hover:bg-blue-700 p-2 rounded-lg transition">
                <i class='bx bx-x text-2xl'></i>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto p-4">
        <ul class="space-y-2">

            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-700 transition {{ request()->routeIs('dashboard') ? 'bg-blue-700 shadow' : '' }}">
                    <i class='bx bx-home-alt text-xl'></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <li class="pt-2 pb-2">
                <div class="border-t border-blue-700 opacity-50"></div>
                <p class="text-xs text-blue-300 mt-2 px-4 font-semibold uppercase tracking-wider">Documents</p>
            </li>

            <!-- Documents -->
            @if(auth()->user()->hasPermission('Documents', 'read'))
            <li>
                <a href="{{ route('indexDocuments') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-700 transition {{ request()->routeIs('indexDocuments') || request()->routeIs('showDocuments') || request()->routeIs('editDocuments') ? 'bg-blue-700 shadow' : '' }}">
                    <i class='bx bx-file text-xl'></i>
                    <span class="font-medium">Documents</span>
                </a>
            </li>
            @endif

            <!-- Document Types -->
            @if(auth()->user()->hasPermission('DocumentTypes', 'read'))
            <li>
                <a href="{{ route('indexDocumentTypes') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-700 transition {{ request()->routeIs('indexDocumentTypes') ? 'bg-blue-700 shadow' : '' }}">
                    <i class='bx bx-category text-xl'></i>
                    <span class="font-medium">Document Types</span>
                </a>
            </li>
            @endif

            <!-- Divider -->
            <li class="pt-2 pb-2">
                <div class="border-t border-blue-700 opacity-50"></div>
                <p class="text-xs text-blue-300 mt-2 px-4 font-semibold uppercase tracking-wider">System</p>
            </li>

            <!-- Users -->
            @if(auth()->user()->hasPermission('Users', 'read'))
            <li>
                <a href="#"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                    <i class='bx bx-user text-xl'></i>
                    <span class="font-medium">Users</span>
                </a>
            </li>
            @endif

            <!-- Activity Logs -->
            @if(auth()->user()->hasPermission('ActivityLogs', 'read'))
            <li>
                <a href="#"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                    <i class='bx bx-history text-xl'></i>
                    <span class="font-medium">Activity Logs</span>
                </a>
            </li>
            @endif

        </ul>
    </nav>

    <!-- User Info Footer -->
    <div class="p-4 border-t border-blue-700">
        <!-- User Card -->
        <div class="bg-blue-800 rounded-lg p-3 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm truncate">{{ auth()->user()->full_name }}</p>
                    <p class="text-xs text-blue-200 truncate">{{ auth()->user()->roles->first()->role_name ?? 'User' }}</p>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow">
                <i class='bx bx-log-out text-lg'></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>
