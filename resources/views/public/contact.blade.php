@extends('layouts.client')

@section('title', 'Kontak Kami')
@section('page-title', 'Kontak Kami')
@section('page-subtitle', 'Ada pertanyaan atau kendala? Kirimkan pesan Anda kepada kami.')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required maxlength="255"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required maxlength="255"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Subjek <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Pesan <span class="text-red-500">*</span></label>
                <textarea name="message" rows="6" required maxlength="2000"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    Kirim Pesan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
