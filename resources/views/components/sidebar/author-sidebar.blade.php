<div class="h-full bg-white dark:bg-gray-800 shadow-lg flex flex-col overflow-y-auto">
    <!-- User Info Section -->
    <div class="p-4 border-b dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <img src="{{ Auth::user()->avatar_url }}"
                alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
            <div x-show="sidebarOpen">
                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Author</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3 space-y-1">
        <a href="{{ route('author.dashboard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('author.dashboard') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Dashboard</span>
        </a>

        <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pt-4 pb-2">
            Artikel
        </div>
        <a href="{{ route('author.articles.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('author.articles.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Artikel Saya</span>
        </a>

        <a href="{{ route('author.articles.create') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('author.articles.create') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Tulis Artikel</span>
        </a>

        <a href="{{ route('author.categories.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('author.categories.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.023.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Kategori</span>
        </a>

        <a href="{{ route('author.comments.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('author.comments.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Komentar</span>
        </a>

        <!-- Divider -->
        <div class="pt-4 mt-4 border-t dark:border-gray-700">
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
