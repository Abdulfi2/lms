@extends('layouts.app')

@section('title', 'Detail Failed Job')
@section('page-title', 'Detail Failed Job')
@section('page-subtitle', 'Job #' . $job->id)

@section('content')
<div x-data="failedJobShow()" class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('admin.failed-jobs.index') }}" class="text-primary hover:underline inline-flex items-center text-sm">
        &larr; Kembali ke Daftar Failed Jobs
    </a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><strong>ID</strong><br>{{ $job->id }}</div>
            <div><strong>Connection</strong><br>{{ $job->connection }}</div>
            <div><strong>Queue</strong><br>{{ $job->queue }}</div>
            <div><strong>Gagal Pada</strong><br>{{ \Carbon\Carbon::parse($job->failed_at)->format('d/m/Y H:i:s') }}</div>
        </div>

        @if (isset($payload['displayName']))
            <div class="text-sm"><strong>Job Class</strong><br>{{ $payload['displayName'] }}</div>
        @endif

        <div class="flex space-x-3 pt-2">
            <button @click="retryJob({{ $job->id }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                Retry Job Ini
            </button>
            <button @click="deleteJob({{ $job->id }})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                Hapus Job Ini
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-2">Payload</h3>
        <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto">{{ json_encode($payload, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-2 text-red-600">Exception</h3>
        <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto whitespace-pre-wrap">{{ $job->exception }}</pre>
    </div>
</div>

@push('scripts')
<script>
    function failedJobShow() {
        return {
            retryJob(id) {
                fetch(`/admin/failed-jobs/${id}/retry`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.toast.success(data.message);
                        setTimeout(() => location.href = '{{ route('admin.failed-jobs.index') }}', 1200);
                    } else {
                        window.toast.error(data.message);
                    }
                });
            },
            deleteJob(id) {
                if (confirm('Yakin ingin menghapus job ini?')) {
                    fetch(`/admin/failed-jobs/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        window.toast.success(data.message);
                        setTimeout(() => location.href = '{{ route('admin.failed-jobs.index') }}', 1200);
                    });
                }
            }
        }
    }
</script>
@endpush
@endsection
