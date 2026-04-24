<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function start(Quiz $quiz)
    {
        $enrolled = auth()->user()->enrollments()->where('course_id', $quiz->course_id)->exists();
        if (!$enrolled)
            abort(403);

        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', auth()->id())->count();
        if ($attemptCount >= $quiz->attempts_allowed) {
            return back()->with('error', 'You have reached maximum attempts.');
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('student.quizzes.attempt', $attempt);
    }

    public function attempt(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id())
            abort(403);
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', $attempt);
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->randomize_questions ? $quiz->questions->shuffle() : $quiz->questions;
        $timeLimit = $quiz->time_limit;

        return view('student.quizzes.attempt', compact('attempt', 'quiz', 'questions', 'timeLimit'));
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id())
            abort(403);
        if ($attempt->status !== 'in_progress')
            abort(400);

        $answers = $request->input('answers', []);
        $totalPoints = 0;
        $earnedPoints = 0;

        DB::beginTransaction();
        foreach ($answers as $questionId => $answer) {
            $question = $attempt->quiz->questions()->find($questionId);
            if (!$question)
                continue;

            $selectedOptionId = null;
            $isCorrect = false;
            $pointsEarned = 0;

            if ($question->type === 'multiple_choice' && isset($answer['option_id'])) {
                $option = $question->options()->find($answer['option_id']);
                if ($option) {
                    $selectedOptionId = $option->id;
                    $isCorrect = $option->is_correct;
                    $pointsEarned = $isCorrect ? $question->points : 0;
                }
            } elseif ($question->type === 'true_false') {
                $option = $question->options()->where('option_text', $answer['value'] === 'true' ? 'Benar' : 'Salah')->first();
                if ($option) {
                    $selectedOptionId = $option->id;
                    $isCorrect = $option->is_correct;
                    $pointsEarned = $isCorrect ? $question->points : 0;
                }
            } elseif ($question->type === 'essay') {
                // Essay points manual later, store text
                $pointsEarned = 0;
                $isCorrect = false;
            }

            $totalPoints += $question->points;
            $earnedPoints += $pointsEarned;

            \App\Models\QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'selected_option_id' => $selectedOptionId,
                'answer_text' => is_string($answer) ? $answer : null,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);
        }

        $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $isPassed = $percentage >= $attempt->quiz->passing_score;

        $attempt->update([
            'completed_at' => now(),
            'time_spent' => $request->time_spent ?? 0,
            'score' => $earnedPoints,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'status' => 'completed',
        ]);

        DB::commit();

        // Update enrollment progress? Bisa dilakukan di hook atau event.

        return redirect()->route('student.quizzes.result', $attempt);
    }

    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id())
            abort(403);
        $attempt->load('answers.question.options');

        return view('student.quizzes.result', compact('attempt'));
    }
}