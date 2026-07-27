@extends('layouts.app')

@section('title', 'Analytics')
@section('page-title', 'Analytics')
@section('page-subtitle', 'Tren dan performa platform 12 bulan terakhir')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pendapatan</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pendaftaran</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalEnrollments) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Rata-rata Nilai Transaksi</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Completion Rate</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $overallCompletionRate }}%</p>
        </div>
    </div>

    <!-- Trend Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tren Pendapatan (12 Bulan)</h3>
            <canvas id="revenueChart" height="220"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Tren Pendaftaran (12 Bulan)</h3>
            <canvas id="enrollmentTrendChart" height="220"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pertumbuhan User Baru (12 Bulan)</h3>
            <canvas id="userTrendChart" height="220"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Distribusi Role User</h3>
            <canvas id="roleChart" height="220"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kursus Terlaris (Pendapatan)</h3>
            <div class="space-y-3">
                @forelse ($topCoursesByRevenue as $course)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-300">{{ Str::limit($course->title, 35) }}</span>
                        <span class="font-semibold text-gray-800 dark:text-white">Rp {{ number_format($course->revenue, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data.</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Kursus Terpopuler (Siswa)</h3>
            <div class="space-y-3">
                @forelse ($topCoursesByEnrollment as $course)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-300">{{ Str::limit($course->title, 35) }}</span>
                        <span class="font-semibold text-gray-800 dark:text-white">{{ number_format($course->total_students) }} siswa</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Distribusi Status Kursus</h3>
            <canvas id="courseStatusChart" height="220"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Distribusi Status Pembayaran</h3>
            <canvas id="paymentStatusChart" height="220"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const monthLabels = @json($monthLabels);

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Pendapatan',
                data: @json($revenueTrend),
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('enrollmentTrendChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Pendaftaran',
                data: @json($enrollmentTrend),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('userTrendChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'User Baru',
                data: @json($userTrend),
                backgroundColor: '#10B981',
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('roleChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($roleDistribution->toArray())),
            datasets: [{
                data: @json(array_values($roleDistribution->toArray())),
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444']
            }]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('courseStatusChart'), {
        type: 'pie',
        data: {
            labels: @json(array_keys($courseStatusDistribution->toArray())),
            datasets: [{
                data: @json(array_values($courseStatusDistribution->toArray())),
                backgroundColor: ['#10B981', '#9CA3AF', '#F59E0B', '#6366F1']
            }]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('paymentStatusChart'), {
        type: 'pie',
        data: {
            labels: @json(array_keys($paymentStatusDistribution->toArray())),
            datasets: [{
                data: @json(array_values($paymentStatusDistribution->toArray())),
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#9CA3AF']
            }]
        },
        options: { responsive: true }
    });
</script>
@endpush
@endsection
