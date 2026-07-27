@extends('layouts.app')

@section('title', 'Detail Activity Log')
@section('page-title', 'Detail Activity Log')
@section('page-subtitle', $log->action)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('admin.activity-logs.index') }}" class="text-primary hover:underline inline-flex items-center text-sm">
        &larr; Kembali ke Activity Log
    </a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><strong>Waktu</strong><br>{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
            <div><strong>User</strong><br>{{ $log->user->name ?? 'Sistem' }}</div>
            <div><strong>Aksi</strong><br>{{ $log->action }}</div>
            <div><strong>IP Address</strong><br>{{ $log->ip_address ?? '-' }}</div>
        </div>

        @if ($log->description)
            <div><strong class="text-sm">Deskripsi</strong><p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $log->description }}</p></div>
        @endif

        @if ($log->table_name)
            <div class="text-sm"><strong>Tabel/Record</strong><br>{{ $log->table_name }} #{{ $log->record_id }}</div>
        @endif
    </div>

    @if ($log->old_data)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-2">Data Sebelumnya</h3>
            <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    @if ($log->new_data)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-2">Data Baru</h3>
            <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto">{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif
</div>
@endsection
