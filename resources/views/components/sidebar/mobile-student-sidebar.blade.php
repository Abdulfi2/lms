<nav class="flex-1 p-4 space-y-1 overflow-y-auto">
    <!-- Main Menu -->
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pb-2">
        @lang('sidebar.main')
    </div>

    <!-- Dashboard -->
    <a href="{{ route('student.dashboard') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.dashboard') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.dashboard')</span>
    </a>

    <!-- My Courses -->
    <a href="{{ route('student.my-courses') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.my-courses') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.my_courses')</span>
        @php
            $enrolledCount = App\Models\Enrollment::where('user_id', Auth::id())->where('status', 'active')->count();
        @endphp
        @if ($enrolledCount > 0)
            <span class="ml-auto bg-primary/10 text-primary text-xs px-2 py-1 rounded-full">{{ $enrolledCount }}</span>
        @endif
    </a>

    <!-- Browse Courses -->
    <a href="{{ route('courses.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('courses.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.browse_courses')</span>
    </a>

    <!-- Wishlist -->
    <a href="{{ route('student.wishlist.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.wishlist.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.wishlist')</span>
    </a>

    <!-- Learning Section -->
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pt-4 pb-2">
        @lang('sidebar.learning')
    </div>

    <!-- Assignments -->
    <a href="{{ route('student.assignments.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.assignments.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.assignments')</span>
        @php
            $pendingAssignments = App\Models\Submission::where('student_id', Auth::id())
                ->where('status', 'submitted')
                ->count();
        @endphp
        @if ($pendingAssignments > 0)
            <span
                class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingAssignments }}</span>
        @endif
    </a>

    <!-- Certificates -->
    <a href="{{ route('student.certificates.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.certificates.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.certificates')</span>
        @php
            $certificateCount = App\Models\Certificate::where('user_id', Auth::id())->count();
        @endphp
        @if ($certificateCount > 0)
            <span
                class="ml-auto bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">{{ $certificateCount }}</span>
        @endif
    </a>

    <!-- Quizzes -->
    <a href="{{ route('student.quizzes.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.quizzes.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.quizzes')</span>
        @php
            $pendingQuizzes = App\Models\QuizAttempt::where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->count();
        @endphp
        @if ($pendingQuizzes > 0)
            <span class="ml-auto bg-purple-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingQuizzes }}</span>
        @endif
    </a>

    <!-- Community Section -->
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pt-4 pb-2">
        @lang('sidebar.community')
    </div>

    <!-- Forum -->
    <a href="{{ route('student.forums.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.forums.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.forum')</span>
        @php
            $unreadPosts = App\Models\Post::whereHas('thread', function ($q) {
                $q->whereHas('forum.course.enrollments', function ($q2) {
                    $q2->where('user_id', Auth::id());
                });
            })
                ->where('created_at', '>', Auth::user()->last_seen_at ?? now()->subDays(7))
                ->count();
        @endphp
        @if ($unreadPosts > 0)
            <span
                class="ml-auto bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadPosts > 99 ? '99+' : $unreadPosts }}</span>
        @endif
    </a>

    <!-- Events -->
    <a href="{{ route('student.events.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.events.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.events')</span>
        @php
            $upcomingEvents = App\Models\EventRegistration::where('user_id', Auth::id())
                ->whereHas('event', function ($q) {
                    $q->where('start_time', '>', now());
                })
                ->count();
        @endphp
        @if ($upcomingEvents > 0)
            <span class="ml-auto bg-pink-500 text-white text-xs px-2 py-1 rounded-full">{{ $upcomingEvents }}</span>
        @endif
    </a>

    <!-- Achievements Section -->
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 pt-4 pb-2">
        @lang('sidebar.achievements')
    </div>

    <!-- Achievements -->
    <a href="{{ route('student.achievements.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.achievements.*') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.achievements')</span>
        @php
            $newAchievements = App\Models\UserAchievement::where('user_id', Auth::id())
                ->where('created_at', '>', Auth::user()->last_seen_at ?? now()->subDays(7))
                ->count();
        @endphp
        @if ($newAchievements > 0)
            <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $newAchievements }}</span>
        @endif
    </a>

    <!-- Leaderboard -->
    <a href="{{ route('student.leaderboard') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.leaderboard')</span>
    </a>

    <!-- Divider -->
    <div class="my-3 border-t dark:border-gray-700"></div>

    <!-- Settings -->
    <a href="{{ route('settings.index') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span class="ml-3 text-sm font-medium">@lang('sidebar.settings')</span>
    </a>

    <!-- Badges Link (Optional) -->
    <a href="{{ route('student.badges') }}" @click="mobileSidebarOpen = false"
        class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span class="ml-3 text-sm font-medium">Lencana</span>
    </a>

    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit"
            class="w-full flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="ml-3 text-sm font-medium">@lang('sidebar.logout')</span>
        </button>
    </form>
</nav>
