@extends('layouts.app')

@section('title', 'Peserta - ' . $event->title)
@section('page-title', 'Peserta Event')
@section('page-subtitle', $event->title)

@section('content')
<div x-data="registrationManager()" class="space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.events.show', $event) }}" class="text-primary hover:underline inline-flex items-center text-sm">
            &larr; Kembali ke Detail Event
        </a>
        <a href="{{ route('admin.events.export', $event) }}" class="px-4 py-2 border rounded-lg text-sm dark:border-gray-600">
            Export CSV
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Total</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Pending</p>
            <p class="text-xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Confirmed</p>
            <p class="text-xl font-bold text-green-600">{{ $stats['confirmed'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Hadir</p>
            <p class="text-xl font-bold text-blue-600">{{ $stats['attended'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Cancelled</p>
            <p class="text-xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 flex items-center gap-3" x-show="selected.length > 0" x-cloak>
        <span class="text-sm" x-text="selected.length + ' dipilih'"></span>
        <select x-model="bulkStatus" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="confirmed">Confirmed</option>
            <option value="pending">Pending</option>
            <option value="attended">Attended</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <button @click="bulkUpdate" class="px-3 py-1.5 bg-primary text-white rounded-lg text-sm">Terapkan</button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3"><input type="checkbox" @change="toggleAll($event)"></th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Kontak</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Tanggal Daftar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($registrations as $reg)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3"><input type="checkbox" value="{{ $reg->id }}" x-model="selected"></td>
                            <td class="px-4 py-3 text-sm">{{ $reg->name }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $reg->email }}<br>{{ $reg->phone }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $reg->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <select onchange="updateStatus({{ $reg->id }}, this.value)"
                                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-xs">
                                    @foreach (['pending', 'confirmed', 'attended', 'cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $reg->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">Belum ada pendaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $registrations->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function registrationManager() {
        return {
            selected: [],
            bulkStatus: 'confirmed',
            toggleAll(event) {
                this.selected = event.target.checked
                    ? Array.from(document.querySelectorAll('tbody input[type=checkbox]')).map(cb => cb.value)
                    : [];
            },
            bulkUpdate() {
                fetch('{{ route('admin.events.registrations.bulk-update', $event) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ registration_ids: this.selected, status: this.bulkStatus })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.toast.success(data.message);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        window.toast.error(data.message || 'Gagal memperbarui.');
                    }
                })
                .catch(() => window.toast.error('Terjadi kesalahan'));
            }
        }
    }

    function updateStatus(id, status) {
        fetch(`/admin/events/registrations/${id}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.toast.success(data.message);
            } else {
                window.toast.error('Gagal memperbarui status');
            }
        })
        .catch(() => window.toast.error('Terjadi kesalahan'));
    }
</script>
@endpush
@endsection
