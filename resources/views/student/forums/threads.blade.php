@extends('layouts.app')

@section('title', $forum->title . ' - ' . $course->title)
@section('page-title', $forum->title)
@section('page-subtitle', $course->title)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $forum->title }}</h1>
                    <p class="text-gray-500 text-sm mt-1">{{ $forum->description }}</p>
                </div>
                <a href="{{ route('student.forums.thread.create', [$course, $forum]) }}"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Thread Baru
                </a>
            </div>
        </div>

        <!-- Threads List -->
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
                                    <a href="{{ route('student.forums.thread.show', [$course, $forum, $thread]) }}"
                                        class="font-semibold text-gray-800 dark:text-white hover:text-primary">
                                        {{ $thread->title }}
                                    </a>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 mt-1">
                                    <span>Oleh: {{ $thread->user->name }}</span>
                                    <span>•</span>
                                    <span>{{ $thread->created_at->diffForHumans() }}</span>
                                    <span>•</span>
                                    <span>{{ $thread->reply_count }} balasan</span>
                                    <span>•</span>
                                    <span>{{ $thread->view_count }} dilihat</span>
                                </div>
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach ($thread->tags as $tag)
                                        <span class="px-2 py-0.5 text-xs rounded-full"
                                            style="background: {{ $tag->color }}20; color: {{ $tag->color }}">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
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
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>Belum ada thread diskusi. Jadilah yang pertama memulai diskusi!</p>
                        <a href="{{ route('student.forums.thread.create', [$course, $forum]) }}"
                            class="mt-3 inline-block text-primary hover:underline">
                            Buat Thread Baru →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{ $threads->links() }}
    </div>
@endsection
