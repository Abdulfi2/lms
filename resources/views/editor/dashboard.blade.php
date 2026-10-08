@extends('layouts.app')

@section('title', 'Dashboard Editor')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Kelola antrian artikel, lakukan review, dan pastikan konten terbaik untuk ' . config('app.name', 'ZS Academy') . '.')

@section('content')
<div class="space-y-6" x-data="editorDashboard()" x-init="initCharts()">
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
            ['label' => 'Artikel Menunggu Review', 'value' => $stats['pending'], 'growth' => $growth['pending'], 'period' => 'dari minggu lalu'],
            ['label' => 'Perlu Revisi', 'value' => $stats['revision'], 'growth' => $growth['revision'], 'period' => 'dari minggu lalu'],
            ['label' => 'Terbit Hari Ini', 'value' => $stats['published_today'], 'growth' => $growth['published_today'], 'period' => 'dari kemarin'],
            ['label' => 'Penulis Aktif', 'value' => $stats['active_writers'], 'growth' => $growth['active_writers'], 'period' => 'bulan ini'],
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
                    <p class="text-xs text-gray-400 mt-0.5">{{ $card['period'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Kolom Utama -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Antrian Review Artikel</h3>
                    <form method="GET" class="flex gap-2">
                        <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revisi</option>
                            <option value="ready_to_publish" {{ request('status') == 'ready_to_publish' ? 'selected' : '' }}>Siap Terbit</option>
                        </select>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau penulis..."
                            class="px-3 py-1.5 rounded-lg border dark:border-gray-600 dark:bg-gray-700 text-sm">
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Penulis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tanggal Kirim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($queueArticles as $article)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ Str::limit($article->title, 30) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                            <img src="{{ $article->author->avatar_url }}" class="w-6 h-6 rounded-full object-cover">
                                            {{ $article->author->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->category->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $article->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4"><x-article-status-badge :status="$article->status" /></td>
                                    <td class="px-6 py-4 text-right">
                                        @if ($article->status === 'draft')
                                            <a href="{{ route('editor.articles.show', $article) }}" class="px-3 py-1.5 bg-primary text-white text-xs rounded-lg hover:bg-secondary transition">Review Sekarang</a>
                                        @elseif ($article->status === 'ready_to_publish')
                                            <form method="POST" action="{{ route('editor.articles.publish', $article) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 bg-primary text-white text-xs rounded-lg hover:bg-secondary transition">Terbitkan</button>
                                            </form>
                                        @else
                                            <a href="{{ route('editor.articles.show', $article) }}" class="px-3 py-1.5 border dark:border-gray-600 text-gray-600 dark:text-gray-300 text-xs rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Lihat</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        Tidak ada artikel dalam antrian. 🎉
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($queueArticles->hasPages())
                    <div class="px-6 py-4 border-t dark:border-gray-700">
                        {{ $queueArticles->links() }}
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-white">Statistik Editorial</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400 px-3 py-1.5 border dark:border-gray-600 rounded-lg">30 Hari Terakhir</span>
                    </div>
                    @if (collect($chartReviewed)->sum() === 0)
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-10 text-center">Belum ada aktivitas review dalam 30 hari terakhir.</p>
                    @else
                        <canvas id="editorialChart" height="100"></canvas>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Kategori Populer</h3>
                        <a href="{{ route('editor.categories.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                    </div>
                    <ul class="space-y-3">
                        @forelse ($topCategories->take(5) as $i => $cat)
                            <li>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $i + 1 }}. {{ $cat['name'] }}</span>
                                    <span class="text-gray-400 text-xs">{{ $cat['count'] }} artikel</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full" style="width: {{ $cat['percent'] }}%; background-color: {{ ['#769826','#3B82F6','#8B5CF6','#F59E0B','#9CA3AF'][$i % 5] }};"></div>
                                </div>
                            </li>
                        @empty
                            <p class="text-sm text-gray-400">Belum ada artikel.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
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
                <x-editor-calendar-grid :days="$calendar['days']" :current="$calendar['current']" :start-offset="$calendar['startOffset']" :compact="true" />
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Aktivitas Review Terbaru</h3>
                </div>
                <div class="divide-y dark:divide-gray-700">
                    @forelse ($recentActivity as $activity)
                        <div class="flex items-start gap-3 px-5 py-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $activity['icon'] === 'publish' ? 'bg-green-100 text-green-600' : ($activity['icon'] === 'request_revision' ? 'bg-red-100 text-red-500' : ($activity['icon'] === 'mark_ready' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500')) }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
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
    function editorDashboard() {
        return {
            initCharts() {
                const canvas = document.getElementById('editorialChart');
                if (!canvas) return;

                new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Artikel Direview',
                                data: @json($chartReviewed),
                                borderColor: '#769826',
                                backgroundColor: 'rgba(118, 152, 38, 0.1)',
                                tension: 0.3,
                                fill: true,
                            },
                            {
                                label: 'Artikel Terbit',
                                data: @json($chartPublished),
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
            },
        };
    }
</script>
@endpush
