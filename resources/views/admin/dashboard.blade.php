@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Overview sistem dan statistik')

@section('content')
    <div class="space-y-6">
        @php
            $needsAttention = $pendingInstructorApprovals + $pendingPayments + $pendingReviews + $pendingCourses + $pendingPostReports + $newContactMessages + $failedJobsCount;
        @endphp

        <!-- Butuh Perhatian -->
        @if ($needsAttention > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Butuh Perhatian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @if ($pendingInstructorApprovals > 0)
                        <a href="{{ route('admin.users.index', ['status' => 'pending_approval']) }}"
                            class="flex items-center justify-between p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition">
                            <div>
                                <p class="text-sm text-yellow-800 dark:text-yellow-400">Instruktur Menunggu Approval</p>
                                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $pendingInstructorApprovals }}</p>
                            </div>
                        </a>
                    @endif
                    @if ($pendingPayments > 0)
                        <a href="{{ route('admin.enrollments.index') }}"
                            class="flex items-center justify-between p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition">
                            <div>
                                <p class="text-sm text-orange-800 dark:text-orange-400">Pembayaran Menunggu Konfirmasi</p>
                                <p class="text-2xl font-bold text-orange-700 dark:text-orange-300">{{ $pendingPayments }}</p>
                            </div>
                        </a>
                    @endif
                    @if ($pendingReviews > 0)
                        <a href="{{ route('admin.reviews.index') }}"
                            class="flex items-center justify-between p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                            <div>
                                <p class="text-sm text-blue-800 dark:text-blue-400">Ulasan Menunggu Approval</p>
                                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $pendingReviews }}</p>
                            </div>
                        </a>
                    @endif
                    @if ($pendingCourses > 0)
                        <a href="{{ route('admin.courses.index', ['status' => 'pending']) }}"
                            class="flex items-center justify-between p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                            <div>
                                <p class="text-sm text-purple-800 dark:text-purple-400">Kursus Menunggu Approval</p>
                                <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $pendingCourses }}</p>
                            </div>
                        </a>
                    @endif
                    @if ($pendingPostReports > 0)
                        <a href="{{ route('admin.post-reports.index') }}"
                            class="flex items-center justify-between p-4 rounded-lg bg-pink-50 dark:bg-pink-900/20 hover:bg-pink-100 dark:hover:bg-pink-900/30 transition">
                            <div>
                                <p class="text-sm text-pink-800 dark:text-pink-400">Laporan Forum Menunggu</p>
                                <p class="text-2xl font-bold text-pink-700 dark:text-pink-300">{{ $pendingPostReports }}</p>
                            </div>
                        </a>
                    @endif
                    @if ($newContactMessages > 0)
                        <a href="{{ route('admin.contact-messages.index') }}"
                            class="flex items-center justify-between p-4 rounded-lg bg-teal-50 dark:bg-teal-900/20 hover:bg-teal-100 dark:hover:bg-teal-900/30 transition">
                            <div>
                                <p class="text-sm text-teal-800 dark:text-teal-400">Pesan Kontak Baru</p>
                                <p class="text-2xl font-bold text-teal-700 dark:text-teal-300">{{ $newContactMessages }}</p>
                            </div>
                        </a>
                    @endif
                    @if ($failedJobsCount > 0)
                        <a href="{{ route('admin.failed-jobs.index') }}"
                            class="flex items-center justify-between p-4 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                            <div>
                                <p class="text-sm text-red-800 dark:text-red-400">Failed Jobs</p>
                                <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $failedJobsCount }}</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Users</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalUsers) }}</p>
                        <p class="text-green-500 text-sm mt-2">+{{ $newUsersThisMonth }} bulan ini</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Courses</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalCourses) }}</p>
                        <p class="text-green-500 text-sm mt-2">+{{ $newCoursesThisMonth }} kursus baru bulan ini</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        <p class="text-green-500 text-sm mt-2">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }} bulan ini</p>
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

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Completion Rate</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $completionRate }}%</p>
                        <p class="text-gray-400 text-sm mt-2">dari seluruh enrollment</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Statistik Pendaftaran (6 Bulan Terakhir)</h3>
                <canvas id="enrollmentChart" height="200"></canvas>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kursus Populer</h3>
                <div class="space-y-4">
                    @forelse ($popularCourses as $course)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($course->title, 30) }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($course->total_students) }} siswa</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: {{ round(($course->total_students / $maxStudents) * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada data kursus.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">User Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @forelse ($recentUsers as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                        {{ ucfirst($user->roles->first()->name ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const ctx = document.getElementById('enrollmentChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Pendaftaran',
                        data: @json($chartData),
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
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
