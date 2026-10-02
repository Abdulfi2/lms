@php
    // Menu structure - mudah ditambah dan diatur
    $menuItems = [
        [
            'label' => 'Dashboard',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
            'route' => 'admin.dashboard',
        ],
        [
            'label' => 'Manajemen User',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>',
            'submenu' => [
                ['label' => 'Semua User', 'route' => 'admin.users.index'],
                ['label' => 'Tambah User', 'route' => 'admin.users.create'],
                ['label' => 'Manajemen Role', 'route' => 'admin.roles.index'],
                ['label' => 'Manajemen Permission', 'route' => 'admin.permissions.index'],
            ],
        ],
        [
            'label' => 'Manajemen Kursus',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
            'submenu' => [
                ['label' => 'Semua Kursus', 'route' => 'admin.courses.index'],
                ['label' => 'Kategori', 'route' => 'admin.categories.index'],
                ['label' => 'Tag', 'route' => 'admin.tags.index'],
                ['label' => 'Sertifikat', 'route' => 'admin.certificates.index'],
                ['label' => 'Statistik Wishlist', 'route' => 'admin.wishlists.index'],
                ['label' => 'Kupon & Diskon', 'route' => 'admin.coupons.index'],
            ],
        ],
        [
            'label' => 'Gamifikasi',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>',
            'submenu' => [
                ['label' => 'Achievement', 'route' => 'admin.achievements.index'],
                ['label' => 'Badge', 'route' => 'admin.badges.index'],
                ['label' => 'Level', 'route' => 'admin.levels.index'],
            ],
        ],
        [
            'label' => 'Analytics',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>',
            'route' => 'admin.analytics',
        ],
        [
            'label' => 'Pembayaran',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
            'submenu' => [
                ['label' => 'Konfirmasi Pembayaran', 'route' => 'admin.enrollments.index'],
                ['label' => 'Payout Instruktur', 'route' => 'admin.payouts.index'],
            ],
        ],
        [
            'label' => 'Pesan Kontak',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>',
            'route' => 'admin.contact-messages.index',
        ],
        [
            'label' => 'Moderasi Forum',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
            'route' => 'admin.post-reports.index',
        ],
        [
            'label' => 'Ulasan',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>',
            'route' => 'admin.reviews.index',
        ],
        [
            'label' => 'Event',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
            'route' => 'admin.events.index',
        ],
        [
            'label' => 'Broadcast Notifikasi',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>',
            'route' => 'admin.notifications.index',
        ],
        [
            'label' => 'Failed Jobs',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
            'route' => 'admin.failed-jobs.index',
        ],
        [
            'label' => 'Activity Log',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
            'route' => 'admin.activity-logs.index',
        ],
        [
            'label' => 'Pengaturan',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
            'route' => 'admin.settings.index',
        ],
    ];
@endphp

<div class="h-full bg-white dark:bg-gray-800 shadow-lg flex flex-col overflow-y-auto">
    <!-- User Info Section -->
    <div class="p-4 border-b dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <img src="{{ Auth::user()->avatar_url }}"
                alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
            <div x-show="sidebarOpen">
                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->hasRole('admin') ? 'Administrator' : 'Event Manager' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3 space-y-1">
        @foreach ($menuItems as $item)
            @if (isset($item['submenu']))
                <!-- Menu dengan submenu (dropdown) -->
                <div x-data="{ open: {{ collect($item['submenu'])->contains(fn ($sub) => ($sub['route'] ?? '') !== '#' && request()->routeIs($sub['route'])) ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-all duration-200 group text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $item['icon'] !!}
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">{{ $item['label'] }}</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 shrink-0 transition-transform"
                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="sidebarOpen && open" x-collapse class="mt-1 ml-6 space-y-1">
                        @foreach ($item['submenu'] as $sub)
                            @php
                                $subIsHash = ($sub['route'] ?? '') === '#';
                                $subHref = $subIsHash ? '#' : route($sub['route'], [], false);
                            @endphp
                            <a href="{{ $subHref }}"
                                class="flex items-center px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ !$subIsHash && request()->routeIs($sub['route']) ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <span class="whitespace-nowrap">{{ $sub['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Menu single link -->
                @php
                    $isHash = ($item['route'] ?? '') === '#';
                    $href = $isHash ? '#' : route($item['route'], [], false);
                @endphp
                <a href="{{ $href }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ !$isHash && request()->routeIs($item['route']) ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm whitespace-nowrap">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</div>
