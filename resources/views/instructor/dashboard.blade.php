@extends('layouts.app')

@section('title', 'Dashboard Instructor')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('content')
    <div x-data="instructorDashboard()" x-init="initChart()" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Kursus</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalCourses }}</p>
                        <div class="flex text-xs mt-1">
                            <span class="text-green-600">Published: {{ $publishedCourses }}</span>
                            <span class="text-yellow-600 ml-3">Draft: {{ $draftCourses }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalStudents }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">Rp
                            {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Rating Rata-rata</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $avgRating ?? '0' }} / 5</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tugas Menunggu Dinilai -->
        @if ($pendingGradingCount > 0)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-red-800 dark:text-red-400">{{ $pendingGradingCount }} tugas menunggu dinilai</h3>
                            <p class="text-sm text-red-600 dark:text-red-500">Siswa menunggu feedback dan nilai dari Anda.</p>
                        </div>
                    </div>
                    <a href="{{ route('instructor.submissions.index') }}"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold whitespace-nowrap">
                        Nilai Sekarang
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg divide-y dark:divide-gray-700">
                    @foreach ($pendingSubmissions as $submission)
                        <div class="p-3 flex items-center justify-between text-sm">
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $submission->student->name ?? '-' }}</span>
                                <span class="text-gray-400 mx-1">&middot;</span>
                                <span class="text-gray-500">{{ $submission->assignment->title ?? '-' }}</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ optional($submission->submitted_at)->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Chart Pendapatan -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pendapatan 6 Bulan Terakhir</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <!-- Dua kolom: Kursus Terbaru & Terlaris -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Kursus Terbaru</h3>
                </div>
                <div class="divide-y dark:divide-gray-700">
                    @forelse($recentCourses as $course)
                        <div class="px-6 py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">{{ $course->created_at->diffForHumans() }}</p>
                            </div>
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ $course->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">Belum ada kursus.</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Courses by Students -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Kursus Terpopuler</h3>
                </div>
                <div class="divide-y dark:divide-gray-700">
                    @forelse($topCourses as $course)
                        <div class="px-6 py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">{{ $course->total_students }} siswa</p>
                            </div>
                            <div class="flex items-center">
                                <span class="text-yellow-500 mr-1">★</span>
                                <span class="text-sm">{{ number_format($course->average_rating, 1) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">Belum ada data.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white">Pendaftaran Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentEnrollments as $enrollment)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $enrollment->user->name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $enrollment->course->title }}</td>
                                <td class="px-6 py-4 text-sm">{{ $enrollment->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-primary h-2 rounded-full"
                                            style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pendaftaran.</td>
                            </tr>
                        @endforelse
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
                    initChart() {
                        const ctx = document.getElementById('revenueChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @json($chartLabels),
                                datasets: [{
                                    label: 'Pendapatan (Rp)',
                                    data: @json($chartData),
                                    borderColor: '#769826',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
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
                    }
                }
            }
        </script>
    @endpush
@endsection
