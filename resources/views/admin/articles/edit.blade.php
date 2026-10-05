@extends('layouts.app')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')
@section('page-subtitle', $article->title)

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.default.min.css" rel="stylesheet">
    @include('components.tom-select-dark-mode')
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="article-form">
            @csrf
            @method('PUT')

            @if ($article->featured_image)
                <div>
                    <img src="{{ Storage::url($article->featured_image) }}" class="w-full h-40 object-cover rounded-lg mb-2">
                </div>
            @endif

            <div>
                <x-label-tooltip tooltip="Judul akan tampil di halaman artikel, daftar artikel, dan tab browser. Usahakan jelas dan menarik.">Judul <span class="text-red-500">*</span></x-label-tooltip>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Bagian akhir alamat URL artikel, contoh: namadomain.com/artikel/slug-ini. Hanya huruf kecil, angka, dan tanda strip. Mengubah slug akan mengubah URL artikel yang sudah ada.">Slug URL</x-label-tooltip>
                <input type="text" name="slug" value="{{ old('slug', $article->slug) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <p class="text-xs text-gray-500 mt-1">{{ route('articles.show', $article->slug) }}</p>
                @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label-tooltip tooltip="Mengelompokkan artikel supaya pembaca mudah menemukan topik serupa. Opsional, boleh dikosongkan.">Kategori</x-label-tooltip>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Tanpa Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-label-tooltip tooltip="Kata kunci tambahan untuk artikel ini, membantu pencarian dan menampilkan artikel terkait. Boleh pilih lebih dari satu.">Tag</x-label-tooltip>
                    <select id="tags-select" multiple class="w-full">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $selectedTags)) ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <div id="tags-hidden-inputs"></div>
                </div>
            </div>

            <div>
                <x-label-tooltip tooltip="Teks singkat yang tampil di daftar artikel, dan dipakai sebagai deskripsi SEO kalau Meta Description di bawah tidak diisi.">Ringkasan</x-label-tooltip>
                <textarea name="excerpt" rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('excerpt', $article->excerpt) }}</textarea>
                @error('excerpt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Isi lengkap artikel. Gunakan toolbar editor untuk format teks seperti heading, bold, daftar, dan tautan.">Konten <span class="text-red-500">*</span></x-label-tooltip>
                <x-tinymce-editor name="content" :content="old('content', $article->content)" placeholder="Tulis isi artikel di sini..." :upload-url="route('admin.articles.upload-image')" />
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Gambar utama yang tampil di bagian atas artikel dan di daftar artikel. Ukuran yang disarankan 1200x630 piksel (rasio 16:9). Maksimal 2MB, format JPG/PNG. Kosongkan jika tidak ingin mengganti gambar yang sudah ada.">Ganti Gambar Unggulan</x-label-tooltip>
                <input type="file" name="featured_image" accept="image/*" class="w-full">
                <p class="text-xs text-gray-500 mt-1">Disarankan 1200x630 piksel. Maksimal 2MB. Kosongkan jika tidak ingin mengganti.</p>
                @error('featured_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label-tooltip tooltip="Draft tidak tampil ke publik. Published langsung tayang (atau sesuai Tanggal Publikasi kalau diisi). Archived disembunyikan dari daftar tapi datanya tetap tersimpan.">Status <span class="text-red-500">*</span></x-label-tooltip>
                    <select name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status', $article->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-label-tooltip tooltip="Kapan artikel mulai tampil ke publik. Boleh diisi tanggal di masa depan untuk jadwal otomatis. Kosongkan untuk memakai waktu saat ini ketika status Published.">Tanggal Publikasi</x-label-tooltip>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>

            <label class="flex items-center cursor-pointer w-fit gap-1.5">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Jadikan artikel unggulan (tampil di halaman utama)</span>
                <span class="group relative inline-flex">
                    <svg class="w-4 h-4 text-gray-400 hover:text-primary cursor-help flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.007v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block w-56 rounded-lg bg-gray-800 dark:bg-gray-700 px-3 py-2 text-xs leading-relaxed text-white shadow-lg z-20 normal-case">
                        Artikel unggulan diprioritaskan tampil di bagian "Artikel Pilihan" halaman utama. Maksimal 3 artikel yang ditampilkan.
                    </span>
                </span>
            </label>

            <!-- SEO -->
            <div class="pt-4 border-t dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">SEO & Share Sosial Media</h3>
                <div class="space-y-4">
                    <div>
                        <x-label-tooltip tooltip="Judul khusus yang muncul di hasil pencarian Google dan tab browser. Idealnya di bawah 60 karakter. Kosongkan untuk memakai judul artikel.">Meta Title</x-label-tooltip>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $article->meta_title) }}" maxlength="255"
                            placeholder="Kosongkan untuk memakai judul artikel"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        @error('meta_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-label-tooltip tooltip="Deskripsi singkat yang muncul di bawah judul pada hasil pencarian Google. Idealnya 120-160 karakter. Kosongkan untuk memakai ringkasan artikel.">Meta Description</x-label-tooltip>
                        <textarea name="meta_description" rows="2" maxlength="500"
                            placeholder="Kosongkan untuk memakai ringkasan artikel"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('meta_description', $article->meta_description) }}</textarea>
                        @error('meta_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-label-tooltip tooltip="Gambar yang tampil sebagai pratinjau saat artikel dibagikan ke WhatsApp, Facebook, atau Twitter. Kosongkan untuk memakai Gambar Unggulan.">Gambar Share (OG Image)</x-label-tooltip>
                        @if ($article->og_image)
                            <img src="{{ Storage::url($article->og_image) }}" class="w-32 h-20 object-cover rounded-lg mb-2">
                        @endif
                        <input type="file" name="og_image" accept="image/*" class="w-full">
                        <p class="text-xs text-gray-500 mt-1">Dipakai saat artikel dibagikan ke media sosial. Kosongkan untuk memakai gambar unggulan.</p>
                        @error('og_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
<script>
    function syncTagsHidden(values) {
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

    const tagsTomSelect = new TomSelect('#tags-select', {
        plugins: ['remove_button'],
        placeholder: 'Pilih tag...',
        onChange: syncTagsHidden
    });
    syncTagsHidden(tagsTomSelect.getValue());
</script>
@endpush
