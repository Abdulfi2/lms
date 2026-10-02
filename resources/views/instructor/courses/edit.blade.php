@extends('layouts.app')

@section('title', __('courses.edit_title'))
@section('page-title', __('courses.edit_title'))

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus Saya', 'url' => route('instructor.courses.index')],
        ['label' => $course->title, 'url' => null],
    ]" />
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div x-data="courseForm()" x-init="init()" class="max-w-4xl mx-auto">
        <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Progress Steps -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 overflow-x-auto">
                <div class="flex items-center justify-between min-w-max gap-1">
                    <template x-for="s in 4" :key="s">
                        <div class="flex items-center">
                            <button type="button" @click="step = s" class="flex items-center group">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0"
                                    :class="step === s ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500'">
                                    <span x-text="s"></span>
                                </div>
                                <span class="ml-2 text-sm font-medium whitespace-nowrap"
                                    :class="step === s ? 'text-gray-800 dark:text-white' : 'text-gray-400'"
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
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">@lang('courses.section_basic_info')</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">@lang('courses.field_title') <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <p x-show="errors.title" class="text-red-500 text-xs mt-1" x-text="errors.title"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">@lang('courses.field_slug')</label>
                    <input type="text" x-model="form.slug"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                    <p class="text-xs text-gray-500 mt-1">@lang('courses.field_slug_help_edit')</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">@lang('courses.field_short_description')</label>
                    <textarea x-model="form.short_description" rows="2" maxlength="255"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">@lang('courses.field_description') <span class="text-red-500">*</span></label>
                    <div id="quill-description" class="bg-white dark:bg-gray-900 rounded-b-lg" style="min-height: 180px;"></div>
                    <p class="text-xs text-gray-500 mt-1">@lang('courses.field_description_help')</p>
                    <p x-show="errors.description" class="text-red-500 text-xs mt-1" x-text="errors.description"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">@lang('courses.field_thumbnail')</label>
                    <input type="file" @change="handleThumbnail" accept="image/*" class="w-full">
                    <p class="text-xs text-gray-500 mt-1">@lang('courses.field_thumbnail_help_edit')</p>
                    <template x-if="thumbnailPreview">
                        <img :src="thumbnailPreview" class="mt-2 h-24 rounded object-cover">
                    </template>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" @click="step = 2" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        @lang('courses.next')
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
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">@lang('courses.section_pricing')</h3>
                </div>

                <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="form.is_free" @change="toggleFree" class="rounded">
                        <span class="ml-2 text-sm font-medium text-gray-800 dark:text-white">@lang('courses.is_free_label')</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 -mt-4">@lang('courses.is_free_help')</p>

                <div x-show="!form.is_free" x-transition.duration.200>
                    <label class="block text-sm font-medium mb-1">@lang('courses.field_price') <span class="text-red-500">*</span></label>
                    <input type="number" x-model="form.price" step="1000" min="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <p class="text-xs text-gray-500 mt-1">@lang('courses.field_price_help')</p>
                    <p x-show="errors.price" class="text-red-500 text-xs mt-1" x-text="errors.price"></p>
                </div>

                <div x-show="!form.is_free" x-transition.duration.200
                    class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">@lang('courses.discount_section_title')</h4>
                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_sale_price')</label>
                        <input type="number" x-model="form.sale_price" step="1000" min="0"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">@lang('courses.field_sale_starts_at')</label>
                            <input type="datetime-local" x-model="form.sale_starts_at"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">@lang('courses.field_sale_ends_at')</label>
                            <input type="datetime-local" x-model="form.sale_ends_at"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step = 1" class="px-6 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">@lang('courses.previous')</button>
                    <button type="button" @click="step = 3" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">@lang('courses.next')</button>
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
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">@lang('courses.section_details_promo')</h3>
                        <p class="text-xs text-gray-500">@lang('courses.section_details_promo_hint')</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_level')</label>
                        <select x-model="form.level" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="beginner">@lang('courses.level_beginner')</option>
                            <option value="intermediate">@lang('courses.level_intermediate')</option>
                            <option value="advanced">@lang('courses.level_advanced')</option>
                            <option value="all_levels">@lang('courses.level_all')</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_language')</label>
                        <input type="text" x-model="form.language" placeholder="id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_duration')</label>
                        <input type="number" x-model="form.duration_total" min="0"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                </div>

                <div class="space-y-5 pt-2 border-t dark:border-gray-700">
                    <p class="text-sm text-gray-500">@lang('courses.details_intro')</p>

                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_learning_objectives')</label>
                        <div x-data="{ items: form.learning_objectives }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">@lang('courses.remove')</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_point')</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_requirements')</label>
                        <div x-data="{ items: form.requirements }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">@lang('courses.remove')</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_generic')</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_target_audience')</label>
                        <div x-data="{ items: form.target_audience }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">@lang('courses.remove')</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_generic')</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">@lang('courses.field_prerequisites')</label>
                        <div x-data="{ items: form.prerequisites }">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="flex mb-2">
                                    <input type="text" x-model="items[idx]"
                                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <button type="button" @click="items.splice(idx,1)" class="ml-2 text-red-500">@lang('courses.remove')</button>
                                </div>
                            </template>
                            <button type="button" @click="items.push('')" class="text-primary text-sm">@lang('courses.add_generic')</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step = 2" class="px-6 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">@lang('courses.previous')</button>
                    <button type="button" @click="step = 4" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">@lang('courses.next')</button>
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
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">@lang('courses.section_category_publish')</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">@lang('courses.field_categories')</label>
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
                    <label class="block text-sm font-medium mb-2">@lang('courses.field_tags')</label>
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
                    <label class="block text-sm font-medium mb-1">@lang('courses.field_status')</label>
                    <select x-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="draft">@lang('courses.status_draft_option')</option>
                        <option value="pending">@lang('courses.status_pending_option')</option>
                        <option value="published">@lang('courses.status_published_option')</option>
                        <option value="archived">@lang('courses.status_archived_option')</option>
                    </select>
                    @if ($course->status === 'draft' && $course->rejection_reason)
                        <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm text-red-700 dark:text-red-400">
                            <strong>@lang('courses.rejected_by_admin')</strong> {{ $course->rejection_reason }}
                        </div>
                    @elseif ($course->status === 'pending')
                        <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-sm text-yellow-700 dark:text-yellow-400">
                            @lang('courses.pending_review_notice')
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="form.is_featured" class="rounded">
                        <span class="ml-2 text-sm">@lang('courses.field_featured')</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" x-model="form.has_certificate" class="rounded">
                        <span class="ml-2 text-sm">@lang('courses.field_certificate')</span>
                    </label>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="step = 3" class="px-6 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">@lang('courses.previous')</button>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                        <span x-show="!loading">@lang('courses.save_changes')</span>
                        <span x-show="loading">@lang('courses.saving')</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
        <script>
            const courseFormI18n = {
                stepLabels: [@json(__('courses.step_basic_info')), @json(__('courses.step_pricing')), @json(__('courses.step_details')), @json(__('courses.step_publish'))],
                descriptionPlaceholder: @json(__('courses.field_description_placeholder')),
                errorTitleRequired: @json(__('courses.error_title_required')),
                errorDescriptionRequired: @json(__('courses.error_description_required')),
                errorPriceRequired: @json(__('courses.error_price_required')),
                errorIncompleteForm: @json(__('courses.error_incomplete_form')),
                errorGeneric: @json(__('courses.error_generic')),
            };

            function courseForm() {
                return {
                    step: 1,
                    stepLabels: courseFormI18n.stepLabels,
                    form: {
                        title: @json($course->title),
                        slug: @json($course->slug),
                        short_description: @json($course->short_description ?? ''),
                        description: @json($course->description),
                        thumbnail: null,
                        is_free: {{ (float) $course->price === 0.0 ? 'true' : 'false' }},
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
                    quill: null,
                    thumbnailPreview: '{{ $course->thumbnail ? Storage::url($course->thumbnail) : '' }}',

                    init() {
                        this.$nextTick(() => this.initQuill());
                    },

                    initQuill() {
                        this.quill = new Quill('#quill-description', {
                            theme: 'snow',
                            placeholder: courseFormI18n.descriptionPlaceholder,
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline'],
                                    [{ list: 'ordered' }, { list: 'bullet' }],
                                    ['link'],
                                    ['clean']
                                ]
                            }
                        });
                        if (this.form.description) {
                            this.quill.root.innerHTML = this.form.description;
                        }
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

                    validateBeforeSubmit() {
                        this.errors = {};
                        this.form.description = this.quill.root.innerHTML;

                        if (!this.form.title.trim()) {
                            this.errors.title = courseFormI18n.errorTitleRequired;
                            this.step = 1;
                            return false;
                        }
                        if (this.quill.getText().trim().length === 0) {
                            this.errors.description = courseFormI18n.errorDescriptionRequired;
                            this.step = 1;
                            return false;
                        }
                        if (!this.form.is_free && (this.form.price === '' || this.form.price === null || this.form.price < 0)) {
                            this.errors.price = courseFormI18n.errorPriceRequired;
                            this.step = 2;
                            return false;
                        }
                        return true;
                    },

                    submitForm() {
                        if (!this.validateBeforeSubmit()) {
                            window.toast.error(courseFormI18n.errorIncompleteForm);
                            return;
                        }

                        this.loading = true;
                        this.errors = {};
                        const formData = new FormData();
                        formData.append('_method', 'PUT');
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
                                    // Beri jeda supaya toast sempat tampil sebelum halaman pindah —
                                    // redirect instan bikin toast tidak pernah sempat ter-render.
                                    setTimeout(() => {
                                        window.location.href = '{{ route('instructor.courses.index') }}';
                                    }, 800);
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
                                window.toast.error(courseFormI18n.errorGeneric);
                                this.loading = false;
                            });
                    }
                }
            }
        </script>
    @endpush
@endsection
