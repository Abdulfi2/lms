@php $coupon = $coupon ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Kode Kupon <span class="text-red-500">*</span></label>
        <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required maxlength="50"
            class="w-full rounded-lg border-gray-300 uppercase" placeholder="DISKON20">
        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Kursus (kosongkan untuk semua kursus)</label>
        <select name="course_id" class="w-full rounded-lg border-gray-300">
            <option value="">Semua Kursus</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" {{ (string) old('course_id', $coupon->course_id ?? '') === (string) $course->id ? 'selected' : '' }}>
                    {{ $course->title }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Tipe Diskon <span class="text-red-500">*</span></label>
        <select name="type" required class="w-full rounded-lg border-gray-300">
            <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
            <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
        </select>
        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nilai Diskon <span class="text-red-500">*</span></label>
        <input type="number" name="value" value="{{ old('value', $coupon->value ?? '') }}" step="0.01" min="0.01" required
            class="w-full rounded-lg border-gray-300">
        @error('value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Minimal Pembelian (Rp)</label>
        <input type="number" name="min_purchase" value="{{ old('min_purchase', $coupon->min_purchase ?? '') }}" step="1000" min="0"
            class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Batas Penggunaan</label>
        <input type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses ?? '') }}" min="1"
            placeholder="Tanpa batas" class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Mulai Berlaku</label>
        <input type="datetime-local" name="starts_at"
            value="{{ old('starts_at', isset($coupon->starts_at) ? $coupon->starts_at?->format('Y-m-d\TH:i') : '') }}"
            class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Berakhir</label>
        <input type="datetime-local" name="expires_at"
            value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at?->format('Y-m-d\TH:i') : '') }}"
            class="w-full rounded-lg border-gray-300">
        @error('expires_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<label class="flex items-center mt-2">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }} class="rounded">
    <span class="ml-2 text-sm">Aktif</span>
</label>
