<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $quizzes = $course->quizzes()->orderBy('created_at')->paginate(10);

        foreach ($quizzes as $quiz) {
            $quiz->pending_essay_count = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('status', 'completed')
                ->whereHas('answers', function ($q) {
                    $q->whereHas('question', fn ($qq) => $qq->where('type', 'essay'))
                        ->whereNull('graded_at');
                })
                ->count();
        }

        return view('instructor.quizzes.index', compact('course', 'quizzes'));
    }

    /**
     * Analitik per-soal: soal mana yang paling sering dijawab salah, dan distribusi pilihan jawaban siswa.
     */
    public function analytics(Course $course, Quiz $quiz)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $this->authorizeQuiz($course, $quiz);

        $totalAttempts = $quiz->attempts()->where('status', 'completed')->count();

        $questions = $quiz->questions()->with('options')->orderBy('order')->get()->map(function ($question) {
            $answered = $question->answers()->count();
            $correct = $question->answers()->where('is_correct', true)->count();

            $optionBreakdown = collect();
            if ($question->type !== 'essay') {
                $optionBreakdown = $question->options->map(function ($option) use ($question) {
                    return [
                        'text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'selected_count' => $question->answers()->where('selected_option_id', $option->id)->count(),
                    ];
                });
            }

            return [
                'question' => $question,
                'answered' => $answered,
                'correct' => $correct,
                'correct_rate' => $answered > 0 ? round(($correct / $answered) * 100) : null,
                'options' => $optionBreakdown,
            ];
        })->sortBy('correct_rate');

        return view('instructor.quizzes.analytics', compact('course', 'quiz', 'questions', 'totalAttempts'));
    }

    /**
     * Daftar attempt yang punya jawaban essay yang belum dinilai instruktur.
     */
    public function grading(Course $course, Quiz $quiz)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $this->authorizeQuiz($course, $quiz);

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->whereHas('answers', function ($q) {
                $q->whereHas('question', fn ($qq) => $qq->where('type', 'essay'))
                    ->whereNull('graded_at');
            })
            ->with('user')
            ->orderBy('completed_at')
            ->get();

        $gradedCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->whereHas('answers', fn ($q) => $q->whereHas('question', fn ($qq) => $qq->where('type', 'essay')))
            ->whereDoesntHave('answers', function ($q) {
                $q->whereHas('question', fn ($qq) => $qq->where('type', 'essay'))
                    ->whereNull('graded_at');
            })
            ->count();

        return view('instructor.quizzes.grading', compact('course', 'quiz', 'attempts', 'gradedCount'));
    }

    /**
     * Form penilaian manual soal essay untuk satu attempt.
     */
    public function gradeAttempt(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $this->authorizeQuiz($course, $quiz);
        abort_unless($attempt->quiz_id === $quiz->id, 404);

        $attempt->load(['user', 'answers.question']);
        $essayAnswers = $attempt->answers->filter(fn ($a) => $a->question->type === 'essay');

        return view('instructor.quizzes.grade-attempt', compact('course', 'quiz', 'attempt', 'essayAnswers'));
    }

    /**
     * Simpan penilaian soal essay, lalu hitung ulang skor total attempt.
     */
    public function storeGrade(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $this->authorizeQuiz($course, $quiz);
        abort_unless($attempt->quiz_id === $quiz->id, 404);

        $request->validate([
            'grades' => 'required|array',
            'grades.*.points_earned' => 'required|integer|min:0',
            'grades.*.feedback' => 'nullable|string',
        ]);

        $wasPassed = $attempt->is_passed;
        $isPassed = $wasPassed;

        DB::beginTransaction();
        try {
            foreach ($request->grades as $answerId => $data) {
                $answer = QuizAnswer::where('id', $answerId)->where('attempt_id', $attempt->id)->first();
                if (!$answer || $answer->question->type !== 'essay') {
                    continue;
                }

                $points = min((int) $data['points_earned'], $answer->question->points);

                $answer->update([
                    'points_earned' => $points,
                    'feedback' => $data['feedback'] ?? null,
                    'graded_at' => now(),
                ]);
            }

            // Hitung ulang skor total dari SEMUA jawaban (bukan cuma essay), supaya
            // konsisten dengan cara submit() menghitung skor pertama kali.
            $totalPoints = $quiz->questions()->sum('points');
            $earnedPoints = $attempt->answers()->sum('points_earned');
            $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            $isPassed = $percentage >= $quiz->passing_score;

            $attempt->update([
                'score' => $earnedPoints,
                'percentage' => $percentage,
                'is_passed' => $isPassed,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quiz grading failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan penilaian.');
        }

        // Jika attempt baru lulus SETELAH dinilai (sebelumnya belum lulus karena
        // menunggu poin essay), beri poin gamifikasi sekarang juga.
        if (!$wasPassed && $isPassed) {
            try {
                GamificationService::quizPassed($attempt->user, $quiz->id, $attempt->percentage);
            } catch (\Exception $e) {
                Log::warning('Gamification error: ' . $e->getMessage());
            }
        }

        return redirect()->route('instructor.courses.quizzes.grading', [$course, $quiz])
            ->with('success', 'Penilaian soal essay berhasil disimpan.');
    }

    public function create(Course $course)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        return view('instructor.quizzes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:0',
            'attempts_allowed' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'randomize_questions' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz = $course->quizzes()->create($request->all());
        return redirect()->route('instructor.courses.quizzes.edit', [$course, $quiz])->with('success', 'Quiz created. Add questions.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $this->authorizeQuiz($course, $quiz);
        $quiz->load('questions.options');
        return view('instructor.quizzes.edit', compact('course', 'quiz'));
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $this->authorizeQuiz($course, $quiz);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:0',
            'attempts_allowed' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'randomize_questions' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz->update($request->all());
        return back()->with('success', 'Quiz updated.');
    }

    public function destroy(Request $request, Course $course, Quiz $quiz)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $this->authorizeQuiz($course, $quiz);

        $quiz->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Quiz berhasil dihapus.']);
        }

        return redirect()->route('instructor.courses.quizzes.index', $course)->with('success', 'Quiz deleted.');
    }

    public function togglePublish(Request $request, Course $course, Quiz $quiz)
    {
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $this->authorizeQuiz($course, $quiz);

        $quiz->update(['is_published' => $request->boolean('is_published')]);

        return response()->json([
            'success' => true,
            'message' => $quiz->is_published ? 'Quiz berhasil dipublikasikan.' : 'Quiz diubah menjadi draft.',
        ]);
    }

    // Question management
    public function storeQuestion(Request $request, Course $course, Quiz $quiz)
    {
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $this->authorizeQuiz($course, $quiz);

        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,essay',
            'points' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        $question = $quiz->questions()->create($request->only('question', 'type', 'points', 'explanation'));
        if ($request->type === 'multiple_choice') {
            foreach ($request->options as $opt) {
                $question->options()->create([
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['is_correct'] ?? false,
                    'points' => $opt['points'] ?? 0,
                ]);
            }
        } elseif ($request->type === 'true_false') {
            $question->options()->createMany([
                ['option_text' => 'Benar', 'is_correct' => $request->correct_answer === 'true'],
                ['option_text' => 'Salah', 'is_correct' => $request->correct_answer === 'false'],
            ]);
        }
        DB::commit();

        return response()->json(['success' => true, 'question' => $question->load('options')]);
    }

    public function updateQuestion(Request $request, Course $course, Quiz $quiz, QuizQuestion $question)
    {
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $this->authorizeQuiz($course, $quiz);

        if ($question->quiz_id !== $quiz->id) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan pada quiz ini.'], 404);
        }

        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,essay',
            'points' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        $question->update($request->only('question', 'type', 'points', 'explanation'));

        // Ganti seluruh opsi lama dengan yang baru — frontend selalu mengirim daftar opsi lengkap.
        $question->options()->delete();
        if ($request->type === 'multiple_choice') {
            foreach ($request->options as $opt) {
                $question->options()->create([
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['is_correct'] ?? false,
                    'points' => $opt['points'] ?? 0,
                ]);
            }
        } elseif ($request->type === 'true_false') {
            $question->options()->createMany([
                ['option_text' => 'Benar', 'is_correct' => $request->correct_answer === 'true'],
                ['option_text' => 'Salah', 'is_correct' => $request->correct_answer === 'false'],
            ]);
        }
        DB::commit();

        return response()->json(['success' => true, 'question' => $question->load('options')]);
    }

    public function deleteQuestion(Course $course, Quiz $quiz, QuizQuestion $question)
    {
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $this->authorizeQuiz($course, $quiz);

        if ($question->quiz_id !== $quiz->id) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan pada quiz ini.'], 404);
        }

        $question->delete();
        return response()->json(['success' => true]);
    }

    private function authorizeQuiz(Course $course, Quiz $quiz): void
    {
        abort_unless($quiz->course_id === $course->id, 404);
    }
}
