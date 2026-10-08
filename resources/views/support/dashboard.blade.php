@extends('layouts.app')

@section('title', 'Dashboard Support')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Kelola tiket, bantu pengguna, dan pantau kualitas layanan sistem ' . config('app.name', 'ZS Academy'))

@section('content')
<div class="space-y-6" x-data="supportDashboard()" x-init="initCharts()">
    <x-notification-card />

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">👋 Selamat datang kembali, {{ Auth::user()->name }}!</h2>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ now()->translatedFormat('l, j F Y') }}
        </div>
    </div>

    @php
        $statCards = [
            ['label' => 'Total Tiket', 'value' => $stats['total'], 'growth' => $growth['total'], 'period' => 'dari bulan lalu'],
            ['label' => 'Tiket Terbuka', 'value' => $stats['open'], 'growth' => $growth['open'], 'period' => 'dari bulan lalu'],
            ['label' => 'Selesai Hari Ini', 'value' => $stats['resolved_today'], 'growth' => $growth['resolved_today'], 'period' => 'dari kemarin'],
            ['label' => 'Prioritas Tinggi', 'value' => $stats['high_priority'], 'growth' => $growth['high_priority'], 'period' => 'dari minggu lalu'],
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($statCards as $card)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                    <div class="flex items-end justify-between mt-1">
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $card['value'] }}</p>
                        @if (!is_null($card['growth']))
                            <span class="flex items-center gap-0.5 text-xs font-medium {{ $card['growth'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if ($card['growth'] >= 0)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    @endif
                                </svg>
                                {{ abs($card['growth']) }}%
                            </span>
                        @endif
                    </div>
                    @if (!is_null($card['growth']))
                        <p class="text-xs text-gray-400 mt-0.5">{{ $card['period'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="bg-gradient-to-br from-primary to-green-700 rounded-xl shadow-sm p-6 text-white lg:row-span-1">
            <h3 class="font-bold text-lg mb-1">Bantu Pengguna Lebih Cepat & Mudah</h3>
            <p class="text-sm text-white/90 mb-4">Tanggapi tiket, selesaikan masalah, dan berikan pengalaman terbaik untuk seluruh pengguna {{ config('app.name', 'ZS Academy') }}.</p>
            <a href="{{ route('support.tickets.index', ['tab' => 'pending']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-primary rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Lihat Antrian Tiket
            </a>
        </div>

        <!-- Kolom Utama -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Performa Layanan & Tiket</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400 px-3 py-1.5 border dark:border-gray-600 rounded-lg">30 Hari Terakhir</span>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Tiket Baru</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $performance['new_30d'] }}
                            @if (!is_null($performanceGrowth['new_30d']))
                                <span class="text-xs font-medium {{ $performanceGrowth['new_30d'] >= 0 ? 'text-green-600' : 'text-red-500' }}">{{ $performanceGrowth['new_30d'] >= 0 ? '↑' : '↓' }}{{ abs($performanceGrowth['new_30d']) }}%</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Tiket Selesai</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $performance['resolved_30d'] }}
                            @if (!is_null($performanceGrowth['resolved_30d']))
                                <span class="text-xs font-medium {{ $performanceGrowth['resolved_30d'] >= 0 ? 'text-green-600' : 'text-red-500' }}">{{ $performanceGrowth['resolved_30d'] >= 0 ? '↑' : '↓' }}{{ abs($performanceGrowth['resolved_30d']) }}%</span>
                            @endif
                        </p>
                    </div>
                </div>
                @if (collect($chartNew)->sum() === 0 && collect($chartResolved)->sum() === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400 py-10 text-center">Belum ada data tiket dalam 30 hari terakhir.</p>
                @else
                    <canvas id="ticketChart" height="100"></canvas>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Daftar Tiket Terbaru</h3>
                    <a href="{{ route('support.tickets.index', ['tab' => 'all']) }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Prioritas</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentTickets as $ticket)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ Str::limit($ticket->title, 28) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $ticket->user->name }}</td>
                                    <td class="px-6 py-4"><x-ticket-status-badge :status="$ticket->status" /></td>
                                    <td class="px-6 py-4"><x-ticket-priority-badge :priority="$ticket->priority" /></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('support.tickets.show', $ticket) }}" class="text-primary hover:underline text-sm">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">Belum ada tiket.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Kategori Tiket Terpopuler</h3>
                        <a href="{{ route('support.reports.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                    </div>
                    @if ($topCategories->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-6">Belum ada tiket.</p>
                    @else
                        <div class="flex items-center gap-4">
                            <div class="relative w-24 h-24 flex-shrink-0">
                                <canvas id="categoryChart"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</span>
                                    <span class="text-[10px] text-gray-400">Total Tiket</span>
                                </div>
                            </div>
                            <ul class="text-xs space-y-1.5 flex-1 min-w-0">
                                @foreach ($topCategories->take(5) as $i => $cat)
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-1.5 min-w-0">
                                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ ['#769826','#3B82F6','#8B5CF6','#F59E0B','#9CA3AF'][$i % 5] }}"></span>
                                            <span class="truncate text-gray-600 dark:text-gray-300">{{ $cat['name'] }}</span>
                                        </span>
                                        <span class="text-gray-400 flex-shrink-0">{{ $cat['count'] }} ({{ $cat['percent'] }}%)</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Aktivitas Terbaru</h3>
                    </div>
                    <div class="divide-y dark:divide-gray-700">
                        @forelse ($recentLogs as $log)
                            <div class="px-5 py-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log['text'] }}</p>
                                <p class="text-xs text-gray-400">{{ $log['time']->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="px-5 py-6 text-center text-sm text-gray-400">Belum ada aktivitas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Prioritas Hari Ini</h3>
                </div>
                <div class="divide-y dark:divide-gray-700">
                    @forelse ($priorities as $priority)
                        <a href="{{ $priority['url'] }}" class="flex items-start justify-between gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $priority['label'] }}</p>
                                <p class="text-xs text-gray-400">{{ $priority['detail'] }}</p>
                            </div>
                            <span class="px-2 py-0.5 text-xs rounded-full flex-shrink-0 {{ $priority['level'] === 'Tinggi' ? 'bg-red-100 text-red-700' : ($priority['level'] === 'Sedang' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $priority['level'] }}
                            </span>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada prioritas mendesak. 🎉</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Jadwal Support</h3>
                    <a href="{{ route('support.schedule.index') }}" class="text-xs text-primary hover:underline">Lihat Kalender</a>
                </div>
                <x-support-calendar-grid :days="$calendar['days']" :current="$calendar['current']" :start-offset="$calendar['startOffset']" :compact="true" />
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function supportDashboard() {
        return {
            initCharts() {
                const ticketCanvas = document.getElementById('ticketChart');
                if (ticketCanvas) {
                    new Chart(ticketCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [
                                {
                                    label: 'Tiket Baru',
                                    data: @json($chartNew),
                                    borderColor: '#769826',
                                    backgroundColor: 'rgba(118, 152, 38, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                },
                                {
                                    label: 'Tiket Selesai',
                                    data: @json($chartResolved),
                                    borderColor: '#F59E0B',
                                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                },
                                {
                                    label: 'Tiket Terbuka',
                                    data: @json($chartOpen),
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: { legend: { position: 'bottom' } },
                        },
                    });
                }

                const categoryCanvas = document.getElementById('categoryChart');
                if (categoryCanvas) {
                    new Chart(categoryCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: @json($topCategories->pluck('name')),
                            datasets: [{
                                data: @json($topCategories->pluck('count')),
                                backgroundColor: ['#769826', '#3B82F6', '#8B5CF6', '#F59E0B', '#9CA3AF'],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            cutout: '70%',
                            plugins: { legend: { display: false } },
                        },
                    });
                }
            },
        };
    }
</script>
@endpush
