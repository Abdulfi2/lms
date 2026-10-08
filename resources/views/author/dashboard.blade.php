@extends('layouts.app')

@section('title', 'Dashboard Author')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Kelola artikel, pantau performa, dan bagikan ilmu yang bermanfaat.')

@section('content')
<div class="space-y-6" x-data="authorDashboard()" x-init="initCharts()">
    <x-notification-card />

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">👋 Selamat datang kembali, {{ Auth::user()->name }}!</h2>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ now()->translatedFormat('l, j F Y') }}
        </div>
    </div>

    @php
        $statCards = [
            ['label' => 'Total Artikel', 'value' => number_format($stats['total']), 'growth' => $growth['total'], 'color' => 'green'],
            ['label' => 'Total Dilihat', 'value' => number_format($stats['views']), 'growth' => $growth['views'], 'color' => 'orange'],
            ['label' => 'Total Komentar', 'value' => number_format($stats['comments']), 'growth' => $growth['comments'], 'color' => 'blue'],
            ['label' => 'Total Suka', 'value' => number_format($stats['likes']), 'growth' => $growth['likes'], 'color' => 'red'],
        ];
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <p class="text-xs text-gray-400 mt-0.5">dari bulan lalu</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Kolom Utama -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Statistik Artikel</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400 px-3 py-1.5 border dark:border-gray-600 rounded-lg">30 Hari Terakhir</span>
                </div>
                @if (collect($chartViews)->sum() === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400 py-10 text-center">Belum ada data kunjungan dalam 30 hari terakhir.</p>
                @else
                    <canvas id="visitsChart" height="90"></canvas>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Artikel Terbaru Saya</h3>
                    <a href="{{ route('author.articles.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Dilihat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Komentar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tanggal</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentArticles as $article)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ Str::limit($article->title, 35) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->category->name ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <x-article-status-badge :status="$article->status" />
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ number_format($article->views) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->comments_count }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('author.articles.edit', $article) }}" class="text-primary hover:underline text-sm">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        Anda belum menulis artikel apa pun.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gradient-to-br from-primary to-green-700 rounded-xl shadow-sm p-6 text-white">
                <h3 class="font-bold text-lg mb-1">Terus Bagikan Ilmu</h3>
                <p class="text-sm text-white/90 mb-4">Setiap tulisan Anda dapat memberikan manfaat bagi lebih banyak orang.</p>
                <a href="{{ route('author.articles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-primary rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Tulis Artikel Baru
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Draft Terakhir</h3>
                    <a href="{{ route('author.articles.index', ['status' => 'draft']) }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="divide-y dark:divide-gray-700">
                    @forelse ($draftArticles as $draft)
                        <a href="{{ route('author.articles.edit', $draft) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ Str::limit($draft->title, 32) }}</p>
                                <p class="text-xs text-gray-400">Terakhir disimpan {{ $draft->updated_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 flex-shrink-0">Draft</span>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada draft.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white text-sm mb-3">Top Kategori Saya</h3>
                @if ($topCategories->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-6">Belum ada artikel.</p>
                @else
                    <div class="flex items-center gap-4">
                        <div class="relative w-24 h-24 flex-shrink-0">
                            <canvas id="categoryChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</span>
                                <span class="text-[10px] text-gray-400">Artikel</span>
                            </div>
                        </div>
                        <ul class="text-xs space-y-1.5 flex-1 min-w-0">
                            @foreach ($topCategories as $i => $cat)
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
                <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Aktivitas Terbaru</h3>
                    <a href="{{ route('author.comments.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="divide-y dark:divide-gray-700">
                    @forelse ($recentActivity as $activity)
                        <div class="flex items-start gap-3 px-5 py-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $activity['icon'] === 'comment' ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-500' }}">
                                @if ($activity['icon'] === 'comment')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $activity['text'] }}</p>
                                <p class="text-xs text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">Belum ada aktivitas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function authorDashboard() {
        return {
            initCharts() {
                const visitsCanvas = document.getElementById('visitsChart');
                if (visitsCanvas) {
                    new Chart(visitsCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [
                                {
                                    label: 'Pengunjung',
                                    data: @json($chartVisitors),
                                    borderColor: '#769826',
                                    backgroundColor: 'rgba(118, 152, 38, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                },
                                {
                                    label: 'Tayangan',
                                    data: @json($chartViews),
                                    borderColor: '#F59E0B',
                                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
