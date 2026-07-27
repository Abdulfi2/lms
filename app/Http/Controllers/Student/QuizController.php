<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    /**
     * Menampilkan daftar quiz yang tersedia untuk student.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua course yang diikuti student
        $enrolledCourseIds = $user->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        // Ambil semua quiz dari course tersebut
        $quizzes = Quiz::with(['course', 'lesson'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Tambahkan status attempt untuk setiap quiz
        foreach ($quizzes as $quiz) {
            $lastAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            $quiz->user_attempt = $lastAttempt;
            $quiz->is_completed = $lastAttempt && $lastAttempt->status === 'completed';
            $quiz->is_passed = $lastAttempt && $lastAttempt->is_passed;
            $quiz->best_score = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->max('percentage') ?? 0;
            $quiz->attempt_count = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->count();
        }

        // Statistik
        $totalQuizzes = $quizzes->total();
        $completedQuizzes = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->distinct('quiz_id')
            ->count('quiz_id');
        $passedQuizzes = QuizAttempt::where('user_id', $user->id)
            ->where('is_passed', true)
            ->distinct('quiz_id')
            ->count('quiz_id');
        $averageScore = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;

        return view('student.quizzes.index', compact('quizzes', 'totalQuizzes', 'completedQuizzes', 'passedQuizzes', 'averageScore'));
    }

    /**
     * Menampilkan detail quiz.
     */
    public function show(Quiz $quiz)
    {
        $user = auth()->user();

        // Cek apakah student terdaftar di course ini
        $isEnrolled = $user->enrollments()
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        // Cek apakah quiz sudah dipublikasikan
        if (!$quiz->is_published) {
            abort(404, 'Quiz belum dipublikasikan.');
        }

        // Ambil attempt terakhir
        $lastAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $isCompleted = $lastAttempt && $lastAttempt->status === 'completed';
        $isPassed = $lastAttempt && $lastAttempt->is_passed;
        $bestScore = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->max('percentage') ?? 0;
        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->count();
        $remainingAttempts = max(0, $quiz->attempts_allowed - $attemptCount);

        return view('student.quizzes.show', compact('quiz', 'lastAttempt', 'isCompleted', 'isPassed', 'bestScore', 'attemptCount', 'remainingAttempts'));
    }

    /**
     * Memulai quiz attempt.
     */
    public function start(Quiz $quiz)
    {
        $user = auth()->user();

        // Check enrollment
        $isEnrolled = $user->enrollments()->where('course_id', $quiz->course_id)->exists();
        if (!$isEnrolled) {
            return redirect()->route('courses.show', $quiz->course->slug)
                ->with('error', 'Anda harus terdaftar di kursus ini untuk mengerjakan quiz.');
        }

        // Check if quiz is published
        if (!$quiz->is_published) {
            return back()->with('error', 'Quiz belum dipublikasikan.');
        }

        // Kunci baris quiz selama transaksi supaya dua request start() yang datang
        // bersamaan (double-klik/tab ganda) tidak lolos pengecekan di bawah secara paralel.
        DB::beginTransaction();
        try {
            Quiz::whereKey($quiz->id)->lockForUpdate()->first();

            // Check attempt limit
            $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->count();

            if ($attemptCount >= $quiz->attempts_allowed) {
                DB::rollBack();
                return back()->with('error', "Anda telah mencapai batas maksimal percobaan ({$quiz->attempts_allowed} kali).");
            }

            // Check for incomplete attempt
            $inProgressAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->first();

            if ($inProgressAttempt) {
                DB::rollBack();
                return redirect()->route('student.quizzes.attempt', $inProgressAttempt)
                    ->with('warning', 'Anda memiliki quiz yang belum selesai. Lanjutkan dari mana Anda berhenti.');
            }

            // Create new attempt
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'status' => 'in_progress',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Update quiz total attempts
            $quiz->increment('total_attempts');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quiz start failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal memulai quiz. Silakan coba lagi.');
        }

        Log::info('Quiz started', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'attempt_id' => $attempt->id
        ]);

        return redirect()->route('student.quizzes.attempt', $attempt)
            ->with('success', 'Quiz dimulai! Kerjakan dengan teliti.');
    }

    /**
     * Menampilkan halaman pengerjaan quiz.
     */
    public function attempt(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this quiz attempt.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', $attempt)
                ->with('info', 'Quiz ini sudah Anda selesaikan. Lihat hasil Anda di bawah.');
        }

        $quiz = $attempt->quiz;

        // Check time limit
        if ($quiz->time_limit > 0) {
            $timeElapsed = now()->diffInSeconds($attempt->started_at);
            $timeLimitSeconds = $quiz->time_limit * 60;
            if ($timeElapsed >= $timeLimitSeconds) {
                return $this->autoSubmit($attempt);
            }
            $remainingTime = $timeLimitSeconds - $timeElapsed;
        } else {
            $remainingTime = null;
        }

        $questions = $quiz->randomize_questions
            ? $quiz->questions->shuffle()
            : $quiz->questions->sortBy('order');

        return view('student.quizzes.attempt', compact('attempt', 'quiz', 'questions', 'remainingTime'));
    }

    /**
     * Menyimpan jawaban quiz.
     */
    public function submit(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', $attempt)
                ->with('warning', 'Quiz ini sudah diselesaikan sebelumnya.');
        }

        $user = auth()->user();
        $quiz = $attempt->quiz;
        $answers = $request->input('answers', []);
        $totalPoints = 0;
        $earnedPoints = 0;
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions()->count();

        // Waktu selalu dihitung dari server, bukan dari input client, dan batas waktu
        // ditegakkan di sini juga (bukan hanya saat halaman attempt() dibuka ulang) —
        // supaya siswa tidak bisa mengerjakan tanpa batas waktu selama tidak reload halaman.
        $timeSpent = now()->diffInSeconds($attempt->started_at);
        if ($quiz->time_limit > 0 && $timeSpent > ($quiz->time_limit * 60) + 30) {
            // Waktu sudah habis di server: perlakukan seperti auto-submit (jawaban dianggap kosong)
            $answers = [];
        }

        DB::beginTransaction();

        // Kunci baris attempt untuk mencegah submit() ganda (double-klik) memproses
        // jawaban dan mengubah skor dua kali secara paralel.
        $attempt = QuizAttempt::whereKey($attempt->id)->lockForUpdate()->first();
        if ($attempt->status !== 'in_progress') {
            DB::rollBack();
            return redirect()->route('student.quizzes.result', $attempt)
                ->with('warning', 'Quiz ini sudah diselesaikan sebelumnya.');
        }

        try {
            foreach ($answers as $questionId => $answerData) {
                $question = $quiz->questions()->find($questionId);
                if (!$question)
                    continue;

                $selectedOptionId = null;
                $isCorrect = false;
                $pointsEarned = 0;
                $answerText = null;

                if ($question->type === 'multiple_choice' && isset($answerData['option_id'])) {
                    $option = $question->options()->find($answerData['option_id']);
                    if ($option) {
                        $selectedOptionId = $option->id;
                        $isCorrect = $option->is_correct;
                        $pointsEarned = $isCorrect ? $question->points : 0;
                        if ($isCorrect)
                            $correctAnswers++;
                    }
                } elseif ($question->type === 'true_false') {
                    $userAnswer = $answerData['value'] ?? ($answerData === 'true' ? 'true' : 'false');
                    $option = $question->options()->where('option_text', $userAnswer === 'true' ? 'Benar' : 'Salah')->first();
                    if ($option) {
                        $selectedOptionId = $option->id;
                        $isCorrect = $option->is_correct;
                        $pointsEarned = $isCorrect ? $question->points : 0;
                        if ($isCorrect)
                            $correctAnswers++;
                    }
                } elseif ($question->type === 'essay') {
                    $answerText = is_string($answerData) ? $answerData : ($answerData['text'] ?? null);
                    $pointsEarned = 0; // Akan dinilai manual oleh instructor
                }

                $totalPoints += $question->points;
                $earnedPoints += $pointsEarned;

                QuizAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'selected_option_id' => $selectedOptionId,
                    'answer_text' => $answerText,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ]);
            }

            // Hitung persentase
            $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            $isPassed = $percentage >= $quiz->passing_score;

            // Update attempt
            $attempt->update([
                'completed_at' => now(),
                'time_spent' => $timeSpent,
                'score' => $earnedPoints,
                'percentage' => $percentage,
                'is_passed' => $isPassed,
                'status' => 'completed',
            ]);

            // Update quiz average score
            $quiz->update([
                'average_score' => QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('status', 'completed')
                    ->avg('percentage') ?? 0
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quiz submit failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan jawaban quiz. Silakan coba lagi.');
        }

        // Gamification: award points if passed
        if ($isPassed) {
            try {
                GamificationService::quizPassed($user, $quiz->id, $percentage);
            } catch (\Exception $e) {
                Log::warning('Gamification error: ' . $e->getMessage());
            }
        }

        Log::info('Quiz completed', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'attempt_id' => $attempt->id,
            'score' => $percentage,
            'passed' => $isPassed
        ]);

        // Update lesson progress if this quiz is part of a lesson
        if ($quiz->lesson_id) {
            $this->updateLessonProgress($user, $quiz);
        }

        $message = $isPassed
            ? "🎉 Selamat! Anda lulus quiz dengan nilai {$percentage}%."
            : "📚 Nilai Anda {$percentage}%. Perlu {$quiz->passing_score}% untuk lulus. Silakan pelajari lagi materinya.";

        return redirect()->route('student.quizzes.result', $attempt)
            ->with('success', $message);
    }

    /**
     * Menampilkan hasil quiz.
     */
    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $attempt->load(['quiz', 'answers.question.options']);

        $questions = $attempt->quiz->questions()->with('options')->get();
        $answers = $attempt->answers->keyBy('question_id');
        $correctCount = $attempt->answers->where('is_correct', true)->count();
        $totalQuestions = $questions->count();
        $score = $attempt->percentage ?? 0;

        return view('student.quizzes.result', compact('attempt', 'questions', 'answers', 'correctCount', 'totalQuestions', 'score'));
    }

    /**
     * Auto-submit quiz when time limit is reached.
     */
    protected function autoSubmit(QuizAttempt $attempt)
    {
        $answers = [];
        foreach ($attempt->quiz->questions as $question) {
            $answers[$question->id] = null;
        }

        $request = new Request(['answers' => $answers, 'time_spent' => $attempt->quiz->time_limit * 60]);

        return $this->submit($request, $attempt);
    }

    /**
     * Update lesson progress after quiz completion.
     */
    protected function updateLessonProgress($user, $quiz)
    {
        if (!$quiz->lesson_id)
            return;

        $lesson = $quiz->lesson;
        $course = $lesson->section->course;

        $completed = \App\Models\LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        if (!$completed) {
            \App\Models\LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'is_completed' => true,
                'completed_at' => now(),
                'time_spent' => 0,
            ]);

            // Update course progress
            $enrollment = \App\Models\Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment) {
                $totalLessons = $course->lessons()->count();
                $completedLessons = \App\Models\LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $course->lessons()->pluck('id'))
                    ->count();

                $progress = ($completedLessons / max($totalLessons, 1)) * 100;
                $enrollment->update(['progress' => $progress]);
            }
        }
    }
}