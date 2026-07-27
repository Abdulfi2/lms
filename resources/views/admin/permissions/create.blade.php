@extends('layouts.app')

@section('title', 'Tambah Permission')
@section('page-title', 'Tambah Permission Baru')
@section('page-subtitle', 'Buat permission baru untuk sistem')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.permissions.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Nama Permission <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: edit courses"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Modul</label>
                <input type="text" name="module" value="{{ old('module') }}" placeholder="Contoh: courses"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <p class="text-xs text-gray-500 mt-1">Dipakai untuk mengelompokkan permission di halaman ini dan di form role.</p>
                @error('module')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
