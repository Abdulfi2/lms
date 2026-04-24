@extends('layouts.app')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Siswa')
@section('page-subtitle', 'Daftar siswa yang terdaftar di kursus Anda')

@section('content')
    <div x-data="studentManager()" x-init="init()" class="space-y-6">
        <!-- Filter -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('instructor.students.index') }}" class="flex flex-wrap gap-3">
                <input type="text" name="search" placeholder="Cari nama atau email..." value="{{ request('search') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:bg-gray-700 flex-1 min-w-[200px]">
                <select name="course_id" class="px-4 py-2 rounded-lg border border-gray-300 dark:bg-gray-700">
                    <option value="">Semua Kursus</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Filter</button>
                <a href="{{ route('instructor.students.index') }}" class="px-4 py-2 border rounded-lg">Reset</a>
            </form>
        </div>

        <!-- Tabel Siswa -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tanggal Daftar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $enrollment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($enrollment->user->name) }}"
                                            class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <div class="font-medium text-gray-800 dark:text-white">
                                                {{ $enrollment->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $enrollment->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-800 dark:text-white">{{ $enrollment->course->title }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $enrollment->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="w-32">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span>Progress</span>
                                            <span>{{ round($enrollment->progress) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full"
                                                style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $enrollment->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('instructor.students.course.progress', [$enrollment->user, $enrollment->course]) }}"
                                        class="text-blue-600 hover:text-blue-800" title="Detail Progress">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="mailto:{{ $enrollment->user->email }}"
                                        class="text-green-600 hover:text-green-800" title="Email">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p>Belum ada siswa yang mendaftar di kursus Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $enrollments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
