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
                        <span>•</span>
                        <span>{{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @if ($thread->user_id === auth()->id())
                    <a href="{{ route('student.forums.thread.edit', [$course, $forum, $thread]) }}"
                        class="px-3 py-1.5 text-xs border rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 shrink-0">
                        Edit Thread
                    </a>
                @endif
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
                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">✓
                                    Solusi</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-3">
                            <button onclick="likePost({{ $post->id }})"
                                class="flex items-center space-x-1 text-gray-500 hover:text-red-500 transition">
                                <svg class="w-5 h-5"
                                    :fill="{{ in_array($post->id, $likedPosts) ? 'currentColor' : 'none' }}"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span id="like-count-{{ $post->id }}">{{ $post->like_count }}</span>
                            </button>
                            @if ($post->user_id !== auth()->id())
                                <button onclick="toggleReportForm({{ $post->id }})"
                                    class="flex items-center text-gray-400 hover:text-yellow-600 transition" title="Lapor post ini">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3v18h2v-8h4.5l.5 1h6.5V5h-6l-.5-1H5" />
                                    </svg>
                                </button>
                            @endif
                            @if ($post->user_id === auth()->id() || auth()->user()->hasRole('admin'))
                                <form
                                    action="{{ route('student.forums.thread.post.delete', [$course, $forum, $thread, $post]) }}"
                                    method="POST" onsubmit="return confirm('Hapus post ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 prose max-w-none dark:prose-invert">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                    @if ($post->user_id !== auth()->id())
                        <div id="report-form-{{ $post->id }}" class="hidden mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <p class="text-sm font-medium mb-2">Laporkan post ini</p>
                            <select id="report-reason-{{ $post->id }}" class="w-full rounded-lg border-gray-300 text-sm mb-2">
                                <option value="spam">Spam</option>
                                <option value="offensive">Konten Tidak Pantas</option>
                                <option value="harassment">Pelecehan</option>
                                <option value="other">Lainnya</option>
                            </select>
                            <textarea id="report-description-{{ $post->id }}" rows="2" placeholder="Keterangan tambahan (opsional)"
                                class="w-full rounded-lg border-gray-300 text-sm mb-2"></textarea>
                            <div class="flex justify-end space-x-2">
                                <button onclick="toggleReportForm({{ $post->id }})"
                                    class="px-3 py-1.5 text-xs border rounded-lg">Batal</button>
                                <button onclick="submitReport({{ $post->id }})"
                                    class="px-3 py-1.5 text-xs bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Kirim Laporan</button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{ $posts->links() }}

        <!-- Reply Form -->
        @if ($canReply)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Tulis Balasan</h3>
                <form action="{{ route('student.forums.thread.post.store', [$course, $forum, $thread]) }}" method="POST">
                    @csrf
                    <textarea name="content" rows="5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        placeholder="Tulis balasan Anda..." required></textarea>
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        @elseif($thread->is_locked)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 text-center text-yellow-800 dark:text-yellow-400">
                🔒 Thread ini terkunci. Tidak dapat menambah balasan.
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function toggleReportForm(postId) {
                document.getElementById(`report-form-${postId}`).classList.toggle('hidden');
            }

            function submitReport(postId) {
                const reason = document.getElementById(`report-reason-${postId}`).value;
                const description = document.getElementById(`report-description-${postId}`).value;

                fetch(`/student/courses/{{ $course->id }}/forums/{{ $forum->id }}/threads/{{ $thread->id }}/posts/${postId}/report`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ reason, description })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            toggleReportForm(postId);
                        }
                    });
            }

            function likePost(postId) {
                fetch(`/student/courses/{{ $course->id }}/forums/{{ $forum->id }}/threads/{{ $thread->id }}/posts/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`like-count-${postId}`).innerText = data.like_count;
                        }
                    });
            }
        </script>
    @endpush
@endsection
