@extends('layouts.app')

@section('title', 'Manajemen Sertifikat')
@section('page-title', 'Sertifikat')
@section('page-subtitle', 'Semua sertifikat yang sudah diterbitkan ke siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor sertifikat, siswa, atau kursus..."
                class="w-72 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Semua Status</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Filter</button>
            @if (request()->filled('search') || request()->filled('status'))
                <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">No. Sertifikat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kursus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Terbit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($certificates as $certificate)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">{{ $certificate->certificate_number }}</td>
                            <td class="px-6 py-4 text-sm">{{ $certificate->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">{{ Str::limit($certificate->course->title ?? '-', 35) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ optional($certificate->issued_at)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $certificate->is_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $certificate->is_verified ? 'Verified' : 'Revoked' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.certificates.show', $certificate) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada sertifikat yang terbit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $certificates->links() }}
        </div>
    </div>
</div>
@endsection
