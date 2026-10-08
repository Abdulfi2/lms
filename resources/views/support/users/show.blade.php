@extends('layouts.app')

@section('title', 'Detail Pengguna')
@section('page-title', 'Detail Pengguna')
@section('page-subtitle', 'Informasi lihat-saja untuk membantu troubleshooting')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('support.users.index') }}" class="text-sm text-gray-500 hover:text-primary transition">&larr; Kembali ke daftar</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" class="w-16 h-16 rounded-full object-cover">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-1">
                    @foreach ($user->roles as $role)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-primary/10 text-primary capitalize">{{ str_replace('_', ' ', $role->name) }}</span>
                    @endforeach
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Bergabung</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $user->created_at->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Email Terverifikasi</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $user->email_verified_at ? 'Ya' : 'Belum' }}</p>
            </div>
            @if ($user->profile)
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status Approval</p>
                    <p class="font-medium text-gray-800 dark:text-white capitalize">{{ $user->profile->approval_status ?? '-' }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Enrollment Kursus</h3>
        </div>
        <div class="divide-y dark:divide-gray-700">
            @forelse ($user->enrollments as $enrollment)
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $enrollment->course->title ?? 'Kursus dihapus' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->enrolled_at?->format('d M Y') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $enrollment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($enrollment->payment_status) }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Belum ada enrollment.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
