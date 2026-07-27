@extends('layouts.app')

@section('title', 'Pesan Kontak')
@section('page-title', 'Pesan Kontak')
@section('page-subtitle', 'Inbox pesan dari form kontak publik')

@section('content')
<div class="space-y-6">
    <div class="flex space-x-2">
        <a href="{{ route('admin.contact-messages.index', ['status' => 'new']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ $status === 'new' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Baru
        </a>
        <a href="{{ route('admin.contact-messages.index', ['status' => 'read']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ $status === 'read' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Dibaca
        </a>
        <a href="{{ route('admin.contact-messages.index', ['status' => 'replied']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ $status === 'replied' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Dibalas
        </a>
        <a href="{{ route('admin.contact-messages.index', ['status' => 'closed']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ $status === 'closed' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Ditutup
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengirim</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subjek</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diterima</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($messages as $message)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">{{ $message->name }}</div>
                            <div class="text-xs text-gray-500">{{ $message->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($message->subject, 50) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.contact-messages.show', $message) }}" class="px-3 py-1.5 bg-primary text-white text-sm rounded-lg hover:bg-secondary">
                                Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada pesan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $messages->links() }}
</div>
@endsection
