@extends('layouts.app')

@section('title', 'Tulis Artikel')
@section('page-title', 'Tulis Artikel')
@section('page-subtitle', 'Tulis artikel baru — Anda bisa langsung mempublikasikannya')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.default.min.css" rel="stylesheet">
    @include('components.tom-select-dark-mode')
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <form action="{{ route('editor.articles.store') }}" method="POST" enctype="multipart/form-data" id="article-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Kolom Utama -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Informasi Artikel</h3>

                    <div>
                        <x-label-tooltip tooltip="Judul akan tampil di halaman artikel, daftar artikel, dan tab browser. Usahakan jelas dan menarik.">Judul <span class="text-red-500">*</span></x-label-tooltip>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <x-label-tooltip tooltip="Kalimat pendek pelengkap judul, tampil di bawah judul utama. Opsional.">Subjudul</x-label-tooltip>
                        <input type="text" name="subtitle" value="{{ old('subtitle') }}" maxlength="150" placeholder="Opsional"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        @error('subtitle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <x-label-tooltip tooltip="Bagian akhir alamat URL artikel, contoh: namadomain.com/artikel/slug-ini. Hanya huruf kecil, angka, dan tanda strip. Kosongkan untuk dibuat otomatis dari judul.">Slug URL</x-label-tooltip>
                        <input type="text" name="slug" value="{{ old('slug') }}" placeholder="Kosongkan untuk dibuat otomatis dari judul"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label-tooltip tooltip="Mengelompokkan artikel supaya pembaca mudah menemukan topik serupa. Opsional, boleh dikosongkan. Belum ada kategorinya? Klik + Baru untuk membuat sendiri.">Kategori</x-label-tooltip>
                            <div class="flex gap-2">
                                <select name="category_id" id="category-select" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                                    <option value="">Tanpa Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="document.getElementById('new-category-row').classList.toggle('hidden')"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-primary hover:bg-primary/5 whitespace-nowrap">
                                    + Baru
                                </button>
                            </div>
                            <div id="new-category-row" class="hidden mt-2 flex gap-2">
                                <input type="text" id="new-category-name" placeholder="Nama kategori baru"
                                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-primary focus:border-primary">
                                <button type="button" onclick="createCategory()" class="px-3 py-2 bg-primary text-white rounded-lg text-sm hover:bg-secondary transition">Simpan</button>
                            </div>
                            @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <x-label-tooltip tooltip="Kata kunci tambahan untuk artikel ini, membantu pencarian dan menampilkan artikel terkait. Ketik lalu Enter untuk membuat tag baru kalau belum ada.">Tag</x-label-tooltip>
                            <select id="tags-select" multiple class="w-full">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            <div id="tags-hidden-inputs"></div>
                        </div>
                    </div>

                    <div>
                        <x-label-tooltip tooltip="Teks singkat yang tampil di daftar artikel, dan dipakai sebagai deskripsi SEO kalau Meta Description di bawah tidak diisi.">Ringkasan</x-label-tooltip>
                        <textarea name="excerpt" rows="2" placeholder="Ringkasan singkat yang tampil di daftar artikel"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('excerpt') }}</textarea>
                        @error('excerpt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Konten Artikel</h3>
                    <x-label-tooltip tooltip="Isi lengkap artikel. Gunakan toolbar editor untuk format teks seperti heading, bold, daftar, tabel, dan tautan.">Konten <span class="text-red-500">*</span></x-label-tooltip>
                    <x-tinymce-editor name="content" :content="old('content')" placeholder="Tulis isi artikel di sini..." :upload-url="route('editor.articles.upload-image')" />
                    @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- SEO -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">SEO & Share Sosial Media</h3>
                    <div>
                        <x-label-tooltip tooltip="Judul khusus yang muncul di hasil pencarian Google dan tab browser. Idealnya di bawah 60 karakter. Kosongkan untuk memakai judul artikel.">Meta Title</x-label-tooltip>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="255"
                            placeholder="Kosongkan untuk memakai judul artikel"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        @error('meta_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-label-tooltip tooltip="Deskripsi singkat yang muncul di bawah judul pada hasil pencarian Google. Idealnya 120-160 karakter. Kosongkan untuk memakai ringkasan artikel.">Meta Description</x-label-tooltip>
                        <textarea name="meta_description" rows="2" maxlength="500"
                            placeholder="Kosongkan untuk memakai ringkasan artikel"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('meta_description') }}</textarea>
                        @error('meta_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-label-tooltip tooltip="Gambar yang tampil sebagai pratinjau saat artikel dibagikan ke WhatsApp, Facebook, atau Twitter. Kosongkan untuk memakai Gambar Unggulan.">Gambar Share (OG Image)</x-label-tooltip>
                        <input type="file" name="og_image" accept="image/*" class="w-full">
                        <p class="text-xs text-gray-500 mt-1">Dipakai saat artikel dibagikan ke media sosial. Kosongkan untuk memakai gambar unggulan.</p>
                        @error('og_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-1">Gambar Unggulan</h3>
                    <p class="text-xs text-gray-500 mb-3">Gambar utama yang tampil di bagian atas artikel dan di daftar artikel.</p>
                    <label for="featured_image_input"
                        class="flex flex-col items-center justify-center gap-1 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg py-8 px-4 cursor-pointer hover:border-primary transition text-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-300">Klik untuk upload gambar</span>
                        <span id="featured-image-filename" class="text-xs text-gray-400">Belum ada file dipilih</span>
                    </label>
                    <input id="featured_image_input" type="file" name="featured_image" accept="image/*" class="hidden"
                        onchange="document.getElementById('featured-image-filename').textContent = this.files[0] ? this.files[0].name : 'Belum ada file dipilih'">
                    <p class="text-xs text-gray-500 mt-2">Format JPG/PNG. Disarankan 1200x630 piksel. Maksimal 2MB.</p>
                    @error('featured_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-1">Status & Publikasi</h3>
                    <p class="text-xs text-gray-500 mb-3">Sebagai editor, Anda bisa langsung mempublikasikan artikel sendiri.</p>
                    <x-label-tooltip tooltip="Draft tersimpan tanpa tampil publik. Published langsung tampil di halaman Blog & Artikel. Archived disembunyikan dari daftar.">Status <span class="text-red-500">*</span></x-label-tooltip>
                    <select name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 space-y-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Pengaturan Tambahan</h3>

                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <span>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Izinkan komentar</span>
                            <span class="block text-xs text-gray-500">Pembaca dapat memberikan komentar</span>
                        </span>
                        <span class="relative inline-flex items-center flex-shrink-0">
                            <input type="hidden" name="allow_comments" value="0">
                            <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', true) ? 'checked' : '' }} class="sr-only peer">
                            <span class="w-10 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer-checked:bg-primary transition-colors"></span>
                            <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-4"></span>
                        </span>
                    </label>

                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <span>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tampilkan di halaman utama</span>
                            <span class="block text-xs text-gray-500">Jadikan artikel unggulan begitu dipublikasikan</span>
                        </span>
                        <span class="relative inline-flex items-center flex-shrink-0">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="sr-only peer">
                            <span class="w-10 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer-checked:bg-primary transition-colors"></span>
                            <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-4"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('editor.articles.index', ['tab' => 'mine']) }}" class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Batal</a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">Simpan Artikel</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect('#tags-select', {
        plugins: ['remove_button'],
        placeholder: 'Pilih atau ketik tag baru...',
        create: true,
        createOnBlur: true,
        onChange: function (values) {
            const container = document.getElementById('tags-hidden-inputs');
            container.innerHTML = '';
            values.forEach(value => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tags[]';
                input.value = value;
                container.appendChild(input);
            });
        }
    });

    function createCategory() {
        const nameInput = document.getElementById('new-category-name');
        const name = nameInput.value.trim();
        if (!name) return;

        fetch('{{ route('editor.article-categories.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name: name }),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    const select = document.getElementById('category-select');
                    const option = document.createElement('option');
                    option.value = result.data.category.id;
                    option.textContent = result.data.category.name;
                    option.selected = true;
                    select.appendChild(option);

                    nameInput.value = '';
                    document.getElementById('new-category-row').classList.add('hidden');

                    if (window.toast) window.toast.success('Kategori "' + result.data.category.name + '" ditambahkan.');
                } else {
                    const message = (result.data.errors && result.data.errors.name) ? result.data.errors.name[0] : 'Gagal menambah kategori.';
                    if (window.toast) window.toast.error(message);
                }
            })
            .catch(function () {
                if (window.toast) window.toast.error('Gagal menambah kategori. Silakan coba lagi.');
            });
    }
</script>
@endpush
