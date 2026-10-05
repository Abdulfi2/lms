@extends('layouts.client')

@section('title', $article->meta_title ?: $article->title)
@section('page-title', $article->title)
@section('page-subtitle', $article->excerpt ?? strip_tags($article->content))
@section('og-title', $article->meta_title ?: $article->title)
@section('meta-description', $article->meta_description ?: ($article->excerpt ?: Str::limit(strip_tags($article->content), 160)))
@if ($article->og_image)
    @section('og-image', Storage::url($article->og_image))
@elseif ($article->featured_image)
    @section('og-image', Storage::url($article->featured_image))
@endif

@section('content')
    <div class="max-w-4xl mx-auto">
        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <!-- Featured Image -->
            @if ($article->featured_image)
                <div class="w-full h-80 md:h-96 overflow-hidden">
                    <img src="{{ Storage::url($article->featured_image) }}" class="w-full h-full object-cover">
                </div>
            @endif

            <!-- Article Header -->
            <div class="p-6 md:p-8 border-b dark:border-gray-700">
                <div class="flex items-center gap-3 text-sm text-gray-500 mb-4 flex-wrap">
                    <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs">
                        {{ $article->category->name ?? 'Umum' }}
                    </span>
                    <span><i class="far fa-calendar-alt mr-1"></i> {{ $article->published_at->format('d F Y') }}</span>
                    <span><i class="far fa-clock mr-1"></i> {{ $article->reading_time }}</span>
                    <span><i class="far fa-eye mr-1"></i> {{ number_format($article->views) }} dilihat</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $article->title }}</h1>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $article->author->avatar_url }}"
                            class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $article->author->name }}</p>
                            <p class="text-sm text-gray-500">Penulis</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Article Content -->
            <div class="p-6 md:p-8 prose dark:prose-invert max-w-none">
                {!! $article->content !!}
            </div>

            <!-- Tags -->
            @if ($article->tags->isNotEmpty())
                <div class="px-6 md:px-8 pb-6 flex flex-wrap gap-2">
                    @foreach ($article->tags as $tag)
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Article Footer -->
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-700/50 border-t dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div class="flex gap-4">
                        <div x-data="articleLike({{ $article->id }}, {{ $isLiked ? 'true' : 'false' }}, {{ $article->likes }})">
                            <button @click="toggle()" :disabled="loading"
                                class="flex items-center gap-2 transition"
                                :class="liked ? 'text-red-500' : 'text-gray-500 hover:text-red-500'">
                                <i :class="liked ? 'fas fa-heart' : 'far fa-heart'"></i>
                                <span x-text="count"></span>
                            </button>
                        </div>

                        <div x-data="articleShare({{ $article->id }}, {{ $article->shares }}, @js($article->title))" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 text-gray-500 hover:text-primary transition">
                                <i class="far fa-share-alt"></i>
                                <span>Bagikan</span>
                                <span x-show="count > 0" x-text="'(' + count + ')'"></span>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute bottom-full mb-2 left-0 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 p-2 w-48 z-20"
                                style="display: none;">
                                <a href="#" @click.prevent="shareTo('whatsapp')"
                                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fab fa-whatsapp text-green-500 w-4"></i> WhatsApp
                                </a>
                                <a href="#" @click.prevent="shareTo('facebook')"
                                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fab fa-facebook text-blue-600 w-4"></i> Facebook
                                </a>
                                <a href="#" @click.prevent="shareTo('twitter')"
                                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fab fa-twitter text-sky-500 w-4"></i> Twitter
                                </a>
                                <button @click="copyLink()"
                                    class="w-full flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="far fa-copy w-4"></i> Salin Tautan
                                </button>
                            </div>
                        </div>
                    </div>
                    <button onclick="window.print()" class="text-gray-500 hover:text-primary transition">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>
            </div>
        </article>

        <!-- Comments -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 md:p-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Komentar ({{ $comments->count() }})</h3>

            @if (!$article->allow_comments)
                <p class="text-sm text-gray-500 dark:text-gray-400">Komentar dinonaktifkan untuk artikel ini.</p>
            @elseif (Auth::check())
                <form method="POST" action="{{ route('articles.comments.store', $article) }}" class="mb-6">
                    @csrf
                    <textarea name="content" rows="3" required maxlength="1000" placeholder="Tulis komentar Anda..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('content') }}</textarea>
                    @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <button type="submit" class="mt-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">Kirim Komentar</button>
                </form>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    <a href="{{ route('login') }}" class="text-primary hover:underline">Masuk</a> untuk menulis komentar.
                </p>
            @endif

            <div class="space-y-4 divide-y dark:divide-gray-700">
                @forelse ($comments as $comment)
                    <div class="flex items-start justify-between gap-3 {{ !$loop->first ? 'pt-4' : '' }}">
                        <div class="flex items-start gap-3 min-w-0">
                            <img src="{{ $comment->user->avatar_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                            <div class="min-w-0">
                                <p class="font-medium text-sm text-gray-800 dark:text-white">{{ $comment->user->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5 break-words">{{ $comment->content }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if (Auth::check() && (Auth::id() === $comment->user_id || Auth::id() === $article->user_id || Auth::user()->hasRole('admin')))
                            <form method="POST" action="{{ route('articles.comments.destroy', [$article, $comment]) }}" onsubmit="return confirm('Hapus komentar ini?')" class="flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada komentar. Jadilah yang pertama berkomentar.</p>
                @endforelse
            </div>
        </div>

        <!-- Related Articles -->
        @if ($relatedArticles->count() > 0)
            <div class="mt-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Artikel Terkait</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($relatedArticles as $related)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
                            @if ($related->featured_image)
                                <img src="{{ Storage::url($related->featured_image) }}"
                                    class="w-full h-40 object-cover group-hover:scale-105 transition">
                            @endif
                            <div class="p-4">
                                <span class="text-xs text-gray-500">{{ $related->published_at->format('d M Y') }}</span>
                                <a href="{{ route('articles.show', $related->slug) }}">
                                    <h4
                                        class="font-semibold text-gray-900 dark:text-white mt-1 hover:text-primary transition">
                                        {{ Str::limit($related->title, 50) }}
                                    </h4>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function articleLike(articleId, initiallyLiked, initialCount) {
        return {
            liked: initiallyLiked,
            count: initialCount,
            loading: false,
            toggle() {
                @guest
                    window.location.href = '{{ route('login') }}';
                    return;
                @endguest

                if (this.loading) return;
                this.loading = true;

                fetch(`/articles/${articleId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                    .then((r) => r.json())
                    .then((data) => {
                        this.liked = data.liked;
                        this.count = data.likes;
                    })
                    .catch(() => {
                        if (window.toast) window.toast.error('Gagal memproses like. Silakan coba lagi.');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
        };
    }

    function articleShare(articleId, initialCount, articleTitle) {
        return {
            open: false,
            count: initialCount,
            recordShare() {
                fetch(`/articles/${articleId}/share`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                    .then((r) => r.json())
                    .then((data) => {
                        this.count = data.shares;
                    })
                    .catch(() => {});
            },
            shareTo(platform) {
                const url = encodeURIComponent(window.location.href);
                const title = encodeURIComponent(articleTitle);
                let shareUrl = '';

                if (platform === 'whatsapp') shareUrl = `https://wa.me/?text=${title}%20${url}`;
                if (platform === 'facebook') shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                if (platform === 'twitter') shareUrl = `https://twitter.com/intent/tweet?text=${title}&url=${url}`;

                window.open(shareUrl, '_blank', 'noopener,noreferrer,width=600,height=500');
                this.recordShare();
                this.open = false;
            },
            copyLink() {
                navigator.clipboard
                    .writeText(window.location.href)
                    .then(() => {
                        if (window.toast) window.toast.success('Tautan berhasil disalin.');
                        this.recordShare();
                        this.open = false;
                    })
                    .catch(() => {
                        if (window.toast) window.toast.error('Gagal menyalin tautan.');
                    });
            },
        };
    }
</script>
@endpush
