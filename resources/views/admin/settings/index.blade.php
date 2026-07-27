@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Konfigurasi umum situs')

@section('content')
<div class="space-y-6">
    <div class="flex gap-2 border-b dark:border-gray-700 overflow-x-auto">
        @foreach ($groups as $key => $label)
            <a href="{{ route('admin.settings.index', ['group' => $key]) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap {{ $activeGroup === $key ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($activeGroup === 'payment')
        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-sm text-yellow-800 dark:text-yellow-400">
            Halaman ini hanya menyimpan kredensial payment gateway untuk persiapan. Belum ada integrasi charging
            otomatis — konfirmasi pembayaran kursus saat ini masih dilakukan manual lewat menu
            <a href="{{ route('admin.enrollments.index') }}" class="underline font-semibold">Pembayaran</a>.
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="group" value="{{ $activeGroup }}">

            @forelse ($settings as $setting)
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $setting->label }}</label>

                    @if ($setting->type === 'textarea')
                        <textarea name="{{ $setting->key }}" rows="3"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old($setting->key, $setting->value) }}</textarea>
                    @elseif ($setting->type === 'boolean')
                        <label class="inline-flex items-center mt-1">
                            <input type="checkbox" name="{{ $setting->key }}" value="1"
                                {{ old($setting->key, $setting->value) ? 'checked' : '' }} class="rounded">
                            <span class="ml-2 text-sm text-gray-500">Aktif</span>
                        </label>
                    @else
                        <input type="text" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-sm">Tidak ada pengaturan di grup ini.</p>
            @endforelse

            @if ($settings->isNotEmpty())
                <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Pengaturan</button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
