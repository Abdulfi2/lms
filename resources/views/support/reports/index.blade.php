@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')
@section('page-subtitle', 'Ringkasan performa tiket support')

@section('content')
<div class="space-y-6" x-data="reportCharts()" x-init="initChart()">
    <form method="GET" class="flex justify-end">
        <select name="range" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="3" {{ $range == 3 ? 'selected' : '' }}>3 Bulan Terakhir</option>
            <option value="6" {{ $range == 6 ? 'selected' : '' }}>6 Bulan Terakhir</option>
            <option value="12" {{ $range == 12 ? 'selected' : '' }}>12 Bulan Terakhir</option>
        </select>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Tren Tiket Baru vs Selesai</h3>
        <canvas id="trendChart" height="90"></canvas>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Berdasarkan Status</h3>
            <ul class="space-y-2 text-sm">
                @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'menunggu_user' => 'Menunggu User', 'selesai' => 'Selesai'] as $key => $label)
                    <li class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $byStatus[$key] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Berdasarkan Prioritas</h3>
            <ul class="space-y-2 text-sm">
                @foreach (['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $key => $label)
                    <li class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $byPriority[$key] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Berdasarkan Kategori</h3>
            <ul class="space-y-2 text-sm">
                @forelse ($byCategory as $category)
                    <li class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ $category['name'] }}</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $category['total'] }}</span>
                    </li>
                @empty
                    <li class="text-gray-400">Belum ada data.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function reportCharts() {
        return {
            initChart() {
                const canvas = document.getElementById('trendChart');
                if (!canvas) return;

                new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($monthLabels),
                        datasets: [
                            {
                                label: 'Tiket Baru',
                                data: @json($monthlyNew),
                                backgroundColor: '#769826',
                            },
                            {
                                label: 'Tiket Selesai',
                                data: @json($monthlyResolved),
                                backgroundColor: '#F59E0B',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            },
        };
    }
</script>
@endpush
