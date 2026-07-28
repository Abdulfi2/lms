@extends('layouts.app')

@section('title', $thread->title . ' - ' . $forum->title)
@section('page-title', $thread->title)
@section('page-subtitle', $forum->title)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Thread Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex flex-wrap gap-2 mb-2">
                        @if ($thread->is_pinned)
                            <span class="text-yellow-500 text-xs">📌 Pinned</span>
                        @endif
                        @if ($thread->is_locked)
                            <span class="text-red-500 text-xs">🔒 Locked</span>
                        @endif
                        @if ($thread->is_solved)
                            <span class="text-green-500 text-xs">✅ Solved</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $thread->title }}</h1>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                        <span>Oleh: {{ $thread->user->name }}</span>
                        <span>&bull;</span>
                        <span>{{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Kontrol moderasi instruktur -->
                <div class="flex items-center space-x-2 shrink-0">
                    <form action="{{ route('instructor.forums.thread.pin', [$course, $forum, $thread]) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-3 py-1.5 text-xs rounded-lg border {{ $thread->is_pinned ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            {{ $thread->is_pinned ? 'Lepas Sematan' : 'Sematkan' }}
                        </button>
                    </form>
                    <form action="{{ route('instructor.forums.thread.lock', [$course, $forum, $thread]) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-3 py-1.5 text-xs rounded-lg border {{ $thread->is_locked ? 'bg-red-100 text-red-800 border-red-200' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            {{ $thread->is_locked ? 'Buka Kunci' : 'Kunci Thread' }}
                        </button>
                    </form>
                    <form action="{{ route('instructor.forums.thread.destroy', [$course, $forum, $thread]) }}" method="POST"
                        onsubmit="return confirm('Hapus thread ini beserta semua balasannya? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-1.5 text-xs rounded-lg border border-red-300 text-red-600 hover:bg-red-50">
                            Hapus Thread
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 prose max-w-none dark:prose-invert">
                {!! nl2br(e($thread->content)) !!}
            </div>

            <div class="mt-4 flex flex-wrap gap-1">
                @foreach ($thread->tags as $tag)
                    <span class="px-2 py-0.5 text-xs rounded-full"
                        style="background: {{ $tag->color }}20; color: {{ $tag->color }}">
                        #{{ $tag->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Posts / Replies -->
        <div class="space-y-4">
            @foreach ($posts as $post)
                <div id="post-{{ $post->id }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=3B82F6&color=white' }}"
                                class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $post->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
                            </div>
                            @if ($post->is_solution)
                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">✓ Solusi</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-3">
                            @unless ($post->is_solution)
                                <form action="{{ route('instructor.forums.thread.post.solution', [$course, $forum, $thread, $post]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-primary hover:underline whitespace-nowrap">
                                        Tandai Solusi
                                    </button>
                                </form>
                            @endunless
                            <form action="{{ route('instructor.forums.thread.post.delete', [$course, $forum, $thread, $post]) }}"
                                method="POST" onsubmit="return confirm('Hapus post ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-4 prose max-w-none dark:prose-invert">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                </div>
            @endforeach
        </div>

        {{ $posts->links() }}

        <!-- Reply Form -->
        @unless ($thread->is_locked)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Tulis Balasan (sebagai Instruktur)</h3>
                <form action="{{ route('instructor.forums.thread.post.store', [$course, $forum, $thread]) }}" method="POST">
                    @csrf
                    <textarea name="content" rows="5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        placeholder="Tulis balasan Anda..." required></textarea>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 text-center text-yellow-800 dark:text-yellow-400">
                🔒 Thread ini terkunci. Buka kunci di atas untuk membalas.
            </div>
        @endunless
    </div>
@endsection
