@extends('layouts.app')

@section('title', 'Pemakaian Kupon')
@section('page-title', 'Pemakaian Kupon')
@section('page-subtitle', 'Kupon yang digunakan siswa saat mendaftar kursus Anda')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">Ringkasan per Kupon</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Kupon</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Dipakai</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Diskon</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($summary as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $row['coupon']->code ?? '(dihapus)' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">{{ $row['times_used'] }}x</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($row['total_discount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada kupon yang dipakai pada kursus Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">Riwayat Pemakaian</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kupon</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Diskon</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($redemptions as $enrollment)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $enrollment->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($enrollment->course->title ?? '-', 35) }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-600 dark:text-gray-300">{{ $enrollment->coupon->code ?? '(dihapus)' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($enrollment->discount_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ optional($enrollment->enrolled_at)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $redemptions->links() }}
        </div>
    </div>
</div>
@endsection
