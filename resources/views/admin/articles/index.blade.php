@extends('layouts.app')

@section('title', 'Manajemen Artikel')
@section('page-title', 'Artikel')
@section('page-subtitle', 'Kelola artikel berita dan informasi')

@section('content')
    <div x-data="articleManager()" class="space-y-6">
        <div class="flex justify-between items-center">
            <div class="flex gap-2">
                <input type="text" x-model="search" @input.debounce.300="fetchArticles()" placeholder="Cari artikel..."
                    class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                <select x-model="status" @change="fetchArticles()" class="px-4 py-2 rounded-lg border dark:bg-gray-700">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="revision">Revisi</option>
                    <option value="ready_to_publish">Siap Terbit</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <a href="{{ route('admin.articles.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg">+ Artikel
                Baru</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Dilihat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($articles as $article)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($article->featured_image)
                                            <img src="{{ Storage::url($article->featured_image) }}"
                                                class="w-10 h-10 rounded object-cover">
                                        @endif
                                        <span class="font-medium">{{ Str::limit($article->title, 40) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $article->category->name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $article->author->name }}</td>
                                <td class="px-6 py-4">{{ number_format($article->views) }}</td>
                                <td class="px-6 py-4">
                                    <button @click="toggleStatus({{ $article->id }})">
                                        <x-article-status-badge :status="$article->status" class="cursor-pointer" />
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $article->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                            title="Lihat Artikel" class="text-gray-500 hover:text-gray-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.articles.edit', $article) }}" title="Edit"
                                            class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button @click="deleteArticle({{ $article->id }})" title="Hapus"
                                            class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $articles->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        function articleManager() {
            return {
                search: '',
                status: '',
                fetchArticles() {
                    window.location.href = '{{ route('admin.articles.index') }}?search=' + this.search + '&status=' + this
                        .status;
                },
                toggleStatus(id) {
                    fetch(`/admin/articles/${id}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => location.reload());
                },
                deleteArticle(id) {
                    if (confirm('Yakin hapus artikel ini?')) {
                        fetch(`/admin/articles/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(() => location.reload());
                    }
                }
            }
        }
    </script>
@endpush
@endsection
