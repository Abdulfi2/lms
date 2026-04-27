@extends('layouts.app')

@section('title', $event->title)
@section('page-title', $event->title)
@section('page-subtitle', $event->type)

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Event Header dengan Image -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            @if ($event->image)
                <img src="{{ Storage::url($event->image) }}" class="w-full h-64 object-cover">
            @else
                <div class="w-full h-48 bg-gradient-to-r from-primary to-secondary flex items-center justify-center">
                    <svg class="w-24 h-24 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            <div class="p-6">
                <div class="flex flex-wrap justify-between items-start gap-4">
                    <div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ $event->type == 'webinar' ? 'bg-blue-100 text-blue-800' : ($event->type == 'workshop' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($event->type) }}
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                {{ ucfirst($event->category) }}
                            </span>
                            @if ($event->is_featured)
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    ⭐ Featured
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ $event->title }}</h1>
                        <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($event->start_time)->translatedFormat('l, d F Y H:i') }} WIB
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->location ?? ($event->zoom_link ? 'Online via Zoom' : 'TBA') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-primary">{{ $event->formatted_price }}</div>
                        @if ($remainingSlots !== null)
                            <p class="text-xs text-gray-500 mt-1">Sisa kuota: {{ $remainingSlots }} peserta</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($isExpired)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-yellow-800 dark:text-yellow-400">Event ini sudah berlangsung. Tidak dapat melakukan
                        pendaftaran.</p>
                </div>
            </div>
        @endif

        @if ($isFull && !$isExpired)
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                    </svg>
                    <p class="text-red-800 dark:text-red-400">Maaf, kuota peserta sudah penuh.</p>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Event Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">📖 Tentang Event</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                <!-- Speaker Information -->
                @if ($event->speaker)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">🎤 Pembicara</h2>
                        <div class="flex items-start space-x-4">
                            @if ($event->speaker_photo)
                                <img src="{{ Storage::url($event->speaker_photo) }}"
                                    class="w-16 h-16 rounded-full object-cover">
                            @else
                                <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-white">{{ $event->speaker }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $event->speaker_bio }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Event Schedule -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">📅 Jadwal Event</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Mulai</span>
                            <span
                                class="font-medium text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($event->start_time)->translatedFormat('l, d F Y H:i') }}
                                WIB</span>
                        </div>
                        @if ($event->end_time)
                            <div class="flex justify-between items-center py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Selesai</span>
                                <span
                                    class="font-medium text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($event->end_time)->translatedFormat('l, d F Y H:i') }}
                                    WIB</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600 dark:text-gray-400">Durasi</span>
                            <span class="font-medium text-gray-800 dark:text-white">
                                @if ($event->start_time && $event->end_time)
                                    {{ \Carbon\Carbon::parse($event->start_time)->diffInHours($event->end_time) }} jam
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Zoom / Meeting Link -->
                @if ($event->zoom_link && !$isExpired)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-800 dark:text-blue-400">Akses Zoom Meeting</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">Link Zoom akan diaktifkan
                                    {{ \Carbon\Carbon::parse($event->start_time)->subMinutes(30)->translatedFormat('d F Y H:i') }}
                                    WIB</p>
                                @if ($isRegistered && $isConfirmed)
                                    <a href="{{ $event->zoom_link }}" target="_blank"
                                        class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                        Join Zoom Meeting →
                                    </a>
                                @else
                                    <p class="text-sm text-blue-600 mt-2">*Link tersedia setelah pendaftaran dikonfirmasi
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Registration Card -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 sticky top-24">
                    <div class="text-center mb-4">
                        <div class="text-3xl font-bold text-primary">{{ $event->formatted_price }}</div>
                        @if ($event->price_type === 'paid' && $event->price > 0)
                            <p class="text-xs text-gray-500 mt-1">Sudah termasuk sertifikat dan materi</p>
                        @endif
                    </div>

                    @if ($isRegistered)
                        <div class="space-y-3">
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                                <svg class="w-6 h-6 text-green-600 mx-auto mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-green-800 dark:text-green-400 font-medium">Anda sudah terdaftar!</p>
                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">Status:
                                    {{ ucfirst($isConfirmed ? 'Terkonfirmasi' : 'Menunggu Konfirmasi') }}</p>
                            </div>

                            @if (!$isExpired)
                                <form action="{{ route('student.events.cancel', $registration) }}" method="POST"
                                    onsubmit="return confirm('Batalkan pendaftaran event ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition">
                                        Batalkan Pendaftaran
                                    </button>
                                </form>
                            @endif
                        </div>
                    @elseif(!$isExpired && !$isFull)
                        <form action="{{ route('student.events.register', $event) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-3">
                                @if ($event->price_type === 'paid')
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Upload Bukti Pembayaran</label>
                                        <input type="file" name="payment_proof" accept="image/*" required
                                            class="w-full text-sm border rounded-lg p-2">
                                        <p class="text-xs text-gray-500 mt-1">Transfer ke BCA 1234567890 a.n. LMS Indonesia
                                        </p>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium mb-1">No. WhatsApp (Opsional)</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Catatan (Opsional)</label>
                                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg"
                                        placeholder="Pertanyaan yang ingin ditanyakan..."></textarea>
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full mt-4 px-4 py-3 bg-primary text-white rounded-lg hover:bg-secondary transition font-semibold">
                                Daftar Sekarang
                            </button>
                        </form>
                    @elseif($isFull)
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 text-center">
                            <p class="text-gray-600 dark:text-gray-400">Maaf, kuota sudah penuh 😔</p>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t dark:border-gray-700 text-xs text-gray-500 text-center">
                        <p>📧 Pendaftar akan mendapatkan email konfirmasi</p>
                        <p class="mt-1">🕒 Link Zoom akan dikirim H-1 event</p>
                    </div>
                </div>

                <!-- Related Events -->
                @if ($relatedEvents->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Event Lain yang Mungkin Anda Suka</h3>
                        <div class="space-y-3">
                            @foreach ($relatedEvents as $related)
                                <a href="{{ route('student.events.show', $related) }}"
                                    class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg p-2 transition">
                                    <div class="font-medium text-gray-800 dark:text-white">{{ $related->title }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($related->start_time)->format('d M Y') }}</div>
                                    <div class="text-primary text-sm font-semibold">{{ $related->formatted_price }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
