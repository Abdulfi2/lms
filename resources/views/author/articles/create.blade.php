@extends('layouts.app')

@section('title', 'Tulis Artikel')
@section('page-title', 'Tulis Artikel')
@section('page-subtitle', 'Artikel akan tersimpan sebagai draft sampai ditinjau editor/admin')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.default.min.css" rel="stylesheet">
    @include('components.tom-select-dark-mode')
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('author.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="article-form">
            @csrf

            <div>
                <x-label-tooltip tooltip="Judul akan tampil di halaman artikel, daftar artikel, dan tab browser. Usahakan jelas dan menarik.">Judul <span class="text-red-500">*</span></x-label-tooltip>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Bagian akhir alamat URL artikel, contoh: namadomain.com/artikel/slug-ini. Hanya huruf kecil, angka, dan tanda strip. Kosongkan untuk dibuat otomatis dari judul.">Slug URL</x-label-tooltip>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="Kosongkan untuk dibuat otomatis dari judul"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label-tooltip tooltip="Mengelompokkan artikel supaya pembaca mudah menemukan topik serupa. Opsional, boleh dikosongkan.">Kategori</x-label-tooltip>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        <option value="">Tanpa Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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

            <div>
                <x-label-tooltip tooltip="Isi lengkap artikel. Gunakan toolbar editor untuk format teks seperti heading, bold, daftar, dan tautan.">Konten <span class="text-red-500">*</span></x-label-tooltip>
                <div id="quill-content" class="bg-white dark:bg-gray-900 rounded-b-lg" style="min-height: 250px;"></div>
                <textarea name="content" id="content-hidden" class="hidden" required>{{ old('content') }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Gambar utama yang tampil di bagian atas artikel dan di daftar artikel. Maksimal 2MB, format JPG/PNG.">Gambar Unggulan</x-label-tooltip>
                <input type="file" name="featured_image" accept="image/*" class="w-full">
                <p class="text-xs text-gray-500 mt-1">Maksimal 2MB.</p>
                @error('featured_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <x-label-tooltip tooltip="Draft tersimpan untuk ditinjau admin/editor. Archived disembunyikan dari daftar. Anda tidak bisa langsung Publish — itu wewenang admin/editor setelah artikel ditinjau.">Status <span class="text-red-500">*</span></x-label-tooltip>
                <select name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Publish hanya bisa dilakukan oleh admin/editor setelah ditinjau.</p>
                @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- SEO -->
            <div class="pt-4 border-t dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">SEO & Share Sosial Media</h3>
                <div class="space-y-4">
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

            <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                <a href="{{ route('author.articles.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">Simpan Artikel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
<script>
    const contentQuill = new Quill('#quill-content', {
        theme: 'snow',
        placeholder: 'Tulis isi artikel di sini...',
        modules: {
            toolbar: [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'link'],
                ['clean']
            ]
        }
    });
    const contentHidden = document.getElementById('content-hidden');
    if (contentHidden.value) {
        contentQuill.root.innerHTML = contentHidden.value;
    }

    new TomSelect('#tags-select', {
        plugins: ['remove_button'],
        placeholder: 'Pilih tag...',
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

    document.getElementById('article-form').addEventListener('submit', function () {
        contentHidden.value = contentQuill.root.innerHTML;
    });
</script>
@endpush
