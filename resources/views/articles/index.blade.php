@extends('layouts.client')

@section('title', 'Blog & Artikel')
@section('meta-description', 'Temukan berbagai artikel edukatif, inspiratif, dan informatif seputar zakat, pendidikan, pengembangan diri, manajemen lembaga, dan topik bermanfaat lainnya.')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Blog & Artikel</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2 max-w-2xl">
            Temukan berbagai artikel edukatif, inspiratif, dan informatif seputar zakat, pendidikan, pengembangan diri, manajemen lembaga, dan topik bermanfaat lainnya.
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Category Filter Pills -->
            <div class="flex flex-wrap gap-2 mb-5">
                <a href="{{ route('articles.index', array_filter(['search' => request('search'), 'sort' => request('sort')])) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-primary' }}">
                    Semua ({{ $totalPublished }})
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('articles.index', array_filter(['category' => $cat->slug, 'search' => request('search'), 'sort' => request('sort')])) }}"
                        class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') === $cat->slug ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-primary' }}">
                        {{ $cat->name }} ({{ $cat->articles_count }})
                    </a>
                @endforeach
            </div>

            <!-- Count + Sort -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 text-sm">
                <p class="text-gray-500 dark:text-gray-400">
                    @php($grandTotal = $articles->total() + ($heroArticle ? 1 : 0))
                    @if ($grandTotal > 0)
                        Menampilkan {{ $articles->total() > 0 ? $articles->firstItem() : 1 }}&ndash;{{ $articles->total() > 0 ? $articles->lastItem() + ($heroArticle ? 1 : 0) : 1 }} dari {{ $grandTotal }} artikel
                    @else
                        Tidak ada artikel ditemukan
                    @endif
                </p>
                <form method="GET" class="flex items-center gap-2">
                    @if (request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    @if (request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if (request('tag'))<input type="hidden" name="tag" value="{{ request('tag') }}">@endif
                    <label class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                        <i class="fas fa-sort"></i> Urutkan:
                    </label>
                    <select name="sort" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary">
                        <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                        <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </form>
            </div>

            <!-- Hero / Artikel Unggulan -->
            @if ($heroArticle)
                <a href="{{ route('articles.show', $heroArticle->slug) }}"
                    class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mb-6 hover:shadow-lg transition group">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-2/5 h-56 md:h-auto relative overflow-hidden">
                            <img src="{{ $heroArticle->featured_image ? Storage::url($heroArticle->featured_image) : 'https://placehold.co/600x400/769826/white?text=Article' }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-primary text-white text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $heroArticle->category->name ?? 'Umum' }}
                            </span>
                        </div>
                        <div class="flex-1 p-6 flex flex-col justify-center">
                            <span class="text-primary text-xs font-semibold uppercase tracking-wide mb-1">Artikel Unggulan</span>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white group-hover:text-primary transition mb-2">
                                {{ $heroArticle->title }}
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                {{ $heroArticle->excerpt ?: Str::limit(strip_tags($heroArticle->content), 160) }}
                            </p>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $heroArticle->author->avatar_url }}" class="w-7 h-7 rounded-full object-cover">
                                        <div class="leading-tight">
                                            <p class="font-medium text-gray-700 dark:text-gray-300">{{ $heroArticle->author->name }}</p>
                                        </div>
                                    </div>
                                    <span><i class="far fa-calendar-alt mr-1"></i>{{ $heroArticle->published_at->format('d M Y') }}</span>
                                    <span><i class="far fa-clock mr-1"></i>{{ $heroArticle->reading_time }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg group-hover:bg-secondary transition flex-shrink-0">
                                    Baca Artikel <i class="fas fa-arrow-right text-xs"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endif

            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($articles as $article)
                    <a href="{{ route('articles.show', $article->slug) }}"
                        class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
                        <div class="h-40 relative overflow-hidden">
                            <img src="{{ $article->featured_image ? Storage::url($article->featured_image) : 'https://placehold.co/600x400/769826/white?text=Article' }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-primary text-white text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $article->category->name ?? 'Umum' }}
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-primary transition line-clamp-2 mb-1">
                                {{ $article->title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 100) }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <img src="{{ $article->author->avatar_url }}" class="w-5 h-5 rounded-full object-cover">
                                <span>{{ $article->author->name }}</span>
                                <span>&middot;</span>
                                <span>{{ $article->published_at->format('d M Y') }}</span>
                            </div>
                            <span class="text-primary text-sm font-medium group-hover:underline">Baca selengkapnya &rarr;</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
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
        <aside class="lg:w-80 flex-shrink-0 space-y-6">
            <!-- Quote -->
            <div class="bg-primary/10 dark:bg-primary/20 rounded-xl p-5 flex items-start gap-3">
                <div class="w-10 h-10 bg-primary/20 dark:bg-primary/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-open text-primary"></i>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300">Ilmu hari ini, untuk dampak yang lebih besar esok hari.</p>
            </div>

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

            <!-- Artikel Populer -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900 dark:text-white"><i class="fas fa-fire text-orange-500 mr-1"></i> Artikel Populer</h3>
                    <a href="{{ route('articles.index', ['sort' => 'popular']) }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse ($popularArticles as $i => $popular)
                        <a href="{{ route('articles.show', $popular->slug) }}" class="flex items-start gap-3 group">
                            <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                                {{ $i + 1 }}
                            </span>
                            <img src="{{ $popular->featured_image ? Storage::url($popular->featured_image) : 'https://placehold.co/100x100/769826/white?text=A' }}"
                                class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white group-hover:text-primary transition line-clamp-2">
                                    {{ $popular->title }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $popular->published_at->format('d M Y') }} &middot; {{ $popular->reading_time }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Belum ada artikel.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tag Populer -->
            @if ($popularTags->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white"><i class="fas fa-hashtag text-gray-400 mr-1"></i> Tag Populer</h3>
                        <a href="{{ route('articles.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($popularTags as $tag)
                            <a href="{{ route('articles.index', ['tag' => $tag->slug]) }}"
                                class="px-3 py-1 text-xs rounded-full transition {{ request('tag') === $tag->slug ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary/10 hover:text-primary' }}">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Newsletter -->
            <div class="bg-gradient-to-br from-primary/10 to-green-50 dark:from-primary/20 dark:to-gray-800 border border-primary/20 rounded-xl p-5">
                <div class="w-9 h-9 bg-primary/20 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-envelope text-primary"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Dapatkan Artikel Terbaru</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Berlangganan newsletter untuk mendapatkan artikel terbaru langsung ke email Anda.</p>
                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="space-y-2">
                    @csrf
                    <input type="email" name="email" required placeholder="Masukkan alamat email Anda"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary">
                    @error('email')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-secondary transition">
                        Berlangganan
                    </button>
                </form>
            </div>
        </aside>
    </div>
@endsection
