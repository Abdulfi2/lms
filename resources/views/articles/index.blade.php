@extends('layouts.client')

@section('title', 'Artikel & Berita')
@section('page-title', 'Artikel & Berita')
@section('page-subtitle', 'Informasi terbaru seputar pendidikan dan teknologi')

@section('content')
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
            <!-- Featured Articles -->
            @if ($featuredArticles->count() > 0)
                <div class="mb-8">
                    <div class="grid md:grid-cols-3 gap-4">
                        @foreach ($featuredArticles as $featured)
                            <div class="relative rounded-xl overflow-hidden h-64 group cursor-pointer"
                                onclick="window.location='{{ route('articles.show', $featured->slug) }}'">
                                <img src="{{ $featured->featured_image ? Storage::url($featured->featured_image) : 'https://placehold.co/600x400/3B82F6/white?text=Article' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                                    <div>
                                        <span class="text-xs text-white/80">{{ $featured->category->name ?? 'Umum' }}</span>
                                        <h3 class="text-white font-bold text-lg">{{ Str::limit($featured->title, 50) }}</h3>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Articles List -->
            <div class="space-y-6">
                @forelse($articles as $article)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        <div class="flex flex-col md:flex-row">
                            @if ($article->featured_image)
                                <div class="md:w-72 h-48 md:h-auto">
                                    <img src="{{ Storage::url($article->featured_image) }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="flex-1 p-6">
                                <div class="flex items-center gap-3 text-sm text-gray-500 mb-2">
                                    <span class="bg-primary/10 text-primary px-2 py-1 rounded-full text-xs">
                                        {{ $article->category->name ?? 'Umum' }}
                                    </span>
                                    <span>{{ $article->published_at->format('d M Y') }}</span>
                                    <span>{{ $article->reading_time }}</span>
                                </div>
                                <a href="{{ route('articles.show', $article->slug) }}">
                                    <h2
                                        class="text-xl font-bold text-gray-900 dark:text-white hover:text-primary transition mb-2">
                                        {{ $article->title }}
                                    </h2>
                                </a>
                                <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                    {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 120) }}</p>
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <img src="{{ $article->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->author->name) }}"
                                            class="w-6 h-6 rounded-full">
                                        <span>{{ $article->author->name }}</span>
                                    </div>
                                    <a href="{{ route('articles.show', $article->slug) }}"
                                        class="text-primary hover:underline">
                                        Baca Selengkapnya →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-newspaper text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada artikel.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="lg:w-80 space-y-6">
            <!-- Search -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Cari Artikel</h3>
                <form method="GET" action="{{ route('articles.index') }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..."
                            class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <button type="submit" class="absolute right-2 top-2 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Categories -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Kategori</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('articles.index') }}"
                            class="flex justify-between text-gray-600 dark:text-gray-400 hover:text-primary transition">
                            Semua
                            <span>{{ \App\Models\Article::published()->count() }}</span>
                        </a>
                    </li>
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('articles.index', ['category' => $cat->slug]) }}"
                                class="flex justify-between text-gray-600 dark:text-gray-400 hover:text-primary transition">
                                {{ $cat->name }}
                                <span>{{ $cat->articles_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
@endsection
