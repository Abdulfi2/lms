@extends('layouts.app')

@section('title', 'Payout Instruktur')
@section('page-title', 'Payout Instruktur')
@section('page-subtitle', 'Kelola pencairan pendapatan untuk setiap instruktur')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instruktur</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Pendapatan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sudah Dicairkan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Tertunda</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($instructors as $instructor)
                    @php $outstanding = $instructor->total_earnings - $instructor->total_paid_out; @endphp
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">{{ $instructor->name }}</div>
                            <div class="text-xs text-gray-500">{{ $instructor->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($instructor->total_earnings, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($instructor->total_paid_out, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold {{ $outstanding > 0 ? 'text-yellow-600' : 'text-gray-400' }}">Rp {{ number_format($outstanding, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.payouts.show', $instructor) }}" class="px-3 py-1.5 bg-primary text-white text-sm rounded-lg hover:bg-secondary">
                                Kelola
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada instruktur dengan transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $instructors->links() }}
</div>
@endsection
