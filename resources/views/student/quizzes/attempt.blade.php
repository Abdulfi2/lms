@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
    <div x-data="quizTimer({{ $timeLimit }})" x-init="initTimer()" class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex justify-between border-b pb-4">
                <h1 class="text-2xl font-bold">{{ $quiz->title }}</h1>
                <div class="text-lg font-mono">Time Left: <span x-text="formattedTime"></span></div>
            </div>
            <form method="POST" action="{{ route('student.quizzes.submit', $attempt) }}" x-ref="quizForm">
                @csrf
                @foreach ($questions as $index => $q)
                    <div class="mt-6 p-4 border rounded">
                        <p class="font-semibold">{{ $index + 1 }}. {{ $q->question }} ({{ $q->points }} pts)</p>
                        @if ($q->type == 'multiple_choice')
                            @foreach ($q->options as $opt)
                                <label class="block mt-2"><input type="radio"
                                        name="answers[{{ $q->id }}][option_id]" value="{{ $opt->id }}">
                                    {{ $opt->option_text }}</label>
                            @endforeach
                        @elseif($q->type == 'true_false')
                            <label class="block mt-2"><input type="radio" name="answers[{{ $q->id }}][value]"
                                    value="true"> Benar</label>
                            <label class="block"><input type="radio" name="answers[{{ $q->id }}][value]"
                                    value="false"> Salah</label>
                        @else
                            <textarea name="answers[{{ $q->id }}]" rows="3" class="w-full rounded mt-2" placeholder="Jawaban Anda..."></textarea>
                        @endif
                    </div>
                @endforeach
                <input type="hidden" name="time_spent" x-model="timeSpent">
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded">Submit Quiz</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function quizTimer(limitMinutes) {
                return {
                    totalSeconds: limitMinutes * 60,
                    timeSpent: 0,
                    interval: null,
                    get formattedTime() {
                        let mins = Math.floor(this.totalSeconds / 60);
                        let secs = this.totalSeconds % 60;
                        return `${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`;
                    },
                    initTimer() {
                        if (this.totalSeconds <= 0) return;
                        this.interval = setInterval(() => {
                            if (this.totalSeconds <= 1) {
                                clearInterval(this.interval);
                                this.$refs.quizForm.submit();
                            } else {
                                this.totalSeconds--;
                                this.timeSpent++;
                            }
                        }, 1000);
                    }
                }
            }
        </script>
    @endpush
@endsection
