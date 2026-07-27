@extends('layouts.app')

@section('title', 'Kategori Artikel')
@section('page-title', 'Kategori Artikel')
@section('page-subtitle', 'Kelola kategori untuk artikel berita dan informasi')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.article-categories.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
            + Kategori Baru
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Artikel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800 dark:text-white">{{ $category->name }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($category->description, 60) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $category->articles()->count() }}</td>
                            <td class="px-6 py-4 text-sm">{{ $category->order }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full uppercase {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.article-categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                <form action="{{ route('admin.article-categories.destroy', $category) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Yakin hapus kategori {{ $category->name }}? Kategori ini dipakai oleh {{ $category->articles()->count() }} artikel.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada kategori artikel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
