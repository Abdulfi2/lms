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
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.articles.edit', $article) }}"
                                        class="text-blue-600 hover:text-blue-800">Edit</a>
                                    <button @click="deleteArticle({{ $article->id }})"
                                        class="text-red-600 hover:text-red-800">Hapus</button>
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
