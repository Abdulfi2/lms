@extends('layouts.app')

@section('title', 'Kursus Saya')
@section('page-title', 'Kursus Saya')
@section('page-subtitle', 'Kelola kursus yang Anda buat')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ route('instructor.courses.create') }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">+ Kursus Baru</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Dibuat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/60x40/3B82F6/white?text=Course' }}"
                                            class="w-16 h-10 object-cover rounded">
                                        <div>
                                            <div class="font-medium">{{ $course->title }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ Str::limit($course->short_description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $course->formatted_price }}</td>
                                <td class="px-6 py-4">{{ $course->total_students }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $course->status == 'published' ? 'bg-green-100 text-green-800' : ($course->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $course->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a :href="'/instructor/courses/' + course.id + '/sections'"
                                        class="text-green-600 hover:text-green-800" title="Kelola Sections">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('instructor.courses.edit', $course) }}"
                                        class="text-blue-600 hover:text-blue-800">Edit</a>
                                    <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST"
                                        class="inline-block" onsubmit="return confirm('Yakin hapus kursus ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada kursus. Buat
                                    kursus pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
@endsection
