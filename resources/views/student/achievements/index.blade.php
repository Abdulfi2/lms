@extends('layouts.app')

@section('title', 'Prestasi Saya')
@section('page-title', 'Prestasi')
@section('page-subtitle', 'Pencapaian Anda selama belajar di LMS')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Level</p>
                <p class="text-2xl font-bold">{{ $gamification['current_level'] }}</p>
                <p class="text-xs text-gray-500">{{ $gamification['current_level_name'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Total Poin</p>
                <p class="text-2xl font-bold">{{ number_format($gamification['total_points']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Prestasi</p>
                <p class="text-2xl font-bold">{{ $stats['earned_achievements'] }}/{{ $stats['total_achievements'] }}</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                    <div class="bg-primary h-1.5 rounded-full" style="width: {{ $stats['completion_percentage'] }}%"></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <p class="text-gray-500 text-sm">Poin dari Prestasi</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_points_earned']) }}</p>
            </div>
        </div>

        <!-- Badges Section -->
        @if ($badges->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">🏅 Lencana yang Didapat</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach ($badges->take(8) as $userBadge)
                        <div class="group relative">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-md transition-transform hover:scale-110"
                                style="background: {{ $userBadge->badge->color }}20; border: 2px solid {{ $userBadge->badge->color }}">
                                <span class="text-3xl">{{ $userBadge->badge->icon }}</span>
                            </div>
                            <div
                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10">
                                {{ $userBadge->badge->name }}
                                <div class="text-[10px] text-gray-300">{{ $userBadge->earned_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    @endforeach
                    @if ($badges->count() > 8)
                        <div
                            class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-sm font-semibold">
                            +{{ $badges->count() - 8 }}
                        </div>
                    @endif
                </div>
                @if ($badges->count() > 8)
                    <div class="mt-4 text-center">
                        <a href="{{ route('student.badges') }}" class="text-primary hover:underline text-sm">Lihat Semua
                            Lencana →</a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Recent Achievements -->
        @if ($recentAchievements->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">🎖️ Prestasi Terbaru</h3>
                <div class="space-y-3">
                    @foreach ($recentAchievements as $userAchievement)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <span class="text-xl">{{ $userAchievement->achievement->icon ?? '🏆' }}</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-white">
                                        {{ $userAchievement->achievement->name }}</h4>
                                    <p class="text-xs text-gray-500">Didapat:
                                        {{ $userAchievement->earned_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <span
                                class="text-xs font-semibold text-green-600">+{{ number_format($userAchievement->achievement->points_reward) }}
                                poin</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Achievement List (Tab) -->
        <div x-data="{ tab: 'earned' }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="border-b dark:border-gray-700 px-6">
                <div class="flex space-x-6">
                    <button @click="tab = 'earned'" :class="{ 'border-primary text-primary': tab === 'earned' }"
                        class="py-3 border-b-2 font-medium text-sm transition">
                        Sudah Didapat ({{ $earnedAchievements->count() }})
                    </button>
                    <button @click="tab = 'pending'" :class="{ 'border-primary text-primary': tab === 'pending' }"
                        class="py-3 border-b-2 font-medium text-sm transition">
                        Belum Didapat ({{ $pendingAchievements->count() }})
                    </button>
                </div>
            </div>

            <!-- Earned Achievements Tab -->
            <div x-show="tab === 'earned'" class="divide-y dark:divide-gray-700">
                @forelse($earnedAchievements as $achievement)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <span class="text-2xl">{{ $achievement->icon ?? '🏆' }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 dark:text-white">{{ $achievement->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $achievement->description }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-xs font-semibold text-green-600">+{{ number_format($achievement->points_reward) }}
                                    poin</span>
                                <a href="{{ route('student.achievements.show', $achievement) }}"
                                    class="block text-xs text-primary hover:underline mt-1">Detail</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">Belum ada prestasi yang didapat.</div>
                @endforelse
            </div>

            <!-- Pending Achievements Tab -->
            <div x-show="tab === 'pending'" class="divide-y dark:divide-gray-700">
                @forelse($pendingAchievements as $achievement)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-2xl opacity-50">{{ $achievement->icon ?? '🔒' }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-500">{{ $achievement->name }}</h4>
                                    <p class="text-sm text-gray-400">{{ $achievement->description }}</p>
                                    <div class="mt-1">
                                        <div class="flex justify-between text-xs text-gray-500 mb-0.5">
                                            <span>Progress</span>
                                            <span>{{ $achievement->current_progress }}/{{ $achievement->required_value }}</span>
                                        </div>
                                        <div class="w-48 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-primary h-1.5 rounded-full"
                                                style="width: {{ $achievement->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-xs font-semibold text-gray-500">+{{ number_format($achievement->points_reward) }}
                                    poin</span>
                                <a href="{{ route('student.achievements.show', $achievement) }}"
                                    class="block text-xs text-primary hover:underline mt-1">Detail</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">Semua prestasi sudah didapat! 🎉</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
