{{-- resources/views/instructor/quizzes/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Quiz - ' . $quiz->title)
@section('page-title', 'Edit Quiz')
@section('page-subtitle', 'Kursus: ' . $course->title)

@section('content')
    <div x-data="quizEditor()" x-init="init()" class="max-w-5xl mx-auto space-y-6">
        <!-- Form Informasi Quiz -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('instructor.courses.quizzes.update', [$course, $quiz]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Quiz</label>
                        <input type="text" name="title" value="{{ old('title', $quiz->title) }}"
                            class="w-full rounded-lg border-gray-300" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                        <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300">{{ old('description', $quiz->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Limit
                            (menit)</label>
                        <input type="number" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}"
                            class="w-full rounded-lg border-gray-300" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attempts
                            Allowed</label>
                        <input type="number" name="attempts_allowed"
                            value="{{ old('attempts_allowed', $quiz->attempts_allowed) }}"
                            class="w-full rounded-lg border-gray-300" min="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Passing Score
                            (%)</label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}"
                            class="w-full rounded-lg border-gray-300" min="0" max="100">
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center"><input type="checkbox" name="randomize_questions" value="1"
                                {{ $quiz->randomize_questions ? 'checked' : '' }} class="rounded"> <span
                                class="ml-2">Randomize Questions</span></label>
                        <label class="flex items-center"><input type="checkbox" name="is_published" value="1"
                                {{ $quiz->is_published ? 'checked' : '' }} class="rounded"> <span
                                class="ml-2">Published</span></label>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Update Quiz
                        Info</button>
                </div>
            </form>
        </div>

        <!-- Daftar Soal -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Daftar Soal</h3>
                <button @click="addQuestion"
                    class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">+ Tambah Soal</button>
            </div>

            <div class="space-y-4">
                <template x-for="(q, idx) in questions" :key="idx">
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Soal <span
                                        x-text="idx+1"></span></span>
                                <select x-model="q.type" @change="updateType(q)" class="text-sm rounded border-gray-300">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True / False</option>
                                    <option value="essay">Essay</option>
                                </select>
                            </div>
                            <button @click="removeQuestion(idx)" class="text-red-600 hover:text-red-800" title="Hapus Soal">
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
                                            <button @click="q.options.splice(optIdx,1)" class="text-red-500">✖</button>
                                        </div>
                                    </template>
                                    <button @click="q.options.push({text:'', is_correct:false})"
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
                <div x-show="questions.length === 0" class="text-center text-gray-500 py-6">Belum ada soal. Klik "Tambah
                    Soal".</div>
            </div>

            <div class="mt-6 flex justify-end">
                <button @click="saveQuestions"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Semua Soal</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function quizEditor() {
                return {
                    questions: [], // akan diisi dari data server
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
                        // Kita akan simpan setiap soal (create/update) via API atau submit batch.
                        // Sederhananya: loop dan kirim satu per satu.
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
                            promises.push(fetch(url, {
                                method: method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            }).then(res => res.json()));
                        }
                        Promise.all(promises).then(results => {
                            let allSuccess = results.every(r => r.success);
                            if (allSuccess) {
                                window.toast.success('Semua soal berhasil disimpan');
                                location.reload(); // refresh untuk update id
                            } else {
                                window.toast.error('Ada kesalahan saat menyimpan soal');
                            }
                        }).catch(() => window.toast.error('Terjadi kesalahan'));
                    }
                }
            }
        </script>
    @endpush
@endsection
