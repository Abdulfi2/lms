@extends('layouts.app')

@section('title', 'Tulis Artikel Basis Pengetahuan')
@section('page-title', 'Tulis Artikel Basis Pengetahuan')
@section('page-subtitle', 'Bantu pengguna menemukan jawaban sendiri')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('support.knowledge-base.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-label-tooltip tooltip="Judul yang jelas membantu pengguna menemukan artikel ini lewat pencarian.">Judul <span class="text-red-500">*</span></x-label-tooltip>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Pengelompokan bebas untuk memudahkan pencarian, misalnya 'Akses Akun' atau 'Pembayaran'. Opsional.">Kategori</x-label-tooltip>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="Opsional"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Isi penjelasan lengkap untuk membantu pengguna menyelesaikan masalahnya sendiri.">Konten <span class="text-red-500">*</span></x-label-tooltip>
                <textarea name="content" rows="10" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('content') }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="text-sm text-gray-700 dark:text-gray-300">Terbitkan sekarang (tampil ke publik)</span>
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('support.knowledge-base.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
