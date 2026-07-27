@extends('layouts.app')

@section('title', 'Detail Pesan Kontak')
@section('page-title', 'Detail Pesan Kontak')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('admin.contact-messages.index') }}" class="text-sm text-primary hover:underline">&larr; Kembali ke inbox</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $contactMessage->subject }}</h3>
                <p class="text-sm text-gray-500">{{ $contactMessage->name }} &lt;{{ $contactMessage->email }}&gt;</p>
                <p class="text-xs text-gray-400 mt-1">{{ $contactMessage->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="px-2 py-1 text-xs rounded-full
                @if ($contactMessage->status === 'new') bg-blue-100 text-blue-800
                @elseif ($contactMessage->status === 'read') bg-yellow-100 text-yellow-800
                @elseif ($contactMessage->status === 'replied') bg-green-100 text-green-800
                @else bg-gray-200 text-gray-700 @endif">
                {{ ucfirst($contactMessage->status) }}
            </span>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">
            {{ $contactMessage->message }}
        </div>

        @if ($contactMessage->admin_reply)
            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-xs text-green-700 dark:text-green-400 font-medium mb-1">
                    Dibalas oleh {{ $contactMessage->repliedBy->name ?? '-' }} &middot; {{ optional($contactMessage->replied_at)->format('d/m/Y H:i') }}
                </p>
                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $contactMessage->admin_reply }}</p>
            </div>
        @endif

        @if ($contactMessage->status !== 'closed')
            <form method="POST" action="{{ route('admin.contact-messages.reply', $contactMessage) }}" class="mt-6">
                @csrf
                <label class="block text-sm font-medium mb-1">{{ $contactMessage->admin_reply ? 'Kirim Balasan Baru' : 'Balas Pesan' }}</label>
                <textarea name="admin_reply" rows="5" required maxlength="2000" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600"></textarea>
                @error('admin_reply') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex justify-end space-x-3 mt-3">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Kirim Balasan</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.contact-messages.close', $contactMessage) }}" class="mt-3 text-right">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">Tutup Tanpa Balasan</button>
            </form>
        @endif
    </div>
</div>
@endsection
