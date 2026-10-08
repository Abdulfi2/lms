@extends('layouts.app')

@section('title', 'Enrollment')
@section('page-title', 'Enrollment')
@section('page-subtitle', 'Lihat data enrollment untuk membantu troubleshooting (lihat-saja)')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama user atau judul kursus..."
            class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
            <option value="">Semua Status Pembayaran</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
        </select>
        <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $enrollment->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $enrollment->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($enrollment->course->title ?? '-', 35) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $enrollment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($enrollment->payment_status === 'refunded' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($enrollment->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $enrollment->progress ?? 0 }}%</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->enrolled_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Tidak ada enrollment ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($enrollments->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
