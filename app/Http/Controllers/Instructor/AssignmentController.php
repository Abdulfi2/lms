<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Jobs\SendAssignmentNotificationJob;
use App\Models\Assignment;
use App\Models\AssignmentRubric;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Tampilkan semua tugas untuk kursus yang diajar oleh instruktur.
     */
    public function index()
    {
        $instructorId = Auth::id();
        
        $assignments = Assignment::with(['course', 'lesson'])
            ->whereHas('course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            })
            ->latest()
            ->paginate(10);

        return view('instructor.assignments.index', compact('assignments'));
    }

    /**
     * Tampilkan form pembuatan tugas.
     */
    public function create()
    {
        $courses = Course::where('instructor_id', Auth::id())->get();
        return view('instructor.assignments.create', compact('courses'));
    }

    /**
     * Simpan tugas baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'max_score' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|lte:max_score',
            'due_date' => 'nullable|date|after:now',
            'is_published' => 'boolean',
            'allow_late_submission' => 'boolean',
            'late_penalty' => 'nullable|integer|min:0|max:100',
            'rubric' => 'nullable|array',
            'rubric.*.criteria' => 'required_with:rubric|string|max:255',
            'rubric.*.description' => 'nullable|string|max:1000',
            'rubric.*.max_points' => 'required_with:rubric|integer|min:1',
        ]);

        $course = Course::findOrFail($request->course_id);
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->except('rubric');
        $data['is_published'] = $request->has('is_published');
        $data['allow_late_submission'] = $request->has('allow_late_submission');

        $assignment = Assignment::create($data);
        $this->syncRubric($assignment, $request->input('rubric', []));

        if ($assignment->is_published) {
            $students = User::whereHas('enrollments', function ($q) use ($course) {
                $q->where('course_id', $course->id)->where('status', 'active');
            })->get();

            if ($students->isNotEmpty()) {
                SendAssignmentNotificationJob::dispatch($assignment, $students);
            }
        }

        return redirect()->route('instructor.assignments.index')
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit tugas.
     */
    public function edit(Assignment $assignment)
    {
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $courses = Course::where('instructor_id', Auth::id())->get();
        $lessons = Lesson::whereHas('section', function ($q) use ($assignment) {
            $q->where('course_id', $assignment->course_id);
        })->get();

        return view('instructor.assignments.edit', compact('assignment', 'courses', 'lessons'));
    }

    /**
     * Perbarui data tugas di database.
     */
    public function update(Request $request, Assignment $assignment)
    {
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'max_score' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|lte:max_score',
            'due_date' => 'nullable|date',
            'is_published' => 'boolean',
            'allow_late_submission' => 'boolean',
            'late_penalty' => 'nullable|integer|min:0|max:100',
            'rubric' => 'nullable|array',
            'rubric.*.criteria' => 'required_with:rubric|string|max:255',
            'rubric.*.description' => 'nullable|string|max:1000',
            'rubric.*.max_points' => 'required_with:rubric|integer|min:1',
        ]);

        $data = $request->except('rubric');
        $data['is_published'] = $request->has('is_published');
        $data['allow_late_submission'] = $request->has('allow_late_submission');

        $assignment->update($data);
        $this->syncRubric($assignment, $request->input('rubric', []));

        return redirect()->route('instructor.assignments.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Ganti seluruh kriteria rubrik milik assignment — frontend selalu mengirim daftar lengkap.
     */
    private function syncRubric(Assignment $assignment, array $rubric): void
    {
        $assignment->rubricItems()->delete();

        foreach ($rubric as $index => $item) {
            if (empty($item['criteria']) || empty($item['max_points'])) {
                continue;
            }

            AssignmentRubric::create([
                'assignment_id' => $assignment->id,
                'criteria' => $item['criteria'],
                'description' => $item['description'] ?? null,
                'max_points' => $item['max_points'],
                'order' => $index,
            ]);
        }
    }

    /**
     * Hapus tugas.
     */
    public function destroy(Assignment $assignment)
    {
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $assignment->delete();

        return redirect()->route('instructor.assignments.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Mendapatkan daftar lesson berdasarkan course (untuk AJAX).
     */
    public function getLessons(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            return response()->json([], 403);
        }

        return response()->json($course->lessons()->orderBy('order')->get(['id', 'title']));
    }
}
