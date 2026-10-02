<nav x-data="{ profileOpen: false, notificationsOpen: false }" class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side -->
            <div class="flex items-center">
                <!-- Mobile menu button - untuk membuka sidebar di mobile -->
                <button @click="mobileSidebarOpen = true"
                    class="lg:hidden mr-3 p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Desktop sidebar toggle -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="hidden lg:block p-2 rounded-lg text-gray-500 bg-gray-100 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg x-show="sidebarOpen" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" />
                    </svg>
                    <svg x-show="!sidebarOpen" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 ml-2">
                    <div class="w-8 h-8 shadow rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/logo-zakatsukses.png') }}" alt="Logo ZakatSukses" class="w-6">
                    </div>
                    <span
                        class="font-bold text-xl bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hidden sm:inline">
                        {{ config('app.name', 'ZS Academy') }}
                    </span>
                </a>
            </div>

            <!-- Center - Search (Desktop) -->
            <div class="hidden md:flex items-center flex-1 max-w-md mx-4">
                <div class="relative w-full">
                    <input type="text" placeholder="Cari kursus, materi..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-2">
                <!-- Language Switcher -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-1 px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        @php
                            $currentLang = app()->getLocale();
                        @endphp
                        @if ($currentLang == 'id')
                            <span class="text-sm font-medium">🇮🇩 Indonesia</span>
                        @else
                            <span class="text-sm font-medium">🇬🇧 English</span>
                        @endif
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition.duration.200
                        class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-50 border dark:border-gray-700"
                        style="display: none;">
                        <a href="{{ route('lang.switch', 'id') }}"
                            class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ app()->getLocale() == 'id' ? 'bg-primary/10 text-primary' : '' }}">
                            <span class="mr-2">🇮🇩</span> Indonesia
                            @if (app()->getLocale() == 'id')
                                <svg class="w-4 h-4 ml-auto text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </a>
                        <a href="{{ route('lang.switch', 'en') }}"
                            class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ app()->getLocale() == 'en' ? 'bg-primary/10 text-primary' : '' }}">
                            <span class="mr-2">🇬🇧</span> English
                            @if (app()->getLocale() == 'en')
                                <svg class="w-4 h-4 ml-auto text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </button>

                <!-- Notifications -->
                @php
                    $navNotifications = auth()->user()->appNotifications()->latest('sent_at')->limit(8)->get();
                    $navUnreadCount = auth()->user()->appNotifications()->unread()->count();
                @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        @if ($navUnreadCount > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition.duration.200
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50"
                        style="display: none;">
                        <div class="px-4 py-2 border-b dark:border-gray-700 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800 dark:text-white">Notifikasi</h3>
                            @if ($navUnreadCount > 0)
                                <span
                                    class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ $navUnreadCount }}
                                    baru</span>
                            @endif
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse ($navNotifications as $notification)
                                <a href="{{ route('notifications.read', $notification) }}"
                                    class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer {{ !$notification->read_at ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $notification->title }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Illuminate\Support\Str::limit($notification->message, 80) }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->sent_at->diffForHumans() }}
                                    </p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-gray-400">Belum ada notifikasi.</div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t dark:border-gray-700">
                            <a href="{{ route('notifications.index') }}"
                                class="text-xs text-primary hover:underline">Lihat semua notifikasi</a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-2 focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=3B82F6&color=white' }}"
                            alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover">
                        <span
                            class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                        <svg class="hidden md:block w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition.duration.200
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50"
                        style="display: none;">
                        <a href="{{ route('profile') }}"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                        <a href="{{ route('settings.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Pengaturan
                        </a>
                        <hr class="my-1 dark:border-gray-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
