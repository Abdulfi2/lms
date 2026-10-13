@extends('layouts.app')

@section('title', 'Testimoni')
@section('page-title', 'Testimoni')
@section('page-subtitle', 'Kelola testimoni yang tampil di halaman beranda')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.testimonials.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
            + Testimoni Baru
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Testimoni</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Urutan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($testimonials as $testimonial)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold flex-shrink-0">
                                        {{ $testimonial->initial() }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800 dark:text-white">{{ $testimonial->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $testimonial->role }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($testimonial->content, 60) }}</td>
                            <td class="px-6 py-4 text-sm text-yellow-500">{{ str_repeat('★', $testimonial->rating) }}{{ str_repeat('☆', 5 - $testimonial->rating) }}</td>
                            <td class="px-6 py-4 text-sm">{{ $testimonial->order }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full uppercase {{ $testimonial->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $testimonial->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Yakin hapus testimoni dari {{ $testimonial->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada testimoni.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $testimonials->links() }}
        </div>
    </div>
</div>
@endsection
