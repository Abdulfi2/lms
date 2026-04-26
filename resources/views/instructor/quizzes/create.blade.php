{{-- resources/views/instructor/quizzes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Quiz Baru')
@section('page-title', 'Buat Quiz Baru')
@section('page-subtitle', 'Untuk kursus: ' . $course->title)

@section('content')
    <div x-data="quizForm()" x-init="init()" class="max-w-5xl mx-auto">
        <form @submit.prevent="submitForm" class="space-y-6">
            @csrf

            <!-- Progress Steps -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold"
                                :class="step === 1 ? 'bg-primary text-white' : 'bg-green-500 text-white'">
                                <span x-show="step !== 1" class="text-white">✓</span>
                                <span x-show="step === 1">1</span>
                            </div>
                            <span class="ml-2 text-sm font-medium"
                                :class="step >= 1 ? 'text-gray-800 dark:text-white' : 'text-gray-400'">Informasi Quiz</span>
                        </div>
                        <div class="w-12 h-0.5 bg-gray-300 mx-2"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold"
                                :class="step === 2 ? 'bg-primary text-white' : (step > 2 ? 'bg-green-500 text-white' :
                                    'bg-gray-200 text-gray-500')">
                                <span x-show="step > 2">✓</span>
                                <span x-show="step <= 2">2</span>
                            </div>
                            <span class="ml-2 text-sm font-medium"
                                :class="step >= 2 ? 'text-gray-800 dark:text-white' : 'text-gray-400'">Pengaturan</span>
                        </div>
                        <div class="w-12 h-0.5 bg-gray-300 mx-2"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold"
                                :class="step === 3 ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500'">
                                3
                            </div>
                            <span class="ml-2 text-sm font-medium"
                                :class="step >= 3 ? 'text-gray-800 dark:text-white' : 'text-gray-400'">Soal</span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span x-text="step"></span> / 3 langkah
                    </div>
                </div>
            </div>

            <!-- Step 1: Informasi Dasar -->
            <div x-show="step === 1" x-transition.duration.300
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Informasi Quiz</h3>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Judul Quiz <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="form.title" @input="generateSlug" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Contoh: Ujian Tengah Semester">
                        <p class="text-xs text-gray-500 mt-1">Beri judul yang jelas dan mudah diingat.</p>
                        <p x-show="errors.title" class="text-red-500 text-xs mt-1" x-text="errors.title"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Slug (URL)
                        </label>
                        <input type="text" x-model="form.slug"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-800"
                            placeholder="otomatis-dari-judul">
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk auto-generate dari judul.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi Quiz
                        </label>
                        <textarea x-model="form.description" rows="3"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Jelaskan tentang quiz ini, materi yang diujikan, dll."></textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="step = 2"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        Selanjutnya →
                    </button>
                </div>
            </div>

            <!-- Step 2: Pengaturan -->
            <div x-show="step === 2" x-transition.duration.300
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Pengaturan Quiz</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tipe Quiz
                        </label>
                        <select x-model="form.quiz_type"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="practice">Practice (Latihan)</option>
                            <option value="pretest">Pre-Test (Sebelum Lesson)</option>
                            <option value="posttest">Post-Test (Setelah Lesson)</option>
                            <option value="final">Final Exam</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Waktu Pengerjaan (menit)
                        </label>
                        <div class="relative">
                            <input type="number" x-model="form.time_limit" min="0" step="5"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 pr-16">
                            <span class="absolute right-3 top-2 text-gray-400 text-sm">menit</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">0 = tidak terbatas</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Maksimal Attempt
                        </label>
                        <input type="number" x-model="form.attempts_allowed" min="1" step="1"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Passing Score (%)
                        </label>
                        <div class="relative">
                            <input type="number" x-model="form.passing_score" min="0" max="100"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 pr-12">
                            <span class="absolute right-3 top-2 text-gray-400">%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
                    <h4 class="font-medium text-gray-800 dark:text-white">Opsi Tambahan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" x-model="form.randomize_questions"
                                class="rounded border-gray-300 text-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Acak urutan soal</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" x-model="form.is_published"
                                class="rounded border-gray-300 text-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Publikasikan sekarang</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="step = 1" class="px-6 py-2 border rounded-lg hover:bg-gray-100">
                        ← Sebelumnya
                    </button>
                    <button type="button" @click="step = 3"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                        Selanjutnya →
                    </button>
                </div>
            </div>

            <!-- Step 3: Soal -->
            <div x-show="step === 3" x-transition.duration.300
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 text-green-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Soal Quiz</h3>
                    </div>
                    <button type="button" @click="addQuestion"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Soal</span>
                    </button>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400">Total soal: <span x-text="questions.length"></span> |
                    Total poin: <span x-text="totalPoints"></span></p>

                <!-- Questions List -->
                <div class="space-y-4">
                    <template x-for="(q, idx) in questions" :key="idx">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm">Soal <span
                                            x-text="idx+1"></span></span>
                                    <select x-model="q.type" @change="updateType(q)"
                                        class="text-sm rounded border-gray-300">
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="true_false">True / False</option>
                                        <option value="essay">Essay</option>
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="button" @click="moveUp(idx)" x-show="idx > 0"
                                        class="text-gray-500 hover:text-gray-700">
                                        ↑
                                    </button>
                                    <button type="button" @click="moveDown(idx)" x-show="idx < questions.length - 1"
                                        class="text-gray-500 hover:text-gray-700">
                                        ↓
                                    </button>
                                    <button type="button" @click="removeQuestion(idx)"
                                        class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <textarea x-model="q.question" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Tulis soal..."></textarea>

                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">Poin:</span>
                                        <input type="number" x-model="q.points" class="w-20 rounded border-gray-300"
                                            min="1">
                                    </div>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" x-model="q.is_required" class="rounded">
                                        <span class="text-sm text-gray-500">Wajib dijawab</span>
                                    </label>
                                </div>

                                <!-- Multiple Choice Options -->
                                <div x-show="q.type === 'multiple_choice'" class="mt-2 space-y-2">
                                    <div class="text-sm font-medium text-gray-700">Pilihan Jawaban</div>
                                    <template x-for="(opt, optIdx) in q.options" :key="optIdx">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" x-model="q.correct_option" :value="optIdx"
                                                class="focus:ring-primary">
                                            <input type="text" x-model="opt.text"
                                                class="flex-1 rounded border-gray-300" placeholder="Opsi jawaban">
                                            <button type="button" @click="q.options.splice(optIdx,1)"
                                                class="text-red-500">✖</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="q.options.push({text:''})"
                                        class="text-sm text-primary hover:underline">
                                        + Tambah Opsi
                                    </button>
                                </div>

                                <!-- True/False -->
                                <div x-show="q.type === 'true_false'" class="mt-2 flex space-x-4">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" x-model="q.correct_answer" value="true"
                                            class="focus:ring-primary">
                                        <span>Benar</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" x-model="q.correct_answer" value="false"
                                            class="focus:ring-primary">
                                        <span>Salah</span>
                                    </label>
                                </div>

                                <!-- Essay -->
                                <div x-show="q.type === 'essay'" class="mt-2">
                                    <textarea x-model="q.sample_answer" rows="2" class="w-full rounded border-gray-300"
                                        placeholder="Contoh jawaban (opsional)"></textarea>
                                </div>

                                <div>
                                    <textarea x-model="q.explanation" rows="1" class="w-full rounded border-gray-300 text-sm"
                                        placeholder="Pembahasan (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="questions.length === 0" class="text-center py-8 border-2 border-dashed rounded-lg">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500">Belum ada soal. Klik "Tambah Soal" untuk mulai membuat soal.</p>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="step = 2" class="px-6 py-2 border rounded-lg hover:bg-gray-100">
                        ← Sebelumnya
                    </button>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                        <span x-show="!loading">Simpan Quiz</span>
                        <span x-show="loading">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function quizForm() {
                return {
                    step: 1,
                    form: {
                        title: '',
                        slug: '',
                        description: '',
                        quiz_type: 'practice',
                        time_limit: 0,
                        attempts_allowed: 1,
                        passing_score: 70,
                        randomize_questions: false,
                        is_published: true,
                    },
                    questions: [],
                    errors: {},
                    loading: false,

                    init() {},

                    generateSlug() {
                        if (this.form.slug === '' && this.form.title !== '') {
                            this.form.slug = this.form.title.toLowerCase()
                                .replace(/[^a-z0-9]+/g, '-')
                                .replace(/^-|-$/g, '');
                        }
                    },

                    get totalPoints() {
                        return this.questions.reduce((sum, q) => sum + (parseInt(q.points) || 0), 0);
                    },

                    addQuestion() {
                        this.questions.push({
                            question: '',
                            type: 'multiple_choice',
                            points: 10,
                            is_required: true,
                            options: [{
                                text: ''
                            }, {
                                text: ''
                            }],
                            correct_option: 0,
                            correct_answer: 'true',
                            sample_answer: '',
                            explanation: ''
                        });
                    },

                    updateType(q) {
                        if (q.type === 'multiple_choice') {
                            if (!q.options || q.options.length === 0) {
                                q.options = [{
                                    text: ''
                                }, {
                                    text: ''
                                }];
                            }
                            q.correct_option = 0;
                        }
                        if (q.type === 'true_false') {
                            q.correct_answer = 'true';
                            delete q.options;
                        }
                        if (q.type === 'essay') {
                            delete q.options;
                            delete q.correct_option;
                            delete q.correct_answer;
                        }
                    },

                    moveUp(idx) {
                        if (idx > 0) {
                            [this.questions[idx - 1], this.questions[idx]] = [this.questions[idx], this.questions[idx - 1]];
                        }
                    },

                    moveDown(idx) {
                        if (idx < this.questions.length - 1) {
                            [this.questions[idx + 1], this.questions[idx]] = [this.questions[idx], this.questions[idx + 1]];
                        }
                    },

                    removeQuestion(idx) {
                        if (confirm('Hapus soal ini?')) {
                            this.questions.splice(idx, 1);
                        }
                    },

                    submitForm() {
                        if (this.questions.length === 0) {
                            window.toast.error('Minimal 1 soal harus ditambahkan');
                            return;
                        }

                        this.loading = true;
                        this.errors = {};

                        // Format questions data
                        const quizData = {
                            ...this.form,
                            questions: this.questions.map(q => ({
                                question: q.question,
                                type: q.type,
                                points: q.points,
                                is_required: q.is_required,
                                explanation: q.explanation,
                                options: q.type === 'multiple_choice' ? q.options.map(opt => opt.text) : null,
                                correct_option: q.type === 'multiple_choice' ? q.correct_option : null,
                                correct_answer: q.type === 'true_false' ? q.correct_answer : null,
                                sample_answer: q.type === 'essay' ? q.sample_answer : null,
                            }))
                        };

                        fetch('{{ route('instructor.courses.quizzes.store', $course) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(quizData)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.toast.success(data.message);
                                    window.location.href = '{{ route('instructor.courses.quizzes.index', $course) }}';
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
