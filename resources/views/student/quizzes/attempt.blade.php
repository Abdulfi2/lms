@extends('layouts.app')

@section('title', 'Mengerjakan Quiz - ' . $quiz->title)
@section('page-title', $quiz->title)

@section('content')
    <div x-data="quizAttempt()" x-init="init()" class="max-w-4xl mx-auto">
        <!-- Timer -->
        @if ($remainingTime)
            <div class="fixed top-20 right-4 z-40">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 border-l-4 border-primary">
                    <div class="text-xs text-gray-500">Sisa Waktu</div>
                    <div class="text-2xl font-bold font-mono" x-text="formatTime(timeLeft)"></div>
                </div>
            </div>
        @endif

        <!-- Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Progress</span>
                <span x-text="`${currentQuestion + 1} dari ${questions.length} soal`"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all"
                    :style="`width: ${((currentQuestion + 1) / questions.length) * 100}%`"></div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            @foreach ($questions as $index => $q)
                <div x-show="currentQuestion === {{ $index }}" x-transition.duration.300 class="p-6">
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Soal {{ $index + 1 }} dari
                                {{ $questions->count() }}</span>
                            <span class="text-sm font-semibold text-primary">{{ $q->points }} poin</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{!! $q->question !!}</h3>
                    </div>

                    <!-- Multiple Choice -->
                    @if ($q->type === 'multiple_choice')
                        <div class="space-y-3 mt-4">
                            @foreach ($q->options as $optIdx => $option)
                                <label
                                    class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <input type="radio" name="answers[{{ $q->id }}][option_id]"
                                        value="{{ $option->id }}" x-model="answers[{{ $q->id }}].option_id"
                                        class="mt-0.5 mr-3">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <!-- True/False -->
                    @if ($q->type === 'true_false')
                        <div class="space-y-3 mt-4">
                            <label
                                class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="answers[{{ $q->id }}][value]" value="true"
                                    x-model="answers[{{ $q->id }}].value" class="mt-0.5 mr-3">
                                <span class="text-gray-700 dark:text-gray-300">Benar</span>
                            </label>
                            <label
                                class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <input type="radio" name="answers[{{ $q->id }}][value]" value="false"
                                    x-model="answers[{{ $q->id }}].value" class="mt-0.5 mr-3">
                                <span class="text-gray-700 dark:text-gray-300">Salah</span>
                            </label>
                        </div>
                    @endif

                    <!-- Essay -->
                    @if ($q->type === 'essay')
                        <div class="mt-4">
                            <textarea name="answers[{{ $q->id }}][text]" rows="6" x-model="answers[{{ $q->id }}].text"
                                class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary"
                                placeholder="Tulis jawaban Anda di sini..."></textarea>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Navigation Buttons -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-between">
                <button @click="prevQuestion" x-show="currentQuestion > 0"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    ← Sebelumnya
                </button>
                <button @click="nextQuestion" x-show="currentQuestion < questions.length - 1"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
                    Selanjutnya →
                </button>
                <button @click="submitQuiz" x-show="currentQuestion === questions.length - 1" :disabled="submitting"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition">
                    <span x-show="!submitting">Submit Quiz</span>
                    <span x-show="submitting">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function quizAttempt() {
                return {
                    questions: @json($questions),
                    answers: {},
                    currentQuestion: 0,
                    submitting: false,
                    timeLeft: {{ $remainingTime ?? 0 }},
                    timer: null,

                    init() {
                        // Initialize answers array
                        this.questions.forEach(q => {
                            this.answers[q.id] = {};
                        });

                        // Start timer if time limit exists
                        if (this.timeLeft > 0) {
                            this.startTimer();
                        }
                    },

                    startTimer() {
                        this.timer = setInterval(() => {
                            if (this.timeLeft <= 1) {
                                clearInterval(this.timer);
                                this.submitQuiz();
                            } else {
                                this.timeLeft--;
                            }
                        }, 1000);
                    },

                    formatTime(seconds) {
                        const minutes = Math.floor(seconds / 60);
                        const secs = seconds % 60;
                        return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    },

                    prevQuestion() {
                        if (this.currentQuestion > 0) {
                            this.currentQuestion--;
                        }
                    },

                    nextQuestion() {
                        if (this.currentQuestion < this.questions.length - 1) {
                            this.currentQuestion++;
                        }
                    },

                    submitQuiz() {
                        if (this.submitting) return;
                        if (confirm('Apakah Anda yakin ingin mengumpulkan quiz? Jawaban tidak dapat diubah lagi.')) {
                            this.submitting = true;
                            if (this.timer) clearInterval(this.timer);

                            fetch('{{ route('student.quizzes.submit', $attempt) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        answers: this.answers,
                                        time_spent: {{ $remainingTime ? $quiz->time_limit * 60 : 0 }} - this.timeLeft
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.href = data.redirect ||
                                            '{{ route('student.quizzes.result', $attempt) }}';
                                    } else {
                                        window.toast.error(data.message);
                                        this.submitting = false;
                                    }
                                })
                                .catch(() => {
                                    window.toast.error('Terjadi kesalahan');
                                    this.submitting = false;
                                });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
