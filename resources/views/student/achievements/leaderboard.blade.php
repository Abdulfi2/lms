@extends('layouts.app')

@section('title', 'Papan Peringkat')
@section('page-title', 'Papan Peringkat')
@section('page-subtitle', 'Siapa yang paling aktif belajar?')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Period Tabs -->
        <div class="flex gap-2">
            <a href="{{ route('student.leaderboard') }}"
                class="px-4 py-2 rounded-lg text-sm font-medium {{ $period === 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border dark:border-gray-700' }}">
                Sepanjang Waktu
            </a>
            <a href="{{ route('student.leaderboard', ['period' => 'weekly']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium {{ $period === 'weekly' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border dark:border-gray-700' }}">
                Minggu Ini
            </a>
        </div>

        @if ($enrolledCourses->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Peringkat per Kursus</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($enrolledCourses as $course)
                        <a href="{{ route('student.courses.leaderboard', $course) }}"
                            class="px-3 py-1.5 rounded-full text-xs border dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ $course->title }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- User Rank Card -->
        @if ($userRank)
            <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90">Peringkat Anda</p>
                        <p class="text-3xl font-bold">#{{ $userRank }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90">Total Poin</p>
                        <p class="text-xl font-bold">
                            {{ number_format($leaderboard->firstWhere('user_id', auth()->id())['total_points'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Leaderboard List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            @if ($leaderboard->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    Belum ada aktivitas poin {{ $period === 'weekly' ? 'minggu ini' : '' }}.
                </div>
            @endif
            <div class="divide-y dark:divide-gray-700">
                @foreach ($leaderboard as $item)
                    <div
                        class="p-4 flex items-center justify-between {{ $item['user_id'] == auth()->id() ? 'bg-primary/5' : '' }}">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 text-center font-bold text-gray-500">#{{ $item['rank'] }}</div>
                            <img src="{{ $item['avatar'] }}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $item['user_name'] }}</div>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span>Level {{ $item['current_level'] }}</span>
                                    <span>•</span>
                                    <span>{{ $item['badges_count'] }} lencana</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-primary">{{ number_format($item['total_points']) }}</div>
                            <div class="text-xs text-gray-500">poin</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
