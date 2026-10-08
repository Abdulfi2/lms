@extends('layouts.app')

@section('title', 'Dashboard Event')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Kelola event, pendaftaran, jadwal, dan pengalaman peserta dengan lebih mudah di ZS Academy.')

@section('content')
@php
    $compact = fn ($n) => $n >= 1000 ? rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'K' : number_format($n);

    $statCards = [
        ['label' => 'Total Event', 'value' => number_format($stats['total_events']), 'growth' => $growth['total_events'], 'unit' => '%', 'color' => 'green',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Event Aktif', 'value' => number_format($stats['active_events']), 'growth' => $growth['active_events'], 'unit' => '%', 'color' => 'orange',
            'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Total Peserta', 'value' => $compact($stats['participants']), 'growth' => $growth['participants'], 'unit' => '%', 'color' => 'blue',
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Kehadiran Rata-rata', 'value' => is_null($stats['attendance']) ? '-' : $stats['attendance'] . '%', 'growth' => $growth['attendance'], 'unit' => '%', 'color' => 'red',
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ];
    $iconBg = [
        'green' => 'bg-green-100 text-primary dark:bg-green-900/30',
        'orange' => 'bg-orange-100 text-accent dark:bg-orange-900/30',
        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30',
        'red' => 'bg-red-100 text-red-500 dark:bg-red-900/30',
    ];

    $statusBadge = [
        'ongoing' => ['Berlangsung', 'bg-green-100 text-green-700'],
        'upcoming' => ['Akan Datang', 'bg-blue-100 text-blue-700'],
        'finished' => ['Selesai', 'bg-gray-100 text-gray-600'],
        'draft' => ['Draft', 'bg-yellow-100 text-yellow-800'],
        'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
    $typeBadge = [
        'webinar' => 'bg-green-100 text-green-700',
        'workshop' => 'bg-orange-100 text-orange-700',
        'seminar' => 'bg-purple-100 text-purple-700',
        'parenting' => 'bg-pink-100 text-pink-700',
        'live_class' => 'bg-blue-100 text-blue-700',
        'zoom_meeting' => 'bg-sky-100 text-sky-700',
    ];
    $dotColor = ['today' => 'bg-accent', 'upcoming' => 'bg-blue-500', 'finished' => 'bg-red-500', 'event' => 'bg-primary'];
    $chartColors = ['#769826', '#3B82F6', '#8B5CF6', '#F59E0B', '#9CA3AF', '#EC4899'];
    $firstName = Str::of(Auth::user()->name)->before(' ');
@endphp
<div class="space-y-6" x-data="eventDashboard()" x-init="initCharts()">
    <x-notification-card />

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">👋 Selamat datang kembali, {{ $firstName }}!</h2>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ now()->translatedFormat('l, j F Y') }}
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        <!-- Kolom utama -->
        <div class="xl:col-span-2 space-y-6 min-w-0">
            <!-- Kartu statistik -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($statCards as $card)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 {{ $iconBg[$card['color']] }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $card['label'] }}</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $card['value'] }}</p>
                                @if (!is_null($card['growth']))
                                    <span class="text-xs font-medium {{ $card['growth'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $card['growth'] >= 0 ? '↑' : '↓' }} {{ abs($card['growth']) }}{{ $card['unit'] }}
                                    </span>
                                @endif
                            </div>
                            @if (!is_null($card['growth']))
                                <p class="text-[11px] text-gray-400">dari bulan lalu</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Jadwal & performa -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Jadwal &amp; Performa Event</h3>
                    <form method="GET" action="{{ route('admin.events.dashboard') }}">
                        <input type="hidden" name="month" value="{{ request('month') }}">
                        <select name="range" onchange="this.form.submit()"
                            class="text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary py-1.5">
                            @foreach ([7 => '7 Hari Terakhir', 30 => '30 Hari Terakhir', 90 => '90 Hari Terakhir'] as $value => $label)
                                <option value="{{ $value }}" @selected($range === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center mb-4">
                    <div>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $compact($performance['summary']['registered']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><span class="inline-block w-2 h-2 rounded-full bg-primary mr-1"></span>Pendaftaran</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $compact($performance['summary']['attended']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><span class="inline-block w-2 h-2 rounded-full bg-accent mr-1"></span>Peserta Hadir</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $compact($performance['summary']['absent']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-1"></span>Tidak Hadir</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ is_null($performance['summary']['rate']) ? '-' : $performance['summary']['rate'] . '%' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-1"></span>Tingkat Kehadiran</p>
                    </div>
                </div>

                @if (array_sum($performance['registered']) + array_sum($performance['attended']) === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400 py-10 text-center">Belum ada pendaftaran dalam {{ $range }} hari terakhir.</p>
                @else
                    <canvas id="performanceChart" height="90"></canvas>
                @endif
            </div>

            <!-- Daftar event terbaru -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Daftar Event Terbaru</h3>
                    <a href="{{ route('admin.events.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @foreach (['Nama Event', 'Tipe', 'Tanggal', 'Lokasi', 'Status', 'Peserta'] as $th)
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">{{ $th }}</th>
                                @endforeach
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentEvents as $event)
                                @php
                                    $status = $statusBadge[$event->lifecycle_status];
                                    $isOnline = $event->zoom_link || $event->meeting_id;
                                    $percent = $event->max_participants ? min(100, round($event->participants_count / $event->max_participants * 100)) : null;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3 min-w-[220px]">
                                            @if ($event->image)
                                                <img src="{{ asset('storage/' . $event->image) }}" alt="" class="w-10 h-10 rounded-md object-cover flex-shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded-md bg-green-100 dark:bg-green-900/30 text-primary flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-800 dark:text-white truncate max-w-[200px]">{{ $event->title }}</p>
                                                <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $event->speaker ?: \App\Models\Event::CATEGORY_LABELS[$event->category] ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs rounded-md whitespace-nowrap {{ $typeBadge[$event->type] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ \App\Models\Event::TYPE_LABELS[$event->type] ?? $event->type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $event->start_time->translatedFormat('j M Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $event->start_time->format('H:i') }}{{ $event->end_time ? ' - ' . $event->end_time->format('H:i') : '' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[140px]">{{ $event->location ?: ($isOnline ? 'Online' : '-') }}</p>
                                        @if ($isOnline)
                                            <p class="text-xs text-gray-400">{{ $event->location ? 'Hybrid + Zoom' : 'Zoom Meeting' }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs rounded-md whitespace-nowrap {{ $status[1] }}">{{ $status[0] }}</span>
                                    </td>
                                    <td class="px-4 py-3 min-w-[110px]">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $event->participants_count }} / {{ $event->max_participants ?: '∞' }}</p>
                                        @if (!is_null($percent))
                                            <div class="mt-1 h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-600">
                                                <div class="h-1.5 rounded-full bg-primary" style="width: {{ $percent }}%"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-primary hover:underline">Detail</a>
                                        <a href="{{ route('admin.events.edit', $event) }}" class="ml-2 text-gray-500 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada event.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kategori + aktivitas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm mb-4">Kategori Event Terpopuler</h3>
                    @if ($categories->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-6">Belum ada event.</p>
                    @else
                        <div class="flex items-center gap-4">
                            <div class="relative w-28 h-28 flex-shrink-0">
                                <canvas id="categoryChart"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['total_events'] }}</span>
                                    <span class="text-[10px] text-gray-400">Event</span>
                                </div>
                            </div>
                            <ul class="text-xs space-y-1.5 flex-1 min-w-0">
                                @foreach ($categories as $i => $cat)
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-1.5 min-w-0">
                                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $chartColors[$i % count($chartColors)] }}"></span>
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
                        @forelse ($activities as $activity)
                            <div class="flex items-start gap-3 px-5 py-3">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $activity['icon'] === 'user' ? 'bg-green-100 text-primary' : 'bg-orange-100 text-accent' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($activity['icon'] === 'user')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        @endif
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

        <!-- Kolom kanan -->
        <div class="xl:col-span-1 space-y-6">
            <div class="relative overflow-hidden bg-gradient-to-br from-primary to-green-700 rounded-xl shadow-sm p-6 text-white">
                <svg class="absolute -right-4 -bottom-4 w-36 h-36 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div class="relative">
                    <h3 class="font-bold text-lg mb-1">Buat Event Inspiratif Bersama ZS Academy</h3>
                    <p class="text-sm text-white/90 mb-4">Bagikan ilmu, bangun komunitas, dan ciptakan dampak yang lebih luas.</p>
                    <a href="{{ route('admin.events.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-primary rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Event Baru
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Agenda Hari Ini</h3>
                    <a href="{{ route('admin.events.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="divide-y dark:divide-gray-700">
                    @forelse ($agenda as $item)
                        <a href="{{ $item['url'] }}" class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            @if ($item['done'])
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            @else
                                <span class="mt-0.5 w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-500 flex-shrink-0"></span>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $item['title'] }}</p>
                                <p class="text-xs text-gray-400">{{ $item['meta'] }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada agenda.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Kalender Event</h3>
                    <a href="{{ route('admin.events.dashboard', ['range' => $range]) }}" class="text-xs text-primary hover:underline">Bulan Ini</a>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <a href="{{ route('admin.events.dashboard', ['range' => $range, 'month' => $calendar['prev']]) }}"
                        class="p-1.5 rounded-lg border dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700" aria-label="Bulan sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $calendar['label'] }}</span>
                    <a href="{{ route('admin.events.dashboard', ['range' => $range, 'month' => $calendar['next']]) }}"
                        class="p-1.5 rounded-lg border dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700" aria-label="Bulan berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
                <div class="grid grid-cols-7 text-center text-xs">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
                        <div class="py-1.5 font-medium text-gray-500 dark:text-gray-400">{{ $dayName }}</div>
                    @endforeach
                    @foreach ($calendar['weeks'] as $week)
                        @foreach ($week as $day)
                            <div class="py-1 flex flex-col items-center" @if ($day['title']) title="{{ $day['title'] }}" @endif>
                                <span class="w-7 h-7 flex items-center justify-center rounded-full text-sm
                                    {{ $day['is_today'] ? 'bg-primary text-white font-semibold' : ($day['in_month'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600') }}">
                                    {{ $day['day'] }}
                                </span>
                                <span class="mt-0.5 w-1.5 h-1.5 rounded-full {{ $day['dot'] ? $dotColor[$day['dot']] : 'bg-transparent' }}"></span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 mt-4 text-[11px] text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span>Event</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-accent"></span>Hari Ini</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Akan Datang</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>Selesai</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function eventDashboard() {
        return {
            initCharts() {
                const performanceCanvas = document.getElementById('performanceChart');
                if (performanceCanvas) {
                    new Chart(performanceCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: @json($performance['labels']),
                            datasets: [
                                {
                                    label: 'Pendaftaran',
                                    data: @json($performance['registered']),
                                    borderColor: '#769826',
                                    backgroundColor: 'rgba(118, 152, 38, 0.12)',
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 0,
                                },
                                {
                                    label: 'Peserta Hadir',
                                    data: @json($performance['attended']),
                                    borderColor: '#EF6905',
                                    backgroundColor: 'rgba(239, 105, 5, 0.12)',
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 0,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            interaction: { mode: 'index', intersect: false },
                            plugins: { legend: { position: 'bottom' } },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                                x: { ticks: { maxTicksLimit: 8, autoSkip: true } },
                            },
                        },
                    });
                }

                const categoryCanvas = document.getElementById('categoryChart');
                if (categoryCanvas) {
                    new Chart(categoryCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: @json($categories->pluck('name')),
                            datasets: [{
                                data: @json($categories->pluck('count')),
                                backgroundColor: @json($chartColors),
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
