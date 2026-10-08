<div class="h-full bg-white dark:bg-gray-800 shadow-lg flex flex-col overflow-y-auto">
    <!-- User Info Section -->
    <div class="p-4 border-b dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <img src="{{ Auth::user()->avatar_url }}"
                alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
            <div x-show="sidebarOpen">
                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Event Manager</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3 space-y-1">
        <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pt-2 pb-2">
            Event
        </div>

        <a href="{{ route('admin.events.dashboard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.events.dashboard') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Dashboard</span>
        </a>

        <a href="{{ route('admin.events.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.events.index', 'admin.events.show', 'admin.events.edit', 'admin.events.registrations') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Event Saya</span>
        </a>

        <a href="{{ route('admin.events.create') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.events.create') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Buat Event</span>
        </a>

        <!-- Divider -->
        <div class="pt-4 mt-4 border-t dark:border-gray-700">
            <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
                Pengaturan
            </div>
            <a href="{{ route('profile') }}"
                class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('profile') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Profil Saya</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </nav>
</div>
