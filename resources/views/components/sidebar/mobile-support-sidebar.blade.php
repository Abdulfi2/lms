@php
    $supportPendingCount = \App\Models\Ticket::whereIn('status', ['baru', 'diproses'])->count();
@endphp
<nav class="flex-1 p-4 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('support.dashboard') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.dashboard') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Dashboard</span>
    </a>

    <!-- Tiket Masuk -->
    <a href="{{ route('support.tickets.index', ['tab' => 'all']) }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.tickets.*') && request('tab') === 'all' ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Tiket Masuk</span>
    </a>

    <!-- Permintaan Bantuan -->
    <a href="{{ route('support.tickets.index', ['tab' => 'pending']) }}" @click="mobileSidebarOpen = false"
        class="flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.tickets.*') && request('tab', 'pending') === 'pending' ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <span class="flex items-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            <span class="ml-3 text-sm font-medium">Permintaan Bantuan</span>
        </span>
        @if ($supportPendingCount > 0)
            <span class="text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ $supportPendingCount }}</span>
        @endif
    </a>

    <!-- Pengguna -->
    <a href="{{ route('support.users.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.users.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Pengguna</span>
    </a>

    <!-- Basis Pengetahuan -->
    <a href="{{ route('support.knowledge-base.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.knowledge-base.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Basis Pengetahuan</span>
    </a>

    <!-- Laporan -->
    <a href="{{ route('support.reports.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.reports.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Laporan</span>
    </a>

    <!-- Komentar / Feedback -->
    <a href="{{ route('admin.post-reports.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.post-reports.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Komentar / Feedback</span>
    </a>

    <!-- Jadwal Support -->
    <a href="{{ route('support.schedule.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.schedule.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Jadwal Support</span>
    </a>

    <!-- Kursus -->
    <a href="{{ route('support.courses.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.courses.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Kursus</span>
    </a>

    <!-- Enrollment -->
    <a href="{{ route('support.enrollments.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('support.enrollments.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Enrollment</span>
    </a>

    <!-- Analytics -->
    <a href="{{ route('admin.analytics') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.analytics') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
            </path>
        </svg>
        <span class="ml-3 text-sm font-medium">Analytics</span>
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
