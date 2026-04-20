@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard Mahasiswa')
@section('page-subtitle', 'Selamat belajar, ' . Auth::user()->name . '!')

@section('content')
    <div x-data="studentDashboard()" x-init="init()">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Kursus Aktif</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="stats.activeCourses">0</p>
                        <p class="text-green-500 text-sm mt-2">Progress rata-rata 65%</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Sertifikat</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="stats.certificates">0</p>
                        <p class="text-green-500 text-sm mt-2">+2 bulan ini</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Poin</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="stats.points">0</p>
                        <p class="text-yellow-500 text-sm mt-2">Level <span x-text="stats.level">1</span></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Jam Belajar</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="stats.hours">0</p>
                        <p class="text-blue-500 text-sm mt-2">+5 jam minggu ini</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-xl shadow-lg p-6 mb-8 text-white">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Semangat Belajar!</h2>
                    <p class="text-white/80">Teruslah belajar dan tingkatkan skillmu. Kamu sudah menyelesaikan <span
                            x-text="stats.completedLessons"></span> dari <span x-text="stats.totalLessons"></span> materi.
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="w-48 text-center">
                        <div class="text-3xl font-bold" x-text="stats.progress + '%'">0</div>
                        <div class="w-full bg-white/30 rounded-full h-2 mt-2">
                            <div class="bg-white h-2 rounded-full" :style="'width: ' + stats.progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Courses Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Kursus Saya</h3>
                <a href="#" class="text-primary hover:text-secondary text-sm">Lihat Semua
                    →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="course in courses" :key="course.id">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        <div class="relative">
                            <img :src="course.thumbnail" alt="course.title" class="w-full h-40 object-cover">
                            <div class="absolute top-2 right-2 px-2 py-1 bg-green-500 text-white text-xs rounded-full"
                                x-text="course.progress + '%'"></div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 dark:text-white mb-2" x-text="course.title"></h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3" x-text="course.instructor"></p>
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress</span>
                                    <span x-text="course.progress + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full" :style="'width: ' + course.progress + '%'">
                                    </div>
                                </div>
                            </div>
                            <a :href="'/student/courses/' + course.slug"
                                class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                                Lanjutkan Belajar
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Recent Activities & Achievements -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activities -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    <template x-for="activity in activities" :key="activity.id">
                        <div class="flex items-start space-x-3 pb-3 border-b dark:border-gray-700">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                :class="{
                                    'bg-green-100': activity.type === 'completed',
                                    'bg-blue-100': activity.type === 'started',
                                    'bg-yellow-100': activity.type === 'certificate'
                                }">
                                <svg x-show="activity.type === 'completed'" class="w-4 h-4 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <svg x-show="activity.type === 'started'" class="w-4 h-4 text-blue-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <svg x-show="activity.type === 'certificate'" class="w-4 h-4 text-yellow-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 dark:text-white" x-text="activity.message"></p>
                                <p class="text-xs text-gray-500" x-text="activity.time"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Achievements / Badges -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Pencapaian</h3>
                <div class="grid grid-cols-3 gap-4">
                    <template x-for="badge in badges" :key="badge.name">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center"
                                :class="badge.earned ? 'bg-yellow-100' : 'bg-gray-100'">
                                <span class="text-2xl" x-text="badge.icon"></span>
                            </div>
                            <p class="text-xs font-medium mt-2" :class="badge.earned ? 'text-gray-800' : 'text-gray-400'"
                                x-text="badge.name"></p>
                            <p class="text-xs text-gray-500" x-show="badge.earned" x-text="badge.date"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Recommended Courses -->
        <div class="mt-8">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Rekomendasi untuk Anda</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <img src="https://placehold.co/400x200/3B82F6/white?text=Laravel" alt="Course"
                        class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-800 dark:text-white mb-1">Laravel 11 Mastery</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">By John Doe</p>
                        <div class="flex items-center mb-3">
                            <div class="flex text-yellow-400">
                                <span>★★★★★</span>
                            </div>
                            <span class="text-xs text-gray-500 ml-2">(123 rating)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-primary">Rp 299.000</span>
                            <button
                                class="px-3 py-1 bg-primary text-white rounded-lg hover:bg-secondary text-sm">Enroll</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function studentDashboard() {
                return {
                    stats: {
                        activeCourses: 4,
                        certificates: 2,
                        points: 1250,
                        hours: 48,
                        level: 3,
                        progress: 65,
                        completedLessons: 24,
                        totalLessons: 36
                    },
                    courses: [{
                            id: 1,
                            title: 'Laravel 10 dari Dasar hingga Mahir',
                            instructor: 'John Doe',
                            thumbnail: 'https://placehold.co/400x200/3B82F6/white?text=Laravel',
                            slug: 'laravel-10-mastery',
                            progress: 75
                        },
                        {
                            id: 2,
                            title: 'Vue.js 3 Composition API',
                            instructor: 'Jane Smith',
                            thumbnail: 'https://placehold.co/400x200/10B981/white?text=Vue',
                            slug: 'vuejs-3-composition',
                            progress: 45
                        },
                        {
                            id: 3,
                            title: 'Tailwind CSS untuk Pemula',
                            instructor: 'Mike Johnson',
                            thumbnail: 'https://placehold.co/400x200/8B5CF6/white?text=Tailwind',
                            slug: 'tailwind-css-beginner',
                            progress: 90
                        }
                    ],
                    activities: [{
                            id: 1,
                            type: 'completed',
                            message: 'Menyelesaikan "Eloquent ORM" di course Laravel',
                            time: '2 jam yang lalu'
                        },
                        {
                            id: 2,
                            type: 'started',
                            message: 'Memulai course "Vue.js 3 Composition API"',
                            time: '1 hari yang lalu'
                        },
                        {
                            id: 3,
                            type: 'certificate',
                            message: 'Mendapatkan sertifikat "JavaScript Dasar"',
                            time: '3 hari yang lalu'
                        },
                        {
                            id: 4,
                            type: 'completed',
                            message: 'Menyelesaikan kuis "Array Methods"',
                            time: '5 hari yang lalu'
                        }
                    ],
                    badges: [{
                            name: 'First Blood',
                            icon: '🏆',
                            earned: true,
                            date: '2 Jan 2024'
                        },
                        {
                            name: 'Fast Learner',
                            icon: '⚡',
                            earned: true,
                            date: '15 Feb 2024'
                        },
                        {
                            name: 'Perfect Score',
                            icon: '🎯',
                            earned: false,
                            date: ''
                        },
                        {
                            name: '100 Hours',
                            icon: '⏰',
                            earned: false,
                            date: ''
                        },
                        {
                            name: 'Certificate King',
                            icon: '👑',
                            earned: false,
                            date: ''
                        },
                        {
                            name: 'Top Student',
                            icon: '⭐',
                            earned: true,
                            date: '1 Mar 2024'
                        }
                    ],
                    init() {
                        // Fetch real data from API if needed
                        // this.fetchDashboardData();
                    },
                    async fetchDashboardData() {
                        try {
                            const response = await fetch('/api/student/dashboard');
                            const data = await response.json();
                            if (response.ok) {
                                this.stats = data.stats;
                                this.courses = data.courses;
                                this.activities = data.activities;
                                this.badges = data.badges;
                            }
                        } catch (error) {
                            console.error('Error fetching dashboard data:', error);
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
