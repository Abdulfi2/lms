@extends('layouts.app')

@section('title', 'Payout - ' . $instructor->name)
@section('page-title', 'Payout Instruktur')
@section('page-subtitle', $instructor->name . ' (' . $instructor->email . ')')

@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.payouts.index') }}" class="text-sm text-primary hover:underline">&larr; Kembali ke daftar instruktur</a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
            <p class="text-sm text-gray-500">Total Pendapatan</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
            <p class="text-sm text-gray-500">Sudah Dicairkan</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($totalPaidOut, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-2 {{ $outstanding > 0 ? 'border-yellow-400' : 'border-transparent' }}">
            <p class="text-sm text-gray-500">Saldo Tertunda</p>
            <p class="text-xl font-bold {{ $outstanding > 0 ? 'text-yellow-600' : 'text-gray-900 dark:text-white' }} mt-1">Rp {{ number_format($outstanding, 0, ',', '.') }}</p>
        </div>
    </div>

    @if ($outstanding > 0)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Catat Payout Baru</h3>
            <form method="POST" action="{{ route('admin.payouts.store', $instructor) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="1" max="{{ $outstanding }}" value="{{ old('amount', $outstanding) }}" required class="w-full rounded-lg border-gray-300">
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Metode</label>
                    <input type="text" name="method" value="{{ old('method') }}" placeholder="Transfer Bank" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Periode Mulai</label>
                    <input type="date" name="period_start" value="{{ old('period_start') }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Periode Selesai</label>
                    <input type="date" name="period_end" value="{{ old('period_end') }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nomor Referensi</label>
                    <input type="text" name="reference" value="{{ old('reference') }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Catatan</label>
                    <input type="text" name="note" value="{{ old('note') }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Catat Payout</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">Riwayat Payout</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode / Referensi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diproses Oleh</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($payouts as $payout)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $payout->paid_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            @if ($payout->period_start || $payout->period_end)
                                {{ optional($payout->period_start)->format('d/m/Y') }} - {{ optional($payout->period_end)->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $payout->method ?? '-' }}
                            @if ($payout->reference)
                                <div class="text-xs text-gray-400">Ref: {{ $payout->reference }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $payout->processedBy->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($payout->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.payouts.destroy', $payout) }}" onsubmit="return confirm('Hapus catatan payout ini? Saldo instruktur akan bertambah kembali.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat payout.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $payouts->links() }}
        </div>
    </div>
</div>
@endsection
