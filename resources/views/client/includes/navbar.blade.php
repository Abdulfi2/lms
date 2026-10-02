<nav class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 shadow rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/logo-zakatsukses.png') }}" alt="Logo ZakatSukses" class="w-6">
                    </div>
                    <span
                        class="font-bold text-xl bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hidden sm:inline">
                        {{ config('app.name', 'ZS Academy') }}
                    </span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">Beranda</a>
                <a href="{{ route('courses.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">Kursus</a>
                <a href="{{ route('events.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">Event</a>
                <a href="{{ route('articles.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">Artikel</a>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=3B82F6&color=white' }}"
                                class="w-8 h-8 rounded-full object-cover">
                            <span
                                class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50">
                            <a href="{{ route('dashboard') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Dashboard
                            </a>
                            <a href="{{ route('profile') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Profil
                            </a>
                            <hr class="my-1 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="text-primary hover:text-secondary transition font-medium">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition shadow-md">
                        Daftar
                    </a>
                @endauth

                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center md:hidden space-x-2">
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div x-show="mobileMenuOpen" x-transition.duration.300
        class="md:hidden bg-white dark:bg-gray-800 border-t dark:border-gray-700 py-4">
        <div class="space-y-3 px-4">
            <a href="{{ route('home') }}"
                class="block text-gray-700 dark:text-gray-300 hover:text-primary transition py-2">Beranda</a>
            <a href="{{ route('courses.index') }}"
                class="block text-gray-700 dark:text-gray-300 hover:text-primary transition py-2">Kursus</a>
            <a href="{{ route('events.index') }}"
                class="block text-gray-700 dark:text-gray-300 hover:text-primary transition py-2">Event</a>
            <a href="{{ route('articles.index') }}"
                class="block text-gray-700 dark:text-gray-300 hover:text-primary transition py-2">Artikel</a>

            @auth
                <hr class="my-2 dark:border-gray-700">
                <a href="{{ route('dashboard') }}"
                    class="block text-gray-700 dark:text-gray-300 hover:text-primary transition py-2">Dashboard</a>
                <a href="{{ route('profile') }}"
                    class="block text-gray-700 dark:text-gray-300 hover:text-primary transition py-2">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-600 hover:text-red-700 py-2">Logout</button>
                </form>
            @else
                <hr class="my-2 dark:border-gray-700">
                <a href="{{ route('login') }}"
                    class="block text-center px-4 py-2 border border-primary text-primary rounded-lg mb-2">Masuk</a>
                <a href="{{ route('register', ['role' => 'student']) }}"
                    class="block text-center px-4 py-2 bg-primary text-white rounded-lg">Daftar</a>
            @endauth
        </div>
    </div>
</nav>
