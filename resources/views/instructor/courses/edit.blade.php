@extends('layouts.app')

@section('title', 'Edit Kursus')
@section('page-title', 'Edit Kursus')

@section('content')
    <div x-data="courseForm()" x-init="init()"
        class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form @submit.prevent="submitForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Informasi Dasar -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Judul Kursus <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="form.title" required class="w-full rounded-lg border-gray-300">
                            <p x-show="errors.title" class="text-red-500 text-xs mt-1" x-text="errors.title"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Slug (biarkan kosong)</label>
                            <input type="text" x-model="form.slug" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Short Description</label>
                            <textarea x-model="form.short_description" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Deskripsi Lengkap <span
                                    class="text-red-500">*</span></label>
                            <textarea x-model="form.description" rows="5" class="w-full rounded-lg border-gray-300"></textarea>
                            <p x-show="errors.description" class="text-red-500 text-xs mt-1" x-text="errors.description">
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div>
                    <label class="block text-sm font-medium mb-1">Thumbnail</label>
                    <input type="file" @change="handleThumbnail" accept="image/*" class="w-full">
                    <template x-if="thumbnailPreview">
                        <img :src="thumbnailPreview" class="mt-2 h-20 rounded object-cover">
                    </template>
                </div>

                <!-- Harga & Diskon -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Harga & Diskon</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Harga (Rp) <span
                                    class="text-red-500">*</span></label>
                            <input type="number" x-model="form.price" step="1000"
                                class="w-full rounded-lg border-gray-300">
                            <p x-show="errors.price" class="text-red-500 text-xs mt-1" x-text="errors.price"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Harga Diskon</label>
                            <input type="number" x-model="form.sale_price" step="1000"
                                class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Periode Diskon Mulai</label>
                            <input type="datetime-local" x-model="form.sale_starts_at"
                                class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Periode Diskon Berakhir</label>
                            <input type="datetime-local" x-model="form.sale_ends_at"
                                class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                </div>

                <!-- Level & Status -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Level & Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Level</label>
                            <select x-model="form.level" class="w-full rounded-lg border-gray-300">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="all_levels">All Levels</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Bahasa</label>
                            <input type="text" x-model="form.language" placeholder="id"
                                class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Durasi (jam)</label>
                            <input type="number" x-model="form.duration_total" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select x-model="form.status" class="w-full rounded-lg border-gray-300">
                                <option value="draft">Draft</option>
                                <option value="pending">Ajukan untuk Review Admin</option>
                                <option value="published">Published (langsung tayang)</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                    @if ($course->status === 'draft' && $course->rejection_reason)
                        <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm text-red-700 dark:text-red-400">
                            <strong>Ditolak admin:</strong> {{ $course->rejection_reason }}
                        </div>
                    @elseif ($course->status === 'pending')
                        <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-sm text-yellow-700 dark:text-yellow-400">
                            Kursus ini sedang menunggu review admin.
                        </div>
                    @endif
                    <div class="flex items-center space-x-4 mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="form.is_featured" class="rounded">
                            <span class="ml-2">Featured</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="form.has_certificate" class="rounded">
                            <span class="ml-2">Sertifikat</span>
                        </label>
                    </div>
                </div>

                <!-- Landing Page Kursus -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Landing Page Kursus</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Yang Akan Dipelajari (Learning Objectives)</label>
                            <div x-data="{ items: form.learning_objectives }">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2">
                                        <input type="text" x-model="items[idx]" class="flex-1 rounded-lg border-gray-300">
                                        <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                    </div>
                                </template>
                                <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah Poin</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Persyaratan (Requirements)</label>
                            <div x-data="{ items: form.requirements }">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2">
                                        <input type="text" x-model="items[idx]" class="flex-1 rounded-lg border-gray-300">
                                        <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                    </div>
                                </template>
                                <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah Persyaratan</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Target Audience</label>
                            <div x-data="{ items: form.target_audience }">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2">
                                        <input type="text" x-model="items[idx]" class="flex-1 rounded-lg border-gray-300">
                                        <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                    </div>
                                </template>
                                <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah Target</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Prasyarat Kursus (Prerequisites)</label>
                            <div x-data="{ items: form.prerequisites }">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2">
                                        <input type="text" x-model="items[idx]" class="flex-1 rounded-lg border-gray-300">
                                        <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                    </div>
                                </template>
                                <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah Prasyarat</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kategori & Tag -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kategori & Tag</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select x-model="form.categories" multiple class="w-full rounded-lg border-gray-300"
                                size="5">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Tekan Ctrl untuk pilih lebih dari satu</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tag</label>
                            <select x-model="form.tags" multiple class="w-full rounded-lg border-gray-300"
                                size="5">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('instructor.courses.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary disabled:opacity-50">
                    <span x-show="!loading">Simpan Kursus</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function courseForm() {
                return {
                    form: {
                        title: '{{ addslashes($course->title) }}',
                        slug: '{{ $course->slug }}',
                        short_description: '{{ addslashes($course->short_description) }}',
                        description: `{!! addslashes($course->description) !!}`,
                        thumbnail: null,
                        price: '{{ $course->price }}',
                        sale_price: '{{ $course->sale_price }}',
                        sale_starts_at: '{{ $course->sale_starts_at ? $course->sale_starts_at->format('Y-m-d\TH:i') : '' }}',
                        sale_ends_at: '{{ $course->sale_ends_at ? $course->sale_ends_at->format('Y-m-d\TH:i') : '' }}',
                        level: '{{ $course->level }}',
                        language: '{{ $course->language }}',
                        duration_total: '{{ $course->duration_total }}',
                        status: '{{ $course->status }}',
                        is_featured: {{ $course->is_featured ? 'true' : 'false' }},
                        has_certificate: {{ $course->has_certificate ? 'true' : 'false' }},
                        categories: @json($selectedCategories),
                        tags: @json($selectedTags),
                        learning_objectives: @json($course->learning_objectives ?? []),
                        requirements: @json($course->requirements ?? []),
                        target_audience: @json($course->target_audience ?? []),
                        prerequisites: @json($course->prerequisites ?? [])
                    },
                    errors: {},
                    loading: false,
                    thumbnailPreview: '{{ $course->thumbnail ? Storage::url($course->thumbnail) : '' }}',

                    init() {},

                    handleThumbnail(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.form.thumbnail = file;
                            this.thumbnailPreview = URL.createObjectURL(file);
                        }
                    },

                    submitForm() {
                        this.loading = true;
                        this.errors = {};
                        const formData = new FormData();
                        formData.append('_method', 'PUT');
                        for (let key in this.form) {
                            if (key === 'thumbnail' && this.form.thumbnail instanceof File) {
                                formData.append('thumbnail', this.form.thumbnail);
                            } else if (Array.isArray(this.form[key])) {
                                this.form[key].forEach((val, idx) => formData.append(`${key}[${idx}]`, val));
                            } else if (this.form[key] !== null && this.form[key] !== undefined) {
                                formData.append(key, this.form[key]);
                            }
                        }

                        fetch('{{ route('instructor.courses.update', $course) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    window.location.href = '{{ route('instructor.courses.index') }}';
                                } else {
                                    if (data.errors) this.errors = data.errors;
                                    else window.toast.error(data.message);
                                    this.loading = false;
                                }
                            })
                            .catch(() => {
                                window.toast.error('Terjadi kesalahan');
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
