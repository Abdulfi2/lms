<aside x-show="sidebarOpen" 
       x-transition.duration.300
       class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-all duration-300 overflow-y-auto"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'">

    <div class="p-4">
        <!-- User Info Section -->
        <div class="flex flex-col items-center p-4 border-b dark:border-gray-700">
            <div class="relative">
                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=3B82F6&color=white' }}"
                     alt="{{ Auth::user()->name }}" 
                     class="w-16 h-16 rounded-full object-cover border-2 border-primary">
                <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div class="mt-3 text-center" x-show="sidebarOpen">
                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Instruktur</p>
            </div>
        </div>

        <!-- In collapsed mode -->
        <div class="flex flex-col items-center pt-3" x-show="!sidebarOpen && window.innerWidth >= 1024">
            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=3B82F6&color=white' }}"
                 alt="{{ Auth::user()->name }}" 
                 class="w-10 h-10 rounded-full object-cover">
            <div class="mt-2 text-center">
                <div class="w-2 h-2 bg-green-500 rounded-full mx-auto"></div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="mt-4 space-y-1">
            <!-- DASHBOARD -->
            <div class="mb-2">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
                    Overview
                </div>
                <a href="{{ route('instructor.dashboard') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.dashboard') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Dashboard</span>
                </a>
            </div>

            <!-- COURSE MANAGEMENT -->
            <div class="mb-2">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
                    Kursus
                </div>
                <a href="{{ route('instructor.courses.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.courses.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Semua Kursus</span>
                </a>

                <a href="{{ route('instructor.courses.create') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.courses.create') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Buat Kursus Baru</span>
                </a>
            </div>

            <!-- ASSESSMENT -->
            <div class="mb-2">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
                    Penilaian
                </div>
                <a href="{{ route('instructor.assignments.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.assignments.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Buat &amp; Kelola Tugas</span>
                    @php
                        $pendingGrading = App\Models\Submission::whereHas('assignment.course', function($q) {
                            $q->where('instructor_id', auth()->id());
                        })->where('status', 'submitted')->count();
                    @endphp
                    @if($pendingGrading > 0)
                        <span x-show="sidebarOpen" class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingGrading }}</span>
                    @endif
                </a>

                <a href="{{ route('instructor.submissions.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.submissions.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Nilai Tugas Siswa</span>
                    @php
                        $pendingSubmissions = App\Models\Submission::whereHas('assignment.course', function($q) {
                            $q->where('instructor_id', auth()->id());
                        })->where('status', 'submitted')->count();
                    @endphp
                    @if($pendingSubmissions > 0)
                        <span x-show="sidebarOpen" class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingSubmissions }}</span>
                    @endif
                </a>

                <a href="{{ route('instructor.courses.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.courses.quizzes.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    title="Pilih kursus terlebih dahulu untuk mengelola quiz-nya">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Manajemen Quiz</span>
                </a>
            </div>

            <!-- STUDENTS -->
            <div class="mb-2">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
                    Siswa
                </div>
                <a href="{{ route('instructor.students.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.students.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Semua Siswa</span>
                    @php
                        $totalStudents = App\Models\Enrollment::whereHas('course', function($q) {
                            $q->where('instructor_id', auth()->id());
                        })->distinct('user_id')->count('user_id');
                    @endphp
                    @if($totalStudents > 0)
                        <span x-show="sidebarOpen" class="ml-auto bg-primary/10 text-primary text-xs px-2 py-0.5 rounded-full">{{ $totalStudents }}</span>
                    @endif
                </a>
            </div>

            <!-- ANALYTICS & EARNINGS -->
            <div class="mb-2">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
                    Laporan
                </div>
                <a href="{{ route('instructor.analytics') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.analytics') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Analytics</span>
                </a>

                <a href="{{ route('instructor.earnings.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.earnings.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Pendapatan</span>
                    @php
                        $totalEarnings = App\Models\Payment::whereHas('enrollment.course', function($q) {
                            $q->where('instructor_id', auth()->id());
                        })->where('status', 'completed')->sum('amount');
                    @endphp
                    @if($totalEarnings > 0)
                        <span x-show="sidebarOpen" class="ml-auto bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</span>
                    @endif
                </a>

                <a href="{{ route('instructor.reviews.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.reviews.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Ulasan Kursus</span>
                </a>

                <a href="{{ route('instructor.announcements.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.announcements.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Pengumuman</span>
                </a>

                <a href="{{ route('instructor.certificates.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.certificates.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Sertifikat Siswa</span>
                </a>

                <a href="{{ route('instructor.coupons.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.coupons.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Pemakaian Kupon</span>
                </a>
            </div>

            <!-- FORUM & COMMUNITY -->
            <div class="mb-2">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
                    Komunitas
                </div>
                <a href="{{ route('instructor.forums.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.forums.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Forum Diskusi</span>
                </a>
            </div>

            <!-- SETTINGS & PROFILE -->
            <div class="pt-4 mt-4 border-t dark:border-gray-700">
                <a href="{{ route('instructor.public-profile.edit') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('instructor.public-profile.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm">Profil Publik</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="sidebarOpen" class="ml-3 text-sm">Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>
</aside>