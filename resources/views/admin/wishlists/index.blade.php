@extends('layouts.app')

@section('title', 'Statistik Wishlist')
@section('page-title', 'Wishlist Siswa')
@section('page-subtitle', 'Kursus yang paling banyak disimpan siswa untuk diambil nanti')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <p class="text-sm text-gray-500">Total Kursus Ter-wishlist</p>
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalWishlisted) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Instruktur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Di-wishlist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Terdaftar (Enrolled)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($courses as $course)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.courses.show', $course) }}" class="font-medium text-gray-800 dark:text-white hover:text-primary">
                                    {{ $course->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $course->instructor->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $course->formatted_price }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-pink-100 text-pink-800">{{ $course->wishlists_count }} siswa</span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ number_format($course->total_students) }} siswa</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada kursus yang di-wishlist siswa.</td>
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
