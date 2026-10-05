<nav class="flex-1 p-4 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('author.dashboard') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('author.dashboard') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Dashboard</span>
    </a>

    <!-- Articles -->
    <a href="{{ route('author.articles.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('author.articles.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Artikel Saya</span>
    </a>

    <!-- Create Article -->
    <a href="{{ route('author.articles.create') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('author.articles.create') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="ml-3 text-sm font-medium">Tulis Artikel</span>
    </a>

    <!-- Categories -->
    <a href="{{ route('author.categories.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('author.categories.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 7h.01M7 3h5c.512 0 1.023.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Kategori</span>
    </a>

    <!-- Comments -->
    <a href="{{ route('author.comments.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('author.comments.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Komentar</span>
    </a>

    <div class="my-3 border-t dark:border-gray-700"></div>

    <!-- Profile Settings -->
    <a href="{{ route('profile') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Profil Saya</span>
    </a>

    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit"
            class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                </path>
            </svg>
            <span class="ml-3 text-sm font-medium">Logout</span>
        </button>
    </form>
</nav>
