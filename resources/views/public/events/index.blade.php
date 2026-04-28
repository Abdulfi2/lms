@extends('layouts.client')

@section('title', 'Event & Webinar')
@section('page-title', 'Event & Webinar')
@section('page-subtitle', 'Ikuti event menarik untuk meningkatkan skill Anda')

@section('content')
    <div class="space-y-8">
        <!-- Hero Section dalam content -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-8 text-white text-center">
            <h2 class="text-3xl font-bold mb-2">Temukan Event Menarik</h2>
            <p class="text-white/80">Webinar, workshop, seminar, dan berbagai event seru lainnya</p>
        </div>

        <!-- Filter -->
        <div x-data="{ showFilter: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <button @click="showFilter = !showFilter"
                class="flex items-center gap-2 text-gray-700 dark:text-gray-300 md:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Filter Event</span>
            </button>

            <div x-show="showFilter" x-collapse class="md:!block">
                <form method="GET" action="{{ route('events.index') }}"
                    class="flex flex-wrap gap-3 items-end mt-4 md:mt-0">
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium mb-1">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari event..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipe</label>
                        <select name="type" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="">Semua</option>
                            <option value="webinar" {{ request('type') == 'webinar' ? 'selected' : '' }}>Webinar</option>
                            <option value="workshop" {{ request('type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="parenting" {{ request('type') == 'parenting' ? 'selected' : '' }}>Parenting
                            </option>
                            <option value="live_class" {{ request('type') == 'live_class' ? 'selected' : '' }}>Live Class
                            </option>
                            <option value="seminar" {{ request('type') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kategori</label>
                        <select name="category" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="">Semua</option>
                            <option value="education" {{ request('category') == 'education' ? 'selected' : '' }}>Education
                            </option>
                            <option value="technology" {{ request('category') == 'technology' ? 'selected' : '' }}>
                                Technology</option>
                            <option value="business" {{ request('category') == 'business' ? 'selected' : '' }}>Business
                            </option>
                            <option value="health" {{ request('category') == 'health' ? 'selected' : '' }}>Health</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga</label>
                        <select name="price_type" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="">Semua</option>
                            <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>Gratis</option>
                            <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>Berbayar
                            </option>
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Filter</button>
                        <a href="{{ route('events.index') }}" class="px-4 py-2 border rounded-lg ml-2">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
                    @if ($event->image)
                        <img src="{{ Storage::url($event->image) }}"
                            class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div
                            class="w-full h-48 bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ $event->type == 'webinar' ? 'bg-blue-100 text-blue-800' : ($event->type == 'workshop' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($event->type) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ \Carbon\Carbon::parse($event->start_time)->format('d M Y') }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-white text-lg mt-2 line-clamp-1">
                            {{ $event->title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                            {{ Str::limit($event->description, 100) }}</p>
                        <div class="mt-3 flex justify-between items-center">
                            <div>
                                @if ($event->price_type === 'free')
                                    <span class="text-green-600 font-bold">Gratis</span>
                                @else
                                    <span class="text-primary font-bold">Rp
                                        {{ number_format($event->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $isRegistered =
                                        auth()->check() &&
                                        \App\Models\EventRegistration::where('event_id', $event->id)
                                            ->where('user_id', auth()->id())
                                            ->exists();
                                @endphp
                                @if ($isRegistered)
                                    <span class="text-xs text-green-600">✓ Terdaftar</span>
                                @endif
                                <a href="{{ route('events.show', $event->slug) }}"
                                    class="text-primary hover:underline text-sm">Detail →</a>
                            </div>
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
                    <p class="text-sm text-gray-400 mt-1">Silakan cek kembali nanti untuk event terbaru.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    </div>
@endsection
