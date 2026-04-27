@extends('layouts.app')

@section('title', 'Event Saya')
@section('page-title', 'Event Saya')
@section('page-subtitle', 'Event yang telah Anda daftar')

@section('content')
    <div x-data="{ activeTab: 'upcoming' }" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Event Diikuti</p>
                <p class="text-2xl font-bold">{{ $registrations->total() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Event Mendatang</p>
                <p class="text-2xl font-bold text-green-600">{{ $upcomingEvents->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Event Selesai</p>
                <p class="text-2xl font-bold text-gray-500">{{ $pastEvents->count() }}</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b dark:border-gray-700">
            <nav class="flex space-x-6">
                <button @click="activeTab = 'upcoming'" :class="{ 'border-primary text-primary': activeTab === 'upcoming' }"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition">
                    Event Mendatang
                </button>
                <button @click="activeTab = 'past'" :class="{ 'border-primary text-primary': activeTab === 'past' }"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition">
                    Event Selesai
                </button>
            </nav>
        </div>

        <!-- Upcoming Events Tab -->
        <div x-show="activeTab === 'upcoming'" class="space-y-4">
            @forelse($upcomingEvents as $reg)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-48 h-32 md:h-auto">
                            @if ($reg->event->image)
                                <img src="{{ Storage::url($reg->event->image) }}"
                                    class="w-full h-32 md:h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full {{ $reg->event->type == 'webinar' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($reg->event->type) }}
                                        </span>
                                        <span
                                            class="px-2 py-1 text-xs rounded-full {{ $reg->status == 'confirmed' ? 'bg-green-100 text-green-800' : ($reg->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($reg->status) }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-2">
                                        {{ $reg->event->title }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($reg->event->start_time)->format('d M Y, H:i') }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $reg->event->location ?? ($reg->event->zoom_link ? 'Online via Zoom' : 'Online') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-2 line-clamp-2">
                                        {{ Str::limit($reg->event->description, 100) }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-3">
                                @if ($reg->status == 'pending' && $reg->event->price_type == 'paid')
                                    <button onclick="uploadPayment({{ $reg->id }})"
                                        class="px-4 py-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition text-sm">
                                        Upload Bukti Bayar
                                    </button>
                                @endif
                                @if ($reg->event->zoom_link)
                                    <a href="{{ $reg->event->zoom_link }}" target="_blank"
                                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">
                                        Join Zoom
                                    </a>
                                @endif
                                <form action="{{ route('student.events.cancel', $reg) }}" method="POST"
                                    onsubmit="return confirm('Batalkan pendaftaran event ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition text-sm">
                                        Batalkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Event Mendatang</h3>
                    <p class="text-gray-500 mt-1">Anda belum mendaftar event apapun. Jelajahi event yang tersedia!</p>
                    <a href="{{ route('student.events.index') }}"
                        class="mt-4 inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        Jelajahi Event
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Past Events Tab -->
        <div x-show="activeTab === 'past'" class="space-y-4">
            @forelse($pastEvents as $reg)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-48 h-32 md:h-auto">
                            @if ($reg->event->image)
                                <img src="{{ Storage::url($reg->event->image) }}"
                                    class="w-full h-32 md:h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                            {{ ucfirst($reg->event->type) }}
                                        </span>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                            Selesai
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-2">
                                        {{ $reg->event->title }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($reg->event->start_time)->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <span class="text-sm text-gray-500">Event telah selesai pada
                                    {{ \Carbon\Carbon::parse($reg->event->end_time ?? $reg->event->start_time)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                    <p class="text-gray-500">Tidak ada event yang telah selesai.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $registrations->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            function uploadPayment(registrationId) {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.onchange = () => {
                    const file = input.files[0];
                    if (file) {
                        const formData = new FormData();
                        formData.append('payment_proof', file);
                        fetch(`/student/events/registrations/${registrationId}/payment`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    location.reload();
                                } else {
                                    window.toast.error(data.message);
                                }
                            });
                    }
                };
                input.click();
            }
        </script>
    @endpush
@endsection
