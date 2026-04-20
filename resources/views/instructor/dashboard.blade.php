@extends('layouts.app')

@section('title', 'Instructor Dashboard')
@section('page-title', 'Dashboard Instruktur')
@section('page-subtitle', 'Selamat mengajar, ' . Auth::user()->name . '!')

@section('content')
    <div x-data="instructorDashboard()" x-init="init()">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Kursus</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="stats.totalCourses">0</p>
                        <p class="text-green-500 text-sm mt-2">+2 bulan ini</p>
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
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="stats.totalStudents">0</p>
                        <p class="text-green-500 text-sm mt-2">+128 bulan ini</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white"
                            x-text="'Rp ' + formatNumber(stats.totalRevenue)">0</p>
                        <p class="text-green-500 text-sm mt-2">↑ 23% dari bulan lalu</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Rating Rata-rata</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="stats.averageRating">0</p>
                        <p class="text-green-500 text-sm mt-2">⭐ 4.8 dari 245 review</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-xl font-bold mb-2">Buat Kursus Baru</h3>
                <p class="text-blue-100 mb-4">Bagikan pengetahuan Anda dengan membuat kursus baru.</p>
                <a href="{{ '#' }}"
                    class="inline-block px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition">
                    + Buat Kursus
                </a>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-xl font-bold mb-2">Tugas Perlu Dinilai</h3>
                <p class="text-purple-100 mb-4">Anda memiliki <span x-text="stats.pendingAssignments"></span> tugas yang
                    perlu dinilai.</p>
                <a href="{{ '#' }}"
                    class="inline-block px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-gray-100 transition">
                    Lihat Tugas
                </a>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Statistik Pendapatan</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <!-- My Courses Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Kursus Saya</h3>
                <a href="{{ '#' }}" class="text-primary hover:text-secondary text-sm">Lihat Semua
                    →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Pendapatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <template x-for="course in courses" :key="course.id">
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img :src="course.thumbnail" alt="course.title"
                                            class="w-10 h-10 rounded object-cover mr-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white"
                                                x-text="course.title"></p>
                                            <p class="text-xs text-gray-500" x-text="course.created_at"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                                    x-text="course.students_count"></td>
                                <td class="px-6 py-4">
                                    <div class="w-32">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="text-gray-600 dark:text-gray-400">Completion</span>
                                            <span x-text="course.completion_rate + '%'"></span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full"
                                                :style="'width: ' + course.completion_rate + '%'"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600"
                                    x-text="'Rp ' + formatNumber(course.revenue)"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <span class="text-yellow-400 mr-1">⭐</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400"
                                            x-text="course.rating"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a :href="'/instructor/courses/' + course.id + '/edit'"
                                        class="text-primary hover:text-secondary text-sm">Edit</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pendaftaran Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <template x-for="enrollment in recentEnrollments" :key="enrollment.id">
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img :src="enrollment.student_avatar" class="w-8 h-8 rounded-full mr-3">
                                        <span class="text-sm text-gray-800 dark:text-white"
                                            x-text="enrollment.student_name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                                    x-text="enrollment.course_title"></td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="enrollment.enrolled_at"></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            function instructorDashboard() {
                return {
                    stats: {
                        totalCourses: 5,
                        totalStudents: 1250,
                        totalRevenue: 89500000,
                        averageRating: 4.8,
                        pendingAssignments: 12
                    },
                    courses: [{
                            id: 1,
                            title: 'Laravel 10 Mastery',
                            thumbnail: 'https://placehold.co/400x200/3B82F6/white?text=Laravel',
                            students_count: 450,
                            completion_rate: 75,
                            revenue: 45000000,
                            rating: 4.9,
                            created_at: 'Jan 2024'
                        },
                        {
                            id: 2,
                            title: 'Vue.js 3 Complete',
                            thumbnail: 'https://placehold.co/400x200/10B981/white?text=Vue',
                            students_count: 320,
                            completion_rate: 68,
                            revenue: 28000000,
                            rating: 4.7,
                            created_at: 'Feb 2024'
                        },
                        {
                            id: 3,
                            title: 'Tailwind CSS Master',
                            thumbnail: 'https://placehold.co/400x200/8B5CF6/white?text=Tailwind',
                            students_count: 280,
                            completion_rate: 82,
                            revenue: 16500000,
                            rating: 4.8,
                            created_at: 'Mar 2024'
                        }
                    ],
                    recentEnrollments: [{
                            id: 1,
                            student_name: 'Budi Santoso',
                            student_avatar: 'https://ui-avatars.com/api/?name=Budi',
                            course_title: 'Laravel 10 Mastery',
                            enrolled_at: '2 jam yang lalu'
                        },
                        {
                            id: 2,
                            student_name: 'Siti Aminah',
                            student_avatar: 'https://ui-avatars.com/api/?name=Siti',
                            course_title: 'Vue.js 3 Complete',
                            enrolled_at: '5 jam yang lalu'
                        },
                        {
                            id: 3,
                            student_name: 'Agus Wijaya',
                            student_avatar: 'https://ui-avatars.com/api/?name=Agus',
                            course_title: 'Tailwind CSS Master',
                            enrolled_at: '1 hari yang lalu'
                        }
                    ],
                    init() {
                        this.initChart();
                    },
                    initChart() {
                        const ctx = document.getElementById('revenueChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                                datasets: [{
                                    label: 'Pendapatan (Rp)',
                                    data: [15000000, 25000000, 35000000, 45000000, 55000000, 75000000],
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    },
                    formatNumber(num) {
                        return num.toLocaleString('id-ID');
                    }
                }
            }
        </script>
    @endpush
@endsection
