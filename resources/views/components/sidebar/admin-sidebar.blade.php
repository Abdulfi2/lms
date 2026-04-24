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
            ],
        ],
        [
            'label' => 'Manajemen Kursus',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
            'submenu' => [
                ['label' => 'Semua Kursus', 'route' => 'admin.courses.index'],
                ['label' => 'Kategori', 'route' => 'admin.categories.index'],
                ['label' => 'Sertifikat', 'route' => '#'],
            ],
        ],
        [
            'label' => 'Analytics',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>',
            'route' => '#',
        ],
        [
            'label' => 'Pembayaran',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
            'route' => '#',
        ],
        [
            'label' => 'Pengaturan',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
            'submenu' => [
                ['label' => 'Umum', 'route' => '#'],
                ['label' => 'Email', 'route' => '#'],
                ['label' => 'Payment Gateway', 'route' => '#'],
            ],
        ],
    ];
@endphp

<aside x-show="sidebarOpen" x-transition.duration.300
    class="fixed lg:static inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-all duration-300 overflow-y-auto"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'">

    <div class="p-4">
        <!-- Profile Header -->
        <div class="p-4 border-b dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=3B82F6&color=white' }}"
                    alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="space-y-1 mt-4">
            @foreach ($menuItems as $item)
                @if (isset($item['submenu']))
                    <!-- Menu dengan submenu (dropdown) -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                            <div class="flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $item['icon'] !!}
                                </svg>
                                <span x-show="sidebarOpen" class="ml-3 text-sm">{{ $item['label'] }}</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="sidebarOpen && open" x-collapse class="ml-6 mt-1 space-y-1">
                            @foreach ($item['submenu'] as $sub)
                                @php
                                    $subIsHash = ($sub['route'] ?? '') === '#';
                                    $subHref = $subIsHash ? '#' : route($sub['route'], [], false);
                                @endphp
                                <a href="{{ $subHref }}"
                                    class="flex items-center px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm {{ !$subIsHash && request()->routeIs($sub['route']) ? 'bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}">
                                    <span>{{ $sub['label'] }}</span>
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
                        class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition group {{ !$isHash && request()->routeIs($item['route']) ? 'bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['icon'] !!}
                        </svg>
                        <span x-show="sidebarOpen" class="ml-3 text-sm">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </div>
</aside>
