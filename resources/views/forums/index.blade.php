@extends('layouts.app')

@section('title', 'Forum - ' . $course->title)
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', $course->title)

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-600 dark:text-gray-400">{{ $forum->description }}</p>
            </div>
            @can('create threads')
                <a href="{{ route('forums.thread.create', $course) }}"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                    + Buat Thread Baru
                </a>
            @endcan
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="divide-y dark:divide-gray-700">
                @forelse($threads as $thread)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    @if ($thread->is_pinned)
                                        <span class="text-yellow-500 text-xs">📌</span>
                                    @endif
                                    @if ($thread->is_locked)
                                        <span class="text-red-500 text-xs">🔒</span>
                                    @endif
                                    @if ($thread->is_solved)
                                        <span class="text-green-500 text-xs">✅</span>
                                    @endif
                                    <a href="{{ route('forums.thread.show', [$course, $thread]) }}"
                                        class="font-semibold text-gray-800 dark:text-white hover:text-primary">
                                        {{ $thread->title }}
                                    </a>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mt-1">
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
                            <div class="text-right text-xs text-gray-500">
                                @if ($thread->lastPostUser)
                                    <div>Balasan terakhir: {{ $thread->lastPostUser->name }}</div>
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
                        <p>Belum ada thread. Jadilah yang pertama membuat diskusi!</p>
                        @can('create threads')
                            <a href="{{ route('forums.thread.create', $course) }}"
                                class="mt-3 inline-block text-primary hover:underline">Buat Thread Baru →</a>
                        @endcan
                    </div>
                @endforelse
            </div>
        </div>

        {{ $threads->links() }}
    </div>
@endsection
