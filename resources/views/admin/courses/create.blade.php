@extends('layouts.app')

@section('title', __('courses.create_title'))
@section('page-title', __('courses.create_heading'))
@section('page-subtitle', __('courses.create_subtitle'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.default.min.css" rel="stylesheet">
    @include('components.tom-select-dark-mode')
@endpush

@section('content')
    <div x-data="courseForm()" x-init="init()" class="max-w-5xl mx-auto">
        <form @submit.prevent="submitForm" enctype="multipart/form-data"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            @csrf
            <div class="p-6 space-y-8">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">@lang('courses.section_basic_info')</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_title') <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="form.title" @input="generateSlug" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <p x-show="errors.title" class="text-red-500 text-xs mt-1" x-text="errors.title"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_slug_admin')</label>
                            <input type="text" x-model="form.slug"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_short_description_admin')</label>
                            <textarea x-model="form.short_description" rows="2"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_thumbnail')</label>
                            <input type="file" @change="handleThumbnail" accept="image/*" class="w-full">
                            <template x-if="thumbnailPreview">
                                <img :src="thumbnailPreview" class="mt-2 h-20 rounded object-cover">
                            </template>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_description')
                            <span class="text-red-500">*</span></label>
                        <textarea x-model="form.description" rows="5"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                        <p x-show="errors.description" class="text-red-500 text-xs mt-1" x-text="errors.description"></p>
                    </div>
                </div>

                <!-- Categories & Tags -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">@lang('courses.section_category_tag')</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_categories')</label>
                            <select x-ref="categoriesSelect" multiple class="w-full">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_tags')</label>
                            <select x-ref="tagsSelect" multiple class="w-full">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Sale -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">@lang('courses.discount_section_admin_title')</h3>

                    <label class="flex items-center cursor-pointer mb-4 bg-green-50 dark:bg-green-900/20 rounded-lg p-3 w-fit">
                        <input type="checkbox" x-model="form.is_free" @change="toggleFree" class="rounded">
                        <span class="ml-2 text-sm font-medium text-gray-800 dark:text-white">@lang('courses.is_free_label')</span>
                    </label>

                    <div x-show="!form.is_free" x-transition.duration.200 class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_price') <span
                                    class="text-red-500">*</span></label>
                            <input type="number" x-model="form.price" step="1000"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <p x-show="errors.price" class="text-red-500 text-xs mt-1" x-text="errors.price"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_sale_price_admin')</label>
                            <input type="number" x-model="form.sale_price" step="1000"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_sale_starts_at_admin')</label>
                            <input type="datetime-local" x-model="form.sale_starts_at"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_sale_ends_at_admin')</label>
                            <input type="datetime-local" x-model="form.sale_ends_at"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                    </div>
                </div>

                <!-- Level & Metadata -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">@lang('courses.section_level_settings')</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_level_admin')</label>
                            <select x-model="form.level"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="beginner">@lang('courses.level_beginner')</option>
                                <option value="intermediate">@lang('courses.level_intermediate')</option>
                                <option value="advanced">@lang('courses.level_advanced')</option>
                                <option value="all_levels">@lang('courses.level_all')</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_language_admin')</label>
                            <input type="text" x-model="form.language" placeholder="id, en"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_duration_admin')</label>
                            <input type="number" x-model="form.duration_total"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_status_admin')</label>
                            <select x-model="form.status"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="draft">@lang('courses.status_draft')</option>
                                <option value="pending">@lang('courses.status_pending')</option>
                                <option value="published">@lang('courses.status_published')</option>
                                <option value="archived">@lang('courses.status_archived')</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_instructor')</label>
                            <select x-model="form.instructor_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                @foreach ($instructors as $inst)
                                    <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="flex items-center"><input type="checkbox" x-model="form.is_featured"
                                    class="rounded"> <span class="ml-2">@lang('courses.field_featured_admin')</span></label>
                            <label class="flex items-center"><input type="checkbox" x-model="form.has_certificate"
                                    class="rounded"> <span class="ml-2">@lang('courses.field_certificate_admin')</span></label>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Arrays (Requirements, Objectives, Audience, Prerequisites) -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">@lang('courses.section_dynamic_content')</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_requirements_admin')</label>
                            <div x-data="{ items: form.requirements }">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2"><input type="text" x-model="items[idx]"
                                            class="flex-1 rounded-lg border-gray-300"><button type="button"
                                            @click="items.splice(idx,1)" class="ml-2 text-red-500">@lang('courses.remove')</button></div>
                                </template>
                                <button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_requirement')</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_learning_objectives_admin')</label>
                            <div x-data="{ items: form.learning_objectives }">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2"><input type="text" x-model="items[idx]"
                                            class="flex-1 rounded-lg"><button type="button" @click="items.splice(idx,1)"
                                            class="ml-2 text-red-500">@lang('courses.remove')</button></div>
                                </template>
                                <button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_objective')</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_target_audience_admin')</label>
                            <div x-data="{ items: form.target_audience }"><template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2"><input type="text" x-model="items[idx]"
                                            class="flex-1 rounded-lg"><button type="button" @click="items.splice(idx,1)"
                                            class="ml-2 text-red-500">@lang('courses.remove')</button></div>
                                </template><button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_target')</button></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('courses.field_prerequisites_admin')</label>
                            <div x-data="{ items: form.prerequisites }"><template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex mb-2"><input type="text" x-model="items[idx]"
                                            class="flex-1 rounded-lg"><button type="button" @click="items.splice(idx,1)"
                                            class="ml-2 text-red-500">@lang('courses.remove')</button></div>
                                </template><button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_prerequisite')</button></div>
                        </div>
                    </div>
                </div>

                <!-- Meta SEO -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">@lang('courses.section_seo')</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium mb-1">@lang('courses.field_meta_keywords')</label><input type="text"
                                x-model="form.meta_keywords" class="w-full rounded-lg border-gray-300"></div>
                        <div><label class="block text-sm font-medium mb-1">@lang('courses.field_meta_description')</label>
                            <textarea x-model="form.meta_description" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3">
                <a href="{{ route('admin.courses.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100">@lang('courses.cancel')</a>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary disabled:opacity-50">
                    <span x-show="!loading">@lang('courses.save_course')</span>
                    <span x-show="loading">@lang('courses.saving')</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
        <script>
            const courseFormI18n = {
                errorGeneric: @json(__('courses.error_generic')),
                searchCategoriesPlaceholder: @json(__('courses.search_categories_placeholder')),
                searchTagsPlaceholder: @json(__('courses.search_tags_placeholder')),
            };

            function courseForm() {
                return {
                    form: {
                        title: '',
                        slug: '',
                        short_description: '',
                        description: '',
                        thumbnail: null,
                        categories: [],
                        tags: [],
                        is_free: false,
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
                        instructor_id: '{{ auth()->user()->hasRole('admin') ? '' : auth()->id() }}',
                        requirements: [],
                        learning_objectives: [],
                        target_audience: [],
                        prerequisites: [],
                        meta_keywords: '',
                        meta_description: ''
                    },
                    errors: {},
                    loading: false,
                    thumbnailPreview: null,
                    init() {
                        // Jika instructor_id belum terisi (admin), bisa diisi manual nanti
                        new TomSelect(this.$refs.categoriesSelect, {
                            plugins: ['remove_button'],
                            placeholder: courseFormI18n.searchCategoriesPlaceholder,
                            onChange: (values) => { this.form.categories = values; }
                        });
                        new TomSelect(this.$refs.tagsSelect, {
                            plugins: ['remove_button'],
                            placeholder: courseFormI18n.searchTagsPlaceholder,
                            onChange: (values) => { this.form.tags = values; }
                        });
                    },
                    generateSlug() {
                        if (!this.form.slug) this.form.slug = this.form.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(
                            /^-|-$/g, '');
                    },
                    toggleFree() {
                        if (this.form.is_free) {
                            this.form.price = 0;
                            this.form.sale_price = null;
                            this.form.sale_starts_at = '';
                            this.form.sale_ends_at = '';
                        }
                    },
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
                        for (let key in this.form) {
                            if (key === 'is_free') continue;
                            if (key === 'thumbnail' && this.form.thumbnail instanceof File) {
                                formData.append('thumbnail', this.form.thumbnail);
                            } else if (Array.isArray(this.form[key])) {
                                this.form[key].forEach((val, idx) => formData.append(`${key}[${idx}]`, val));
                            } else if (this.form[key] !== null && this.form[key] !== undefined) {
                                // FormData men-stringify boolean JS jadi literal "true"/"false", tapi
                                // rule validasi Laravel 'boolean' cuma menerima true/false/1/0/"1"/"0" —
                                // konversi eksplisit supaya tidak ditolak validasi.
                                const value = typeof this.form[key] === 'boolean' ? (this.form[key] ? '1' : '0') : this.form[key];
                                formData.append(key, value);
                            }
                        }
                        fetch('{{ route('admin.courses.store') }}', {
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
                                    // Beri jeda supaya toast sempat tampil sebelum halaman pindah —
                                    // redirect instan bikin toast tidak pernah sempat ter-render.
                                    setTimeout(() => {
                                        window.location.href = '{{ route('admin.courses.index') }}';
                                    }, 800);
                                } else {
                                    if (data.errors) this.errors = data.errors;
                                    else window.toast.error(data.message);
                                    this.loading = false;
                                }
                            }).catch(() => {
                                window.toast.error(courseFormI18n.errorGeneric);
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
