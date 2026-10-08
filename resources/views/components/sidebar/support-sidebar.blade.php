@php
    $supportPendingCount = \App\Models\Ticket::whereIn('status', ['baru', 'diproses'])->count();
@endphp
<div class="h-full bg-white dark:bg-gray-800 shadow-lg flex flex-col overflow-y-auto">
    <!-- User Info Section -->
    <div class="p-4 border-b dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <img src="{{ Auth::user()->avatar_url }}"
                alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
            <div x-show="sidebarOpen">
                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Support Sistem</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3 space-y-1">
        <a href="{{ route('support.dashboard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.dashboard') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Dashboard</span>
        </a>

        <a href="{{ route('support.tickets.index', ['tab' => 'all']) }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.tickets.*') && request('tab') === 'all' ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Tiket Masuk</span>
        </a>

        <a href="{{ route('support.tickets.index', ['tab' => 'pending']) }}"
            class="flex items-center justify-between px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.tickets.*') && request('tab', 'pending') === 'pending' ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <span class="flex items-center">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Permintaan Bantuan</span>
            </span>
            @if ($supportPendingCount > 0)
                <span x-show="sidebarOpen" class="text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ $supportPendingCount }}</span>
            @endif
        </a>

        <a href="{{ route('support.users.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.users.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Pengguna</span>
        </a>

        <a href="{{ route('support.knowledge-base.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.knowledge-base.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Basis Pengetahuan</span>
        </a>

        <a href="{{ route('support.reports.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.reports.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Laporan</span>
        </a>

        <a href="{{ route('admin.post-reports.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.post-reports.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Komentar / Feedback</span>
        </a>

        <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pt-4 pb-2">
            Lainnya
        </div>

        <a href="{{ route('support.schedule.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.schedule.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Jadwal Support</span>
        </a>

        <a href="{{ route('support.courses.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.courses.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Kursus</span>
        </a>

        <a href="{{ route('support.enrollments.index') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('support.enrollments.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Enrollment</span>
        </a>

        <a href="{{ route('admin.analytics') }}"
            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.analytics') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">Analytics</span>
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
