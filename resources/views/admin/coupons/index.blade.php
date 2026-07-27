@extends('layouts.app')

@section('title', 'Kupon & Diskon')
@section('page-title', 'Kupon & Diskon')
@section('page-subtitle', 'Kelola kode kupon diskon untuk pendaftaran kursus')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form method="GET" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode kupon..."
                class="px-4 py-2 rounded-lg border dark:bg-gray-700">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Cari</button>
        </form>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Tambah Kupon</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diskon</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($coupons as $coupon)
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $coupon->code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $coupon->type === 'percentage' ? $coupon->value . '%' : 'Rp ' . number_format($coupon->value, 0, ',', '.') }}
                            @if ($coupon->min_purchase)
                                <div class="text-xs text-gray-400">Min. Rp {{ number_format($coupon->min_purchase, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $coupon->course->title ?? 'Semua Kursus' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if ($coupon->starts_at || $coupon->expires_at)
                                {{ optional($coupon->starts_at)->format('d/m/Y') ?? '-' }} s/d {{ optional($coupon->expires_at)->format('d/m/Y') ?? '-' }}
                            @else
                                <span class="text-gray-400">Tanpa batas waktu</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-1 text-xs rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $coupon->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline"
                                onsubmit="return confirm('Hapus kupon {{ $coupon->code }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada kupon.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $coupons->links() }}
</div>
@endsection
