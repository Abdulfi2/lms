@extends('layouts.app')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')
@section('page-subtitle', $event->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            @if ($event->image)
                <div>
                    <img src="{{ Storage::url($event->image) }}" class="w-full h-40 object-cover rounded-lg mb-2">
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium mb-1">Judul Event <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('description', $event->description) }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        @foreach (['webinar', 'workshop', 'parenting', 'live_class', 'zoom_meeting', 'seminar'] as $type)
                            <option value="{{ $type }}" {{ old('type', $event->type) == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        @foreach (['education', 'parenting', 'technology', 'business', 'health', 'other'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $event->category) == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Pembicara</label>
                    <input type="text" name="speaker" value="{{ old('speaker', $event->speaker) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $event->location) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Bio Pembicara</label>
                <textarea name="speaker_bio" rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('speaker_bio', $event->speaker_bio) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_time"
                        value="{{ old('start_time', optional($event->start_time)->format('Y-m-d\TH:i')) }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('start_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Selesai</label>
                    <input type="datetime-local" name="end_time"
                        value="{{ old('end_time', optional($event->end_time)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('end_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Link Zoom</label>
                    <input type="url" name="zoom_link" value="{{ old('zoom_link', $event->zoom_link) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Meeting ID</label>
                    <input type="text" name="meeting_id" value="{{ old('meeting_id', $event->meeting_id) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Passcode</label>
                    <input type="text" name="passcode" value="{{ old('passcode', $event->passcode) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Maks. Peserta</label>
                    <input type="number" name="max_participants" min="1" value="{{ old('max_participants', $event->max_participants) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipe Harga <span class="text-red-500">*</span></label>
                    <select name="price_type" required x-data x-on:change="$refs.priceField.classList.toggle('hidden', $event.target.value === 'free')"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="free" {{ old('price_type', $event->price_type) == 'free' ? 'selected' : '' }}>Gratis</option>
                        <option value="paid" {{ old('price_type', $event->price_type) == 'paid' ? 'selected' : '' }}>Berbayar</option>
                    </select>
                </div>
                <div x-ref="priceField" class="{{ old('price_type', $event->price_type) === 'free' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <input type="number" name="price" min="0" value="{{ old('price', $event->price) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Ganti Gambar Event</label>
                <input type="file" name="image" accept="image/*" class="w-full">
                @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-center mt-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }} class="rounded">
                        <span class="ml-2 text-sm">Jadikan event unggulan</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.events.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
