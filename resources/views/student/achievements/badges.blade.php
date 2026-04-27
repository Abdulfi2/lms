@extends('layouts.app')

@section('title', 'Lencana Saya')
@section('page-title', 'Lencana')
@section('page-subtitle', 'Koleksi lencana yang Anda dapatkan')

@section('content')
    <div class="space-y-6">
        <div class="text-center mb-6">
            <p class="text-gray-500">Total lencana: <span
                    class="font-bold text-primary">{{ $allBadges->where('is_earned', true)->count() }}</span> /
                {{ $allBadges->count() }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($allBadges as $badge)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center {{ !$badge->is_earned ? 'opacity-50' : '' }}">
                    <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center shadow-md"
                        style="background: {{ $badge->color }}20; border: 2px solid {{ $badge->color }}">
                        <span class="text-4xl">{{ $badge->icon }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-800 dark:text-white mt-3">{{ $badge->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $badge->description }}</p>
                    @if ($badge->is_earned)
                        <p class="text-xs text-green-600 mt-2">Didapat: {{ $badge->earned_at->format('d M Y') }}</p>
                    @else
                        <p class="text-xs text-gray-400 mt-2">Belum didapat</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
