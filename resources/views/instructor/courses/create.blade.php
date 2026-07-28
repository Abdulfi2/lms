@extends('layouts.app')

@section('title', 'Buat Kursus Baru')
@section('page-title', 'Buat Kursus')
@section('page-subtitle', 'Tambahkan kursus baru ke platform')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div x-data="courseForm()" x-init="$nextTick(() => initQuill())" class="max-w-4xl mx-auto">
        <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Progress Steps -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 overflow-x-auto">
                <div class="flex items-center justify-between min-w-max gap-1">
                    <template x-for="s in 4" :key="s">
                        <div class="flex items-center">
                            <button type="button" @click="step = s" class="flex items-center group">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0"
                                    :class="step === s ? 'bg-primary text-white' : (step > s ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500')">
                                    <span x-show="step > s">✓</span>
                                    <span x-show="step <= s" x-text="s"></span>
                                </div>
                                <span class="ml-2 text-sm font-medium whitespace-nowrap"
                                    :class="step >= s ? 'text-gray-800 dark:text-white' : 'text-gray-400'"
                                    x-text="stepLabels[s - 1]"></span>
                            </button>
                            <div class="w-8 md:w-12 h-0.5 bg-gray-300 mx-2" x-show="s < 4"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Step 1: Info Dasar -->
            <div x-show="step === 1" x-transition.duration.300
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Info Dasar Kursus</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Judul Kursus <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" @input="generateSlug" required
                        placeholder="Contoh: Belajar Memasak untuk Pemula"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <p x-show="errors.title" class="text-red-500 text-xs mt-1" x-text="errors.title"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Alamat Halaman Kursus (Slug)</label>
                    <input type="text" x-model="form.slug"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                    <p class="text-xs text-gray-500 mt-1">Ini akan jadi bagian alamat website kursus Anda. Biarkan kosong — akan terisi otomatis dari judul.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Ringkasan Singkat</label>
                    <textarea x-model="form.short_description" rows="2" maxlength="255"
                        placeholder="Satu-dua kalimat yang muncul di daftar kursus"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                    <div id="quill-description" class="bg-white dark:bg-gray-900 rounded-b-lg" style="min-height: 180px;"></div>
                    <p class="text-xs text-gray-500 mt-1">Gunakan tombol format di atas untuk membuat huruf tebal, daftar poin, dll — seperti mengetik di Word.</p>
                    <p x-show="errors.description" class="text-red-500 text-xs mt-1" x-text="errors.description"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Gambar Sampul (Thumbnail)</label>
                    <input type="file" @change="handleThumbnail" accept="image/*" class="w-full">
                    <p class="text-xs text-gray-500 mt-1">Gambar ini akan tampil di daftar kursus. Format JPG/PNG, disarankan rasio 16:9.</p>
                    <template x-if="thumbnailPreview">
                        <img :src="thumbnailPreview" class="mt-2 h-24 rounded object-cover">
                    </template>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" @click="step = 2" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        Selanjutnya →
                    </button>
                </div>
            </div>

            <!-- Step 2: Harga -->
            <div x-show="step === 2" x-transition.duration.300
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Harga Kursus</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" x-model="form.price" step="1000" min="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <p class="text-xs text-gray-500 mt-1">Isi <strong>0</strong> jika kursus ini gratis.</p>
                    <p x-show="errors.price" class="text-red-500 text-xs mt-1" x-text="errors.price"></p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Diskon (Opsional)</h4>
                    <p class="text-xs text-gray-500 -mt-2">Isi bagian ini hanya jika Anda ingin memberi harga diskon sementara waktu.</p>
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga Setelah Diskon</label>
                        <input type="number" x-model="form.sale_price" step="1000" min="0"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Diskon Mulai</label>
                            <input type="datetime-local" x-model="form.sale_starts_at"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Diskon Berakhir</label>
                            <input type="datetime-local" x-model="form.sale_ends_at"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step = 1" class="px-6 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">← Sebelumnya</button>
                    <button type="button" @click="step = 3" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Selanjutnya →</button>
                </div>
            </div>

            <!-- Step 3: Detail & Materi Promosi (Opsional) -->
            <div x-show="step === 3" x-transition.duration.300
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Detail & Materi Promosi</h3>
                        <p class="text-xs text-gray-500">Opsional — bisa dilewati dan diisi belakangan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tingkat Kesulitan</label>
                        <select x-model="form.level" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="beginner">Pemula</option>
                            <option value="intermediate">Menengah</option>
                            <option value="advanced">Mahir</option>
                            <option value="all_levels">Semua Level</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Bahasa Pengantar</label>
                        <input type="text" x-model="form.language" placeholder="id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Perkiraan Durasi (jam)</label>
                        <input type="number" x-model="form.duration_total" min="0"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                </div>

                <div class="space-y-5 pt-2 border-t dark:border-gray-700">
                    <p class="text-sm text-gray-500">Bagian di bawah ini akan tampil di halaman promosi kursus Anda untuk meyakinkan calon siswa.</p>

                    <div>
                        <label class="block text-sm font-medium mb-1">Yang Akan Dipelajari Siswa</label>
                        <div x-data="{ items: form.learning_objectives }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]" placeholder="Contoh: Membuat kue dari nol"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah Poin</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Yang Perlu Disiapkan Siswa</label>
                        <div x-data="{ items: form.requirements }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]" placeholder="Contoh: Laptop dengan koneksi internet"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kursus Ini Cocok Untuk</label>
                        <div x-data="{ items: form.target_audience }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]" placeholder="Contoh: Pemula yang ingin belajar dari dasar"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kursus yang Perlu Diselesaikan Dulu (jika ada)</label>
                        <div x-data="{ items: form.prerequisites }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">Hapus</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">+ Tambah</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step = 2" class="px-6 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">← Sebelumnya</button>
                    <button type="button" @click="step = 4" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Selanjutnya →</button>
                </div>
            </div>

            <!-- Step 4: Kategori & Publikasikan -->
            <div x-show="step === 4" x-transition.duration.300
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 text-green-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Kategori & Publikasikan</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Kategori</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($categories as $cat)
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border cursor-pointer text-sm"
                                :class="form.categories.includes('{{ $cat->id }}') || form.categories.includes({{ $cat->id }}) ? 'bg-primary/10 border-primary text-primary' : 'border-gray-300 dark:border-gray-600'">
                                <input type="checkbox" value="{{ $cat->id }}" x-model="form.categories" class="rounded">
                                {{ $cat->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tag</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border cursor-pointer text-sm"
                                :class="form.tags.includes('{{ $tag->id }}') || form.tags.includes({{ $tag->id }}) ? 'bg-primary/10 border-primary text-primary' : 'border-gray-300 dark:border-gray-600'">
                                <input type="checkbox" value="{{ $tag->id }}" x-model="form.tags" class="rounded">
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Status Kursus</label>
                    <select x-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="draft">Simpan sebagai Draft (belum tayang, hanya Anda yang bisa lihat)</option>
                        <option value="pending">Ajukan ke Admin untuk Ditinjau (akan tayang setelah disetujui)</option>
                        <option value="published">Langsung Tayang (siswa bisa langsung mendaftar)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Anda bisa mengubah status ini kapan saja lewat halaman Edit Kursus.</p>
                </div>

                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="form.is_featured" class="rounded">
                        <span class="ml-2 text-sm">Tampilkan di kursus unggulan</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="form.has_certificate" class="rounded">
                        <span class="ml-2 text-sm">Beri sertifikat setelah selesai</span>
                    </label>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step = 3" class="px-6 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">← Sebelumnya</button>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                        <span x-show="!loading">Simpan Kursus</span>
                        <span x-show="loading">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
        <script>
            function courseForm() {
                return {
                    step: 1,
                    stepLabels: ['Info Dasar', 'Harga', 'Detail', 'Publikasikan'],
                    quill: null,
                    form: {
                        title: '',
                        slug: '',
                        short_description: '',
                        description: '',
                        thumbnail: null,
                        price: 0,
                        sale_price: null,
                        sale_starts_at: '',
                        sale_ends_at: '',
                        level: 'beginner',
                        language: 'id',
                        duration_total: 0,
                        status: 'draft',
                        is_featured: false,
                        has_certificate: false,
                        categories: [],
                        tags: [],
                        learning_objectives: [],
                        requirements: [],
                        target_audience: [],
                        prerequisites: []
                    },
                    errors: {},
                    loading: false,
                    thumbnailPreview: null,

                    generateSlug() {
                        if (this.form.slug === '' && this.form.title !== '') {
                            this.form.slug = this.form.title.toLowerCase()
                                .replace(/[^a-z0-9]+/g, '-')
                                .replace(/^-|-$/g, '');
                        }
                    },

                    handleThumbnail(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.form.thumbnail = file;
                            this.thumbnailPreview = URL.createObjectURL(file);
                        }
                    },

                    initQuill() {
                        this.quill = new Quill('#quill-description', {
                            theme: 'snow',
                            placeholder: 'Jelaskan kursus ini secara lengkap: apa yang dipelajari, untuk siapa, dll.',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline'],
                                    [{ list: 'ordered' }, { list: 'bullet' }],
                                    ['link'],
                                    ['clean']
                                ]
                            }
                        });
                    },

                    // x-show hanya menyembunyikan step lewat CSS (display:none), jadi atribut
                    // `required` HTML tidak bisa diandalkan untuk step yang sedang tidak aktif.
                    // Validasi manual di sini sebelum submit, lalu loncat ke step yang bermasalah.
                    validateBeforeSubmit() {
                        this.errors = {};
                        this.form.description = this.quill.root.innerHTML;

                        if (!this.form.title.trim()) {
                            this.errors.title = 'Judul kursus wajib diisi.';
                            this.step = 1;
                            return false;
                        }
                        if (this.quill.getText().trim().length === 0) {
                            this.errors.description = 'Deskripsi lengkap wajib diisi.';
                            this.step = 1;
                            return false;
                        }
                        if (this.form.price === '' || this.form.price === null || this.form.price < 0) {
                            this.errors.price = 'Harga wajib diisi (isi 0 jika gratis).';
                            this.step = 2;
                            return false;
                        }
                        return true;
                    },

                    submitForm() {
                        if (!this.validateBeforeSubmit()) {
                            window.toast.error('Ada bagian yang belum lengkap, silakan periksa kembali.');
                            return;
                        }

                        this.loading = true;
                        this.errors = {};
                        const formData = new FormData();
                        for (let key in this.form) {
                            if (key === 'thumbnail' && this.form.thumbnail instanceof File) {
                                formData.append('thumbnail', this.form.thumbnail);
                            } else if (Array.isArray(this.form[key])) {
                                this.form[key].forEach((val, idx) => formData.append(`${key}[${idx}]`, val));
                            } else if (this.form[key] !== null && this.form[key] !== undefined) {
                                formData.append(key, this.form[key]);
                            }
                        }

                        fetch('{{ route('instructor.courses.store') }}', {
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
                                    // Arahkan ke halaman section kursus baru (bukan daftar kursus) supaya
                                    // instruktur langsung diarahkan ke langkah berikutnya: tambah section & lesson.
                                    const sectionsUrl = '{{ route('instructor.courses.sections.index', ['course' => '__ID__']) }}'
                                        .replace('__ID__', data.course_id);
                                    window.location.href = sectionsUrl + '?new=1';
                                } else {
                                    if (data.errors) {
                                        this.errors = data.errors;
                                        if (data.errors.title || data.errors.description) this.step = 1;
                                        else if (data.errors.price) this.step = 2;
                                    } else {
                                        window.toast.error(data.message);
                                    }
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
