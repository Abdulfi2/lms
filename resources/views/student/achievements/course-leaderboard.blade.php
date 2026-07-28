@extends('layouts.app')

@section('title', 'Peringkat Kelas - ' . $course->title)
@section('page-title', 'Peringkat Kelas')
@section('page-subtitle', $course->title)

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <a href="{{ route('student.leaderboard') }}" class="text-primary hover:underline text-sm">&larr; Kembali ke Papan Peringkat</a>

        <p class="text-sm text-gray-500">Peringkat dihitung dari progress penyelesaian kursus dan rata-rata nilai quiz di kursus ini — bukan poin gamifikasi global.</p>

        @if ($userRank)
            <div class="bg-gradient-to-r from-primary to-secondary rounded-xl p-4 text-white">
                <p class="text-sm opacity-90">Peringkat Anda di Kelas Ini</p>
                <p class="text-3xl font-bold">#{{ $userRank }}</p>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            @if ($leaderboard->isEmpty())
                <div class="p-8 text-center text-gray-500">Belum ada siswa lain di kursus ini.</div>
            @endif
            <div class="divide-y dark:divide-gray-700">
                @foreach ($leaderboard as $item)
                    <div class="p-4 flex items-center justify-between {{ $item['user_id'] == auth()->id() ? 'bg-primary/5' : '' }}">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 text-center font-bold text-gray-500">#{{ $item['rank'] }}</div>
                            <img src="{{ $item['avatar'] }}" class="w-10 h-10 rounded-full object-cover">
                            <div class="font-semibold text-gray-800 dark:text-white">{{ $item['user_name'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-primary">{{ $item['progress'] }}% selesai</div>
                            <div class="text-xs text-gray-500">
                                @if ($item['avg_quiz_score'] !== null)
                                    Rata-rata quiz: {{ $item['avg_quiz_score'] }}%
                                @else
                                    Belum ada quiz
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
