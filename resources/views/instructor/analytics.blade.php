@extends('layouts.app')

@section('title', 'Analitik Pengajar')
@section('page-title', 'Analitik & Performa')
@section('page-subtitle', 'Pantau pertumbuhan kursus dan keterlibatan siswa Anda secara real-time.')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Siswa</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalEnrollments) }}</h3>
                    <p class="text-xs text-green-500 mt-1 font-medium">+{{ $enrollmentsThisMonth }} bulan ini</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Rating</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($avgCourseRating, 1) }}</h3>
                    <div class="flex text-yellow-400 mt-1">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-3 h-3 {{ $i <= $avgCourseRating ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        @endfor
                    </div>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Selesai Materi</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalCompletedLessons) }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Interaksi materi</p>
                </div>
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Quiz</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($avgQuizScore, 1) }}%</h3>
                    <p class="text-xs text-blue-500 mt-1 font-medium">{{ $totalSubmissions }} Tugas terkumpul</p>
                </div>
                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Column -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white">Tren Pendaftaran (30 Hari Terakhir)</h3>
                <span class="text-xs text-gray-400">Update harian</span>
            </div>
            <div class="h-80 w-full">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>

        <!-- Course Performance List -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white mb-6">Performa per Kursus</h3>
            <div class="space-y-4">
                @foreach($coursePerformance as $course)
                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="flex items-center min-w-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-bold shrink-0">
                            {{ substr($course->title, 0, 1) }}
                        </div>
                        <div class="ml-3 truncate">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $course->title }}</p>
                            <p class="text-[10px] text-gray-400">{{ $course->enrollments_count }} Siswa terdaftar</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ number_format($course->reviews_avg_rating ?? 0, 1) }} ⭐</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('enrollmentChart').getContext('2d');
    const enrollmentData = @json($enrollmentTrend);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: enrollmentData.map(item => item.date),
            datasets: [{
                label: 'Pendaftaran Baru',
                data: enrollmentData.map(item => item.count),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#3B82F6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
@endsection
