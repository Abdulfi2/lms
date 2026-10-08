@extends('layouts.app')

@section('title', 'Jadwal Support')
@section('page-title', 'Jadwal Support')
@section('page-subtitle', 'Kelola jadwal shift, maintenance, meeting, dan kegiatan tim')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <x-support-calendar-grid :days="$days" :current="$current" :start-offset="$startOffset" />
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Tambah Jadwal</h3>
            <form method="POST" action="{{ route('support.schedule.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Judul</label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal</label>
                    <input type="date" name="date" required value="{{ old('date') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary">
                    @error('date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jenis</label>
                    <select name="type" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary">
                        <option value="jadwal_support">Jadwal Support</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="meeting">Meeting</option>
                        <option value="kegiatan">Kegiatan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">Simpan Jadwal</button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Jadwal Bulan Ini</h3>
            </div>
            <div class="divide-y dark:divide-gray-700 max-h-96 overflow-y-auto">
                @php($allItems = collect($days)->flatMap(fn ($d) => $d['items']))
                @forelse ($allItems as $item)
                    <div class="flex items-center justify-between px-5 py-3 gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $item->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->date->format('d M Y') }} &middot; {{ $item->typeLabel() }}</p>
                        </div>
                        <form method="POST" action="{{ route('support.schedule.destroy', $item) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs flex-shrink-0">Hapus</button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-sm text-gray-400">Belum ada jadwal bulan ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
