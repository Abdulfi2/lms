@extends('layouts.app')

@section('title', 'Profil Publik')
@section('page-title', 'Profil Publik Instruktur')
@section('page-subtitle', 'Bio ini akan ditampilkan kepada calon siswa sebelum mereka mendaftar kursus Anda')

@section('content')
@php
    $professional = $profile->professional_info ?? [];
    $social = $profile->social_media ?? [];
@endphp
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6" x-data="{ expertise: {{ json_encode($professional['expertise'] ?? ['']) }} }">
    <form method="POST" action="{{ route('instructor.public-profile.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Headline</label>
            <input type="text" name="headline" value="{{ old('headline', $professional['headline'] ?? '') }}"
                placeholder="Contoh: Senior Web Developer & Instruktur Laravel" maxlength="255" class="w-full rounded-lg border-gray-300">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Bio</label>
            <textarea name="bio" rows="5" maxlength="2000" class="w-full rounded-lg border-gray-300">{{ old('bio', $professional['bio'] ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Keahlian</label>
            <template x-for="(item, idx) in expertise" :key="idx">
                <div class="flex mb-2">
                    <input type="text" :name="`expertise[${idx}]`" x-model="expertise[idx]" class="flex-1 rounded-lg border-gray-300">
                    <button type="button" @click="expertise.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                </div>
            </template>
            <button type="button" @click="expertise.push('')" class="text-primary text-sm">+ Tambah Keahlian</button>
        </div>

        <div class="pt-2 border-t dark:border-gray-700">
            <h4 class="text-sm font-semibold mb-3">Tautan Sosial</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Website</label>
                    <input type="url" name="website" value="{{ old('website', $social['website'] ?? '') }}" placeholder="https://" class="w-full rounded-lg border-gray-300">
                    @error('website') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">LinkedIn</label>
                    <input type="url" name="linkedin" value="{{ old('linkedin', $social['linkedin'] ?? '') }}" placeholder="https://" class="w-full rounded-lg border-gray-300">
                    @error('linkedin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Twitter / X</label>
                    <input type="url" name="twitter" value="{{ old('twitter', $social['twitter'] ?? '') }}" placeholder="https://" class="w-full rounded-lg border-gray-300">
                    @error('twitter') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Instagram</label>
                    <input type="url" name="instagram" value="{{ old('instagram', $social['instagram'] ?? '') }}" placeholder="https://" class="w-full rounded-lg border-gray-300">
                    @error('instagram') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">YouTube</label>
                    <input type="url" name="youtube" value="{{ old('youtube', $social['youtube'] ?? '') }}" placeholder="https://" class="w-full rounded-lg border-gray-300">
                    @error('youtube') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <label class="flex items-center pt-2">
            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $profile->is_public) ? 'checked' : '' }} class="rounded">
            <span class="ml-2 text-sm">Tampilkan profil ini secara publik kepada calon siswa</span>
        </label>
        @if ($profile->is_public)
            <p class="text-xs text-gray-500">Profil Anda dapat dilihat di: <a href="{{ route('instructors.show', auth()->user()) }}" target="_blank" class="text-primary hover:underline">{{ route('instructors.show', auth()->user()) }}</a></p>
        @endif

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Profil</button>
        </div>
    </form>
</div>
@endsection
