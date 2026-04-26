@extends('layouts.app')

@section('title', 'Event & Webinar')
@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Event & Webinar</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($events as $event)
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    @if ($event->image)
                        <img src="{{ Storage::url($event->image) }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <span
                            class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ ucfirst($event->type) }}</span>
                        <h3 class="text-xl font-bold mt-2">{{ $event->title }}</h3>
                        <p class="text-gray-600 text-sm mt-1">{{ $event->start_time->format('d M Y, H:i') }}</p>
                        <p class="text-gray-500 text-sm mt-2">{{ Str::limit($event->description, 100) }}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="font-bold text-primary">{{ $event->formatted_price }}</span>
                            <a href="{{ route('events.show', $event->slug) }}" class="text-primary hover:underline">Detail
                                →</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $events->links() }}
    </div>
@endsection
