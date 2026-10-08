@extends('layouts.app')

@section('title', 'Buat Tiket Bantuan')
@section('page-title', 'Buat Tiket Bantuan')
@section('page-subtitle', 'Jelaskan kendala Anda, tim support akan segera membantu')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('tickets.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-label-tooltip tooltip="Ringkasan singkat masalah Anda, misalnya 'Tidak bisa mengakses materi kursus'.">Judul <span class="text-red-500">*</span></x-label-tooltip>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Pilih topik yang paling sesuai supaya tiket Anda lebih cepat ditangani. Opsional.">Kategori</x-label-tooltip>
                <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                    <option value="">Pilih kategori (opsional)</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Seberapa mendesak masalah ini bagi Anda. Tiket dengan prioritas tinggi akan diprioritaskan tim support.">Prioritas <span class="text-red-500">*</span></x-label-tooltip>
                <select name="priority" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                    <option value="rendah" {{ old('priority') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="sedang" {{ old('priority', 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="tinggi" {{ old('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                </select>
                @error('priority')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Jelaskan masalah Anda sedetail mungkin — kapan terjadi, apa yang sudah dicoba, pesan error jika ada.">Deskripsi <span class="text-red-500">*</span></x-label-tooltip>
                <textarea name="description" rows="6" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('tickets.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">Kirim Tiket</button>
            </div>
        </form>
    </div>
</div>
@endsection
