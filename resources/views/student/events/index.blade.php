@extends('layouts.app')

@section('title', 'Event & Webinar')
@section('page-title', 'Event & Webinar')
@section('page-subtitle', 'Ikuti event menarik untuk meningkatkan skill Anda')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Event Mendatang</p>
                <p class="text-2xl font-bold">{{ $upcomingCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Event Yang Saya Ikuti</p>
                <p class="text-2xl font-bold">{{ $myEventsCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <a href="{{ route('student.events.my') }}" class="text-primary hover:underline text-sm block mt-2">
                    Lihat Event Saya →
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('student.events.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari event..."
                        class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipe</label>
                    <select name="type" class="rounded-lg border-gray-300">
                        <option value="">Semua</option>
                        @foreach ($eventTypes as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Kategori</label>
                    <select name="category" class="rounded-lg border-gray-300">
                        <option value="">Semua</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <select name="price_type" class="rounded-lg border-gray-300">
                        <option value="">Semua</option>
                        <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>Gratis</option>
                        <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>Berbayar</option>
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Filter</button>
                    <a href="{{ route('student.events.index') }}" class="px-4 py-2 border rounded-lg ml-2">Reset</a>
                </div>
            </form>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    @if ($event->image)
                        <img src="{{ Storage::url($event->image) }}" class="w-full h-40 object-cover">
                    @else
                        <div
                            class="w-full h-40 bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ $event->type == 'webinar' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($event->type) }}
                            </span>
                            @if (in_array($event->id, $registeredEventIds))
                                <span class="text-xs text-green-600">✓ Terdaftar</span>
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-white mt-2">{{ $event->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($event->start_time)->format('d M Y, H:i') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2">
                            {{ Str::limit($event->description, 100) }}</p>
                        <div class="mt-3 flex justify-between items-center">
                            <span class="font-bold text-primary">{{ $event->formatted_price }}</span>
                            <a href="{{ route('student.events.show', $event) }}"
                                class="text-primary hover:underline text-sm">Detail →</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500">Tidak ada event yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>

        {{ $events->links() }}
    </div>
@endsection
