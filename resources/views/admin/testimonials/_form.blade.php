@php($testimonial = $testimonial ?? null)

<div>
    <label class="block text-sm font-medium mb-1">Nama <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $testimonial->name ?? '') }}" required
        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium mb-1">Jabatan / Profesi</label>
    <input type="text" name="role" value="{{ old('role', $testimonial->role ?? '') }}" placeholder="mis. Fullstack Developer"
        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium mb-1">Isi Testimoni <span class="text-red-500">*</span></label>
    <textarea name="content" rows="4" required maxlength="1000"
        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('content', $testimonial->content ?? '') }}</textarea>
    @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Rating</label>
        <select name="rating" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
            @endfor
        </select>
        @error('rating')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Urutan</label>
        <input type="number" name="order" value="{{ old('order', $testimonial->order ?? 0) }}" min="0"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
        @error('order')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }} class="rounded">
        <span class="ml-2 text-sm">Aktif (tampil di halaman beranda)</span>
    </label>
</div>
