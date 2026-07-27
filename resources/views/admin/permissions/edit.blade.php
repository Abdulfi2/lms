@extends('layouts.app')

@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission')
@section('page-subtitle', $permission->name)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Nama Permission <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $permission->name) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Modul</label>
                <input type="text" name="module" value="{{ old('module', $permission->module) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('module')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
