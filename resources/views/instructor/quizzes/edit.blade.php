{{-- resources/views/instructor/quizzes/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Quiz - ' . $quiz->title)
@section('page-title', 'Edit Quiz')
@section('page-subtitle', 'Kursus: ' . $course->title)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Kursus Saya', 'url' => route('instructor.courses.index')],
        ['label' => $course->title, 'url' => route('instructor.courses.edit', $course)],
        ['label' => 'Quiz', 'url' => route('instructor.courses.quizzes.index', $course)],
        ['label' => $quiz->title, 'url' => null],
    ]" />
@endsection

@section('content')
    <div x-data="quizEditor()" x-init="init()" class="max-w-5xl mx-auto space-y-6">

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

        <!-- Form Informasi & Pengaturan Quiz (Step 1 & 2) -->
        <form action="{{ route('instructor.courses.quizzes.update', [$course, $quiz]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

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
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi Quiz
                        </label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Jelaskan tentang quiz ini, materi yang diujikan, dll.">{{ old('description', $quiz->description) }}</textarea>
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
                        <select name="quiz_type"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="practice" {{ old('quiz_type', $quiz->quiz_type) === 'practice' ? 'selected' : '' }}>Practice (Latihan)</option>
                            <option value="pretest" {{ old('quiz_type', $quiz->quiz_type) === 'pretest' ? 'selected' : '' }}>Pre-Test (Sebelum Lesson)</option>
                            <option value="posttest" {{ old('quiz_type', $quiz->quiz_type) === 'posttest' ? 'selected' : '' }}>Post-Test (Setelah Lesson)</option>
                            <option value="final" {{ old('quiz_type', $quiz->quiz_type) === 'final' ? 'selected' : '' }}>Final Exam</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Lesson Terkait (opsional)
                        </label>
                        <select name="lesson_id"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="">-- Tidak terikat lesson tertentu --</option>
                            @php $quizTypeLabels = ['practice' => 'Practice', 'pretest' => 'Pre-Test', 'posttest' => 'Post-Test', 'final' => 'Final']; @endphp
                            @foreach ($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ (int) old('lesson_id', $quiz->lesson_id) === $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->section->title }} — {{ $lesson->title }}
                                    @if (!empty($lesson->existing_quiz_types))
                                        (sudah ada: {{ collect($lesson->existing_quiz_types)->map(fn ($t) => $quizTypeLabels[$t] ?? $t)->implode(', ') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            @if ($lessons->isEmpty())
                                Belum ada lesson bertipe "Quiz" yang tersedia di kursus ini.
                            @else
                                Kalau dipilih, quiz ini akan muncul dan bisa langsung dikerjakan siswa dari halaman lesson tersebut.
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Waktu Pengerjaan (menit)
                        </label>
                        <div class="relative">
                            <input type="number" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}"
                                min="0" step="5"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 pr-16">
                            <span class="absolute right-3 top-2 text-gray-400 text-sm">menit</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">0 = tidak terbatas</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Maksimal Attempt
                        </label>
                        <input type="number" name="attempts_allowed"
                            value="{{ old('attempts_allowed', $quiz->attempts_allowed) }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            min="1">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Passing Score (%)
                        </label>
                        <div class="relative">
                            <input type="number" name="passing_score"
                                value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 pr-12">
                            <span class="absolute right-3 top-2 text-gray-400">%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Nilai minimal (%) supaya siswa dinyatakan lulus quiz ini.</p>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
                    <h4 class="font-medium text-gray-800 dark:text-white">Opsi Tambahan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="randomize_questions" value="1"
                                {{ old('randomize_questions', $quiz->randomize_questions) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Acak urutan soal</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="is_published" value="1"
                                {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Publikasikan sekarang</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" @click="step = 1" class="px-6 py-2 border rounded-lg hover:bg-gray-100">
                        ← Sebelumnya
                    </button>
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                            Simpan Info Quiz
                        </button>
                        <button type="button" @click="step = 3"
                            class="px-6 py-2 border rounded-lg hover:bg-gray-100">
                            Lanjut ke Soal →
                        </button>
                    </div>
                </div>
            </div>
        </form>

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

            <div class="space-y-4">
                <template x-for="(q, idx) in questions" :key="idx">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-2">
                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm">Soal <span
                                        x-text="idx+1"></span></span>
                                <select x-model="q.type" @change="updateType(q)" class="text-sm rounded border-gray-300">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True / False</option>
                                    <option value="essay">Essay</option>
                                </select>
                            </div>
                            <button type="button" @click="removeQuestion(idx)" class="text-red-600 hover:text-red-800"
                                title="Hapus Soal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2">
                            <textarea x-model="q.question" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Teks soal"></textarea>
                        </div>
                        <div class="mt-2">
                            <label class="text-sm font-medium">Poin</label>
                            <input type="number" x-model="q.points" class="w-24 rounded border-gray-300 ml-2"
                                min="1">
                        </div>

                        <!-- Multiple Choice Options -->
                        <template x-if="q.type === 'multiple_choice'">
                            <div class="mt-3">
                                <label class="text-sm font-medium">Pilihan Jawaban</label>
                                <div class="space-y-2 mt-1">
                                    <template x-for="(opt, optIdx) in q.options" :key="optIdx">
                                        <div class="flex items-center space-x-2">
                                            <input type="text" x-model="opt.text" class="flex-1 rounded border-gray-300"
                                                placeholder="Opsi">
                                            <label class="flex items-center space-x-1">
                                                <input type="checkbox" x-model="opt.is_correct" class="rounded"> <span
                                                    class="text-sm">Benar</span>
                                            </label>
                                            <button type="button" @click="q.options.splice(optIdx,1)" class="text-red-500">✖</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="q.options.push({text:'', is_correct:false})"
                                        class="text-sm text-primary">+ Tambah Opsi</button>
                                </div>
                            </div>
                        </template>

                        <!-- True False -->
                        <template x-if="q.type === 'true_false'">
                            <div class="mt-3 flex space-x-4">
                                <label class="flex items-center"><input type="radio" x-model="q.correct_answer"
                                        value="true" class="mr-1"> Benar</label>
                                <label class="flex items-center"><input type="radio" x-model="q.correct_answer"
                                        value="false" class="mr-1"> Salah</label>
                            </div>
                        </template>

                        <!-- Essay -->
                        <template x-if="q.type === 'essay'">
                            <div class="mt-3 text-sm text-gray-500">Essay akan dinilai manual oleh instruktur.</div>
                        </template>

                        <div class="mt-3">
                            <label class="text-sm font-medium">Penjelasan (opsional)</label>
                            <textarea x-model="q.explanation" rows="1" class="w-full rounded border-gray-300 text-sm"
                                placeholder="Penjelasan jawaban"></textarea>
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
                <button type="button" @click="saveQuestions" :disabled="saving"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                    <span x-show="!saving">Simpan Semua Soal</span>
                    <span x-show="saving">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function quizEditor() {
                return {
                    step: 1,
                    questions: [], // akan diisi dari data server
                    saving: false,
                    init() {
                        @php
                            $questionData = [];
                            foreach ($quiz->questions as $q) {
                                $item = [
                                    'id' => $q->id,
                                    'question' => $q->question,
                                    'type' => $q->type,
                                    'points' => $q->points,
                                    'explanation' => $q->explanation,
                                ];
                                if ($q->type === 'multiple_choice') {
                                    $item['options'] = $q->options->map(fn($opt) => ['text' => $opt->option_text, 'is_correct' => (bool) $opt->is_correct])->toArray();
                                } elseif ($q->type === 'true_false') {
                                    $trueOpt = $q->options->firstWhere('option_text', 'Benar');
                                    $item['correct_answer'] = $trueOpt && $trueOpt->is_correct ? 'true' : 'false';
                                }
                                $questionData[] = $item;
                            }
                        @endphp
                        this.questions = @json($questionData);
                    },
                    addQuestion() {
                        this.questions.push({
                            id: null,
                            question: '',
                            type: 'multiple_choice',
                            points: 1,
                            options: [{
                                text: '',
                                is_correct: false
                            }],
                            correct_answer: 'true',
                            explanation: ''
                        });
                    },
                    updateType(q) {
                        if (q.type === 'multiple_choice' && !q.options) {
                            q.options = [{
                                text: '',
                                is_correct: false
                            }];
                        }
                        if (q.type === 'true_false') {
                            q.correct_answer = 'true';
                            delete q.options;
                        }
                        if (q.type === 'essay') {
                            delete q.options;
                            delete q.correct_answer;
                        }
                    },
                    removeQuestion(idx) {
                        if (confirm('Hapus soal ini?')) {
                            this.questions.splice(idx, 1);
                        }
                    },
                    saveQuestions() {
                        if (this.questions.length === 0) {
                            window.toast.error('Minimal 1 soal harus ditambahkan');
                            return;
                        }

                        this.saving = true;
                        const courseId = {{ $course->id }};
                        const quizId = {{ $quiz->id }};
                        let promises = [];
                        for (let q of this.questions) {
                            let payload = {
                                question: q.question,
                                type: q.type,
                                points: q.points,
                                explanation: q.explanation
                            };
                            if (q.type === 'multiple_choice') {
                                payload.options = q.options;
                            } else if (q.type === 'true_false') {
                                payload.correct_answer = q.correct_answer;
                            }
                            let url, method;
                            if (q.id) {
                                url = `/instructor/courses/${courseId}/quizzes/${quizId}/questions/${q.id}`;
                                method = 'PUT';
                            } else {
                                url = `/instructor/courses/${courseId}/quizzes/${quizId}/questions`;
                                method = 'POST';
                            }
                            promises.push(
                                fetch(url, {
                                    method: method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(payload)
                                }).then(res => res.json()).then(result => ({ q, result }))
                            );
                        }
                        Promise.all(promises).then(pairs => {
                            let allSuccess = pairs.every(({ result }) => result.success);
                            pairs.forEach(({ q, result }) => {
                                if (result.success && result.question) {
                                    q.id = result.question.id;
                                }
                            });
                            if (allSuccess) {
                                window.toast.success('Semua soal berhasil disimpan');
                            } else {
                                window.toast.error('Ada kesalahan saat menyimpan soal');
                            }
                            this.saving = false;
                        }).catch(() => {
                            window.toast.error('Terjadi kesalahan');
                            this.saving = false;
                        });
                    }
                }
            }
        </script>
    @endpush
@endsection
