@extends('layouts.client')

@section('title', $event->title)
@section('page-title', $event->title)
@section('page-subtitle', $event->type)

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            @if ($event->image)
                <img src="{{ Storage::url($event->image) }}" class="w-full h-64 object-cover">
            @endif

            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <span
                            class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">{{ ucfirst($event->type) }}</span>
                        <span
                            class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-sm ml-2">{{ ucfirst($event->category) }}</span>
                    </div>
                    <span class="text-2xl font-bold text-primary">{{ $event->formatted_price }}</span>
                </div>

                <h1 class="text-3xl font-bold mt-4">{{ $event->title }}</h1>

                <div class="mt-6 space-y-3">
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $event->start_time->format('l, d F Y H:i') }} WIB
                    </div>
                    @if ($event->location)
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $event->location }}
                        </div>
                    @endif
                    @if ($event->zoom_link)
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Online via Zoom
                        </div>
                    @endif
                </div>

                <div class="mt-6 prose max-w-none">
                    <h3 class="font-semibold">Deskripsi Event</h3>
                    {!! nl2br(e($event->description)) !!}
                </div>

                @if ($event->speaker)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold">Pembicara</h3>
                        <div class="flex items-center mt-2">
                            @if ($event->speaker_photo)
                                <img src="{{ Storage::url($event->speaker_photo) }}"
                                    class="w-12 h-12 rounded-full object-cover mr-3">
                            @endif
                            <div>
                                <p class="font-medium">{{ $event->speaker }}</p>
                                <p class="text-sm text-gray-500">{{ $event->speaker_bio }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($event->remaining_slots !== null)
                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                        Kuota tersisa: {{ $event->remaining_slots }} peserta
                    </div>
                @endif

                @if (!$isRegistered)
                    @auth
                        <form action="{{ route('events.register', $event->slug) }}" method="POST" class="mt-6">
                            @csrf
                            <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                            <button type="submit" class="w-full py-3 bg-primary text-white rounded-lg hover:bg-secondary">
                                Daftar Event Sekarang
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="block w-full mt-6 py-3 bg-primary text-white text-center rounded-lg hover:bg-secondary">
                            Login untuk Mendaftar
                        </a>
                    @endauth
                @else
                    <div class="mt-6 p-4 bg-green-100 rounded-lg text-center">
                        <svg class="w-6 h-6 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-semibold">Anda sudah terdaftar!</p>
                        <p class="text-sm mt-1">Status: {{ $registration->status }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
