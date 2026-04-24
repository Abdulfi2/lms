@extends('layouts.app')

@section('title', 'Failed Jobs')
@section('page-title', 'Failed Jobs')
@section('page-subtitle', 'Kelola job yang gagal dieksekusi')

@section('content')
    <div x-data="failedJobsManager()" x-init="init()" class="space-y-6">
        <!-- Header Actions -->
        <div class="flex justify-between items-center">
            <div class="flex space-x-3">
                <button @click="retryAll" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Retry All
                </button>
                <button @click="deleteAll" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Delete All
                </button>
            </div>
            <div class="text-sm text-gray-500">
                Total Failed Jobs: <span class="font-semibold">{{ $failedJobs->total() }}</span>
            </div>
        </div>

        <!-- Failed Jobs Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Connection</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Failed At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exception</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($failedJobs as $job)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $job->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $job->connection }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $job->queue }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-sm text-red-600 truncate max-w-md">
                                    {{ Str::limit($job->exception, 100) }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.failed-jobs.show', $job->id) }}"
                                        class="text-blue-600 hover:text-blue-800">Detail</a>
                                    <button @click="retryJob({{ $job->id }})"
                                        class="text-green-600 hover:text-green-800">Retry</button>
                                    <button @click="deleteJob({{ $job->id }})"
                                        class="text-red-600 hover:text-red-800">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p>Tidak ada failed jobs.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $failedJobs->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function failedJobsManager() {
                return {
                    retryJob(id) {
                        fetch(`/admin/failed-jobs/${id}/retry`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success('Job berhasil di-retry');
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    window.toast.error(data.message);
                                }
                            });
                    },
                    retryAll() {
                        if (confirm('Yakin ingin meretry semua failed jobs?')) {
                            fetch(`/admin/failed-jobs/retry-all`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    window.toast.success(data.message);
                                    setTimeout(() => location.reload(), 1500);
                                });
                        }
                    },
                    deleteJob(id) {
                        if (confirm('Yakin ingin menghapus job ini?')) {
                            fetch(`/admin/failed-jobs/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    window.toast.success(data.message);
                                    setTimeout(() => location.reload(), 1500);
                                });
                        }
                    },
                    deleteAll() {
                        if (confirm('Yakin ingin menghapus semua failed jobs?')) {
                            fetch(`/admin/failed-jobs`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    window.toast.success(data.message);
                                    setTimeout(() => location.reload(), 1500);
                                });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
