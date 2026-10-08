<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarOpen: true,
    darkMode: localStorage.getItem('darkMode') === 'true',
    mobileSidebarOpen: false
}" x-init="darkMode = localStorage.getItem('darkMode') === 'true';
$watch('darkMode', val => localStorage.setItem('darkMode', val));
// Handle responsive
function handleResize() {
    if (window.innerWidth >= 1024) {
        sidebarOpen = true;
        mobileSidebarOpen = false;
    } else {
        sidebarOpen = false;
    }
}
handleResize();
window.addEventListener('resize', handleResize);"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZS Academy') }} - @yield('title', 'Dashboard')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon-192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Sidebar transition */
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }

        /* Smooth transition untuk bottom sheet */
        .bottom-sheet-content {
            transition: transform 0.2s ease-out;
        }

        /* Active state saat di-drag */
        .bottom-sheet-content.dragging {
            transition: none;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .dark ::-webkit-scrollbar-track {
            background: #374151;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #6B7280;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Top Navbar -->
        @include('components.navbar')

        <!-- Mobile Menu Overlay - Hanya muncul saat mobile sidebar terbuka -->
        <div x-show="mobileSidebarOpen" x-transition.opacity.duration.300 @click="mobileSidebarOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" style="display: none;"></div>

        <div class="flex">
            <!-- Sidebar - Desktop -->
            <div x-show="sidebarOpen" x-transition.duration.300
                class="hidden lg:block lg:fixed lg:top-16 lg:left-0 lg:h-[calc(100vh-4rem)] z-30 sidebar-transition"
                :class="sidebarOpen ? 'w-64' : 'w-20'">
                @auth
                    @if (auth()->user()->hasRole('admin'))
                        @include('components.sidebar.admin-sidebar')
                    @elseif(auth()->user()->hasRole('event_manager'))
                        @include('components.sidebar.event-manager-sidebar')
                    @elseif(auth()->user()->hasRole('instructor'))
                        @include('components.sidebar.instructor-sidebar')
                    @elseif(auth()->user()->hasRole('author'))
                        @include('components.sidebar.author-sidebar')
                    @elseif(auth()->user()->hasRole('editor'))
                        @include('components.sidebar.editor-sidebar')
                    @elseif(auth()->user()->hasRole('support'))
                        @include('components.sidebar.support-sidebar')
                    @else
                        @include('components.sidebar.student-sidebar')
                    @endif
                @endauth
            </div>

            <!-- Main Content -->
            <main class="flex-1 min-h-screen transition-all duration-300 w-full"
                :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
                <div class="p-4 md:p-6">
                    <div class="max-w-7xl mx-auto">
                        <!-- Breadcrumb -->
                        @hasSection('breadcrumb')
                            <div class="mb-3">
                                @yield('breadcrumb')
                            </div>
                        @endif

                        <!-- Page Header -->
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-gray-600 dark:text-gray-400">@yield('page-subtitle', 'Selamat datang kembali, ' . Auth::user()->name)</p>
                        </div>

                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Main Content Slot -->
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>

        <!-- Footer -->
        @include('components.footer')
    </div>

    <!-- Mobile Bottom Sheet Sidebar -->
    <div x-show="mobileSidebarOpen" x-transition.duration.300 class="fixed inset-x-0 bottom-0 z-50 lg:hidden"
        x-data="{
            startY: 0,
            currentY: 0,
            isDragging: false,
            handleTouchStart(e) {
                this.startY = e.touches[0].clientY;
                this.isDragging = true;
            },
            handleTouchMove(e) {
                if (!this.isDragging) return;
                this.currentY = e.touches[0].clientY;
                const diff = this.currentY - this.startY;
                if (diff > 0) {
                    // Drag ke bawah, geser bottom sheet
                    const sheet = $el.querySelector('.bottom-sheet-content');
                    if (sheet) {
                        const transform = Math.min(diff, 200);
                        sheet.style.transform = `translateY(${transform}px)`;
                    }
                }
            },
            handleTouchEnd(e) {
                if (!this.isDragging) return;
                const diff = this.currentY - this.startY;
                if (diff > 100) {
                    // Swipe lebih dari 100px, tutup bottom sheet
                    mobileSidebarOpen = false;
                }
                // Reset transform
                const sheet = $el.querySelector('.bottom-sheet-content');
                if (sheet) {
                    sheet.style.transform = '';
                }
                this.isDragging = false;
                this.startY = 0;
                this.currentY = 0;
            }
        }" @touchstart="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd"
        style="display: none;">

        <!-- Drag Handle -->
        <div class="flex justify-center -mb-2">
            <div class="w-12 h-1 bg-gray-400 rounded-full"></div>
        </div>

        <!-- Bottom Sheet Content -->
        <div
            class="bottom-sheet-content bg-white dark:bg-gray-800 rounded-t-2xl shadow-xl max-h-[80vh] overflow-y-auto transition-transform duration-300">
            <!-- Header with user info -->
            <div class="p-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img src="{{ Auth::user()->avatar_url }}"
                            alt="{{ Auth::user()->name }}" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <p class="text-base font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @if (auth()->user()->hasRole('admin'))
                                    Administrator
                                @elseif(auth()->user()->hasRole('event_manager'))
                                    Event Manager
                                @elseif(auth()->user()->hasRole('instructor'))
                                    Instruktur
                                @elseif(auth()->user()->hasRole('author'))
                                    Author
                                @elseif(auth()->user()->hasRole('editor'))
                                    Editor
                                @elseif(auth()->user()->hasRole('support'))
                                    Support
                                @else
                                    Mahasiswa
                                @endif
                            </p>
                        </div>
                    </div>
                    <button @click="mobileSidebarOpen = false"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Menu -->
            @auth
                @if (auth()->user()->hasRole('admin'))
                    @include('components.sidebar.mobile-admin-sidebar')
                @elseif(auth()->user()->hasRole('event_manager'))
                    @include('components.sidebar.mobile-event-manager-sidebar')
                @elseif(auth()->user()->hasRole('instructor'))
                    @include('components.sidebar.mobile-instructor-sidebar')
                @elseif(auth()->user()->hasRole('author'))
                    @include('components.sidebar.mobile-author-sidebar')
                @elseif(auth()->user()->hasRole('editor'))
                    @include('components.sidebar.mobile-editor-sidebar')
                @elseif(auth()->user()->hasRole('support'))
                    @include('components.sidebar.mobile-support-sidebar')
                @else
                    @include('components.sidebar.mobile-student-sidebar')
                @endif
            @endauth
        </div>
    </div>

    <!-- Toast Component -->
    <x-toast position="top-right" />

    <!-- Confirm Dialog Component -->
    <x-confirm-dialog />

    @stack('scripts')
</body>

</html>
