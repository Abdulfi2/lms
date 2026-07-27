@extends('layouts.app')

@section('title', $forum->title . ' - ' . $course->title)
@section('page-title', $forum->title)
@section('page-subtitle', $course->title)

@section('content')
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $forum->title }}</h1>
                    <p class="text-gray-500 text-sm mt-1">{{ $forum->description }}</p>
                </div>
                <a href="{{ route('instructor.forums.index') }}" class="text-sm text-primary hover:underline">
                    &larr; Kembali ke Semua Forum
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="divide-y dark:divide-gray-700">
                @forelse($threads as $thread)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center flex-wrap gap-2">
                                    @if ($thread->is_pinned)
                                        <span class="text-yellow-500 text-xs">📌 Pinned</span>
                                    @endif
                                    @if ($thread->is_locked)
                                        <span class="text-red-500 text-xs">🔒 Locked</span>
                                    @endif
                                    @if ($thread->is_solved)
                                        <span class="text-green-500 text-xs">✓ Solved</span>
                                    @endif
                                    <a href="{{ route('instructor.forums.thread.show', [$course, $forum, $thread]) }}"
                                        class="font-semibold text-gray-800 dark:text-white hover:text-primary">
                                        {{ $thread->title }}
                                    </a>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 mt-1">
                                    <span>Oleh: {{ $thread->user->name }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $thread->created_at->diffForHumans() }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $thread->reply_count }} balasan</span>
                                    <span>&bull;</span>
                                    <span>{{ $thread->view_count }} dilihat</span>
                                </div>
                            </div>
                            <div class="text-right text-xs text-gray-500 min-w-[120px]">
                                @if ($thread->lastPostUser)
                                    <div>Balasan: {{ $thread->lastPostUser->name }}</div>
                                    <div>{{ \Carbon\Carbon::parse($thread->last_post_at)->diffForHumans() }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>Belum ada thread diskusi di forum ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{ $threads->links() }}
    </div>
@endsection
