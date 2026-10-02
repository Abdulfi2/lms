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

    @if ($activeGroup === 'email')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-white">Koneksi Google Workspace (Gmail API)</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Email sistem (verifikasi akun, reset password, notifikasi) dikirim lewat akun Google yang terhubung di sini.
                    </p>

                    @if ($googleAuthorized)
                        <div class="mt-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Terhubung sebagai <strong>{{ $googleEmail }}</strong></span>
                        </div>
                    @else
                        <div class="mt-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                            <span class="text-sm text-gray-500">Belum terhubung ke akun Google manapun</span>
                        </div>
                    @endif

                    @unless ($googleMailerActive)
                        <p class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                            Catatan: <code>MAIL_MAILER</code> di <code>.env</code> belum diset ke <code>gmail_api</code>, jadi email sistem masih dikirim lewat mailer lain meskipun sudah terhubung di sini.
                        </p>
                    @endunless
                </div>

                <div class="flex gap-2 flex-shrink-0">
                    @if ($googleAuthorized)
                        <form action="{{ route('admin.settings.google.disconnect') }}" method="POST"
                            onsubmit="return confirm('Putuskan koneksi akun Google? Email sistem tidak akan terkirim sampai dihubungkan ulang.')">
                            @csrf
                            <button type="submit" class="px-4 py-2 border border-red-300 dark:border-red-800 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Putuskan
                            </button>
                        </form>
                        <a href="{{ route('auth.google.redirect') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Ganti Akun
                        </a>
                    @else
                        <a href="{{ route('auth.google.redirect') }}" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-secondary transition">
                            Hubungkan Google
                        </a>
                    @endif
                </div>
            </div>
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
