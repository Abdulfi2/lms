@extends('layouts.app')

@section('title', $event->title)
@section('page-title', $event->title)
@section('page-subtitle', 'Detail event')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.events.index') }}" class="text-primary hover:underline inline-flex items-center text-sm">
            &larr; Kembali ke Daftar Event
        </a>
        <div class="flex space-x-2">
            <form action="{{ route('admin.events.duplicate', $event) }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 border rounded-lg text-sm dark:border-gray-600">Duplikasi</button>
            </form>
            <a href="{{ route('admin.events.edit', $event) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Edit</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        @if ($event->image)
            <img src="{{ Storage::url($event->image) }}" class="w-full h-56 object-cover">
        @endif
        <div class="p-6">
            <div class="flex items-center gap-2 mb-2">
                <x-status-badge :status="$event->status" />
                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">{{ ucfirst(str_replace('_', ' ', $event->type)) }}</span>
                @if ($event->is_featured)
                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Unggulan</span>
                @endif
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $event->title }}</h1>
            <p class="text-gray-500 text-sm mt-1">Diselenggarakan oleh: {{ $event->organizer->name ?? '-' }}</p>

            <div class="mt-4 prose dark:prose-invert max-w-none">{!! nl2br(e($event->description)) !!}</div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6 text-sm">
                <div><strong>Jadwal:</strong><br>{{ optional($event->start_time)->format('d F Y, H:i') ?? '-' }}</div>
                <div><strong>Lokasi:</strong><br>{{ $event->location ?? 'Online' }}</div>
                <div><strong>Harga:</strong><br>{{ $event->formatted_price }}</div>
                @if ($event->speaker)
                    <div><strong>Pembicara:</strong><br>{{ $event->speaker }}</div>
                @endif
                @if ($event->zoom_meeting_url)
                    <div><strong>Link Zoom:</strong><br><a href="{{ $event->zoom_meeting_url }}" class="text-primary hover:underline" target="_blank">Buka Zoom</a></div>
                @endif
                <div><strong>Kuota:</strong><br>{{ $event->max_participants ? $event->total_registrations . '/' . $event->max_participants : 'Tidak terbatas' }}</div>
            </div>
        </div>
    </div>

    <!-- Statistik Registrasi -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Total Pendaftar</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $statistics['total_registrations'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Terkonfirmasi</p>
            <p class="text-xl font-bold text-green-600">{{ $statistics['confirmed'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Hadir</p>
            <p class="text-xl font-bold text-blue-600">{{ $statistics['attended'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Pendapatan</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($statistics['revenue'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('admin.events.registrations', $event) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary text-sm">
            Lihat & Kelola Semua Pendaftar
        </a>
    </div>

    <!-- Pendaftar Terbaru -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 font-semibold">Pendaftar Terbaru</div>
        <div class="divide-y dark:divide-gray-700">
            @forelse ($registrations->take(5) as $reg)
                <div class="px-6 py-3 flex items-center justify-between text-sm">
                    <div>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $reg->name }}</span>
                        <span class="text-gray-400 mx-1">&middot;</span>
                        <span class="text-gray-500">{{ $reg->email }}</span>
                    </div>
                    <x-status-badge :status="$reg->status" />
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">Belum ada pendaftar.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
