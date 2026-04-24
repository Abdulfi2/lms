<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        $quizzes = $course->quizzes()->orderBy('created_at')->paginate(10);
        return view('instructor.quizzes.index', compact('course', 'quizzes'));
    }

    public function create(Course $course)
    {
        if ($course->instructor_id !== auth()->id())
            abort(403);
        return view('instructor.quizzes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
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
        $quiz->load('questions.options');
        return view('instructor.quizzes.edit', compact('course', 'quiz'));
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $quiz->update($request->all());
        return back()->with('success', 'Quiz updated.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('instructor.courses.quizzes.index', $course)->with('success', 'Quiz deleted.');
    }

    // Question management
    public function storeQuestion(Request $request, Course $course, Quiz $quiz)
    {
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
        // similar update logic
    }

    public function deleteQuestion(Course $course, Quiz $quiz, QuizQuestion $question)
    {
        $question->delete();
        return response()->json(['success' => true]);
    }
}