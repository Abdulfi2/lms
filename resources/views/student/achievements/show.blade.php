@extends('layouts.app')

@section('title', $achievement->name)
@section('page-title', $achievement->name)

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-amber-600 p-6 text-center text-white">
                <div class="text-6xl mb-4">{{ $achievement->icon ?? '🏆' }}</div>
                <h1 class="text-2xl font-bold">{{ $achievement->name }}</h1>
                <p class="text-white/80 mt-2">{{ $achievement->description }}</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-gray-500 text-sm">Reward Poin</p>
                        <p class="text-2xl font-bold text-primary">+{{ number_format($achievement->points_reward) }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-gray-500 text-sm">Total Penerima</p>
                        <p class="text-2xl font-bold">{{ $totalEarners }}</p>
                    </div>
                </div>

                @if (!$isEarned)
                    <div class="mt-6">
                        <h3 class="font-semibold mb-2">Progress Anda</h3>
                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                            <span>{{ $progress['current'] }}/{{ $progress['required'] }}</span>
                            <span>{{ $progress['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $progress['percentage'] }}%"></div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                        <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-green-800 dark:text-green-400 font-medium">Anda sudah mendapatkan prestasi ini!</p>
                        <p class="text-sm text-green-700 dark:text-green-300">Didapat pada
                            {{ \Carbon\Carbon::parse($userAchievement->earned_at)->format('d F Y') }}</p>
                    </div>
                @endif

                @if ($recentEarners->count() > 0)
                    <div class="mt-6">
                        <h3 class="font-semibold mb-3">Penerima Terbaru</h3>
                        <div class="space-y-2">
                            @foreach ($recentEarners as $earner)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <img src="{{ $earner->user->avatar_url }}" class="w-6 h-6 rounded-full">
                                        <span>{{ $earner->user->name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $earner->earned_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
