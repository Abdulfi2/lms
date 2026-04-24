<?php
// app/Http/Controllers/Instructor/LessonController.php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Display lessons of a section.
     */
    public function index(Course $course, Section $section)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($section->course_id !== $course->id) {
            abort(404);
        }

        $lessons = $section->lessons()->orderBy('order')->get();
        return view('instructor.lessons.index', compact('course', 'section', 'lessons'));
    }

    /**
     * Show form to create a new lesson.
     */
    public function create(Course $course, Section $section)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($section->course_id !== $course->id) {
            abort(404);
        }
        return view('instructor.lessons.create', compact('course', 'section'));
    }

    /**
     * Store a new lesson.
     */
    public function store(Request $request, Course $course, Section $section)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($section->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz,assignment,live,discussion',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'video_url' => 'nullable|url',
            'attachment' => 'nullable|file|mimes:pdf,zip,mp4|max:10240',
            'is_free_preview' => 'nullable|boolean',
            'points' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
        ]);

        try {
            DB::beginTransaction();

            $maxOrder = $section->lessons()->max('order') ?? 0;
            $data = $request->except(['attachment']);
            $data['order'] = $maxOrder + 1;
            $data['is_free_preview'] = $request->has('is_free_preview');

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('lessons/attachments', 'public');
                $data['attachment'] = $path;
                $data['is_downloadable'] = true;
            }

            $lesson = $section->lessons()->create($data);

            DB::commit();

            return redirect()->route('instructor.courses.sections.lessons.index', [$course, $section])
                ->with('success', 'Lesson berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambah lesson: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit a lesson.
     */
    public function edit(Course $course, Section $section, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($section->course_id !== $course->id || $lesson->section_id !== $section->id) {
            abort(404);
        }
        return view('instructor.lessons.edit', compact('course', 'section', 'lesson'));
    }

    /**
     * Update a lesson.
     */
    public function update(Request $request, Course $course, Section $section, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($section->course_id !== $course->id || $lesson->section_id !== $section->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz,assignment,live,discussion',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'video_url' => 'nullable|url',
            'attachment' => 'nullable|file|mimes:pdf,zip,mp4|max:10240',
            'is_free_preview' => 'nullable|boolean',
            'points' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['attachment', '_method', '_token']);
            $data['is_free_preview'] = $request->has('is_free_preview');

            if ($request->hasFile('attachment')) {
                if ($lesson->attachment && Storage::disk('public')->exists($lesson->attachment)) {
                    Storage::disk('public')->delete($lesson->attachment);
                }
                $path = $request->file('attachment')->store('lessons/attachments', 'public');
                $data['attachment'] = $path;
                $data['is_downloadable'] = true;
            }

            $lesson->update($data);

            DB::commit();

            return redirect()->route('instructor.courses.sections.lessons.index', [$course, $section])
                ->with('success', 'Lesson berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui lesson: ' . $e->getMessage());
        }
    }

    /**
     * Delete a lesson.
     */
    public function destroy(Course $course, Section $section, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($section->course_id !== $course->id || $lesson->section_id !== $section->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();
            if ($lesson->attachment && Storage::disk('public')->exists($lesson->attachment)) {
                Storage::disk('public')->delete($lesson->attachment);
            }
            $lesson->delete();
            DB::commit();

            return redirect()->route('instructor.courses.sections.lessons.index', [$course, $section])
                ->with('success', 'Lesson berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus lesson.');
        }
    }

    /**
     * Update order of lessons (drag & drop).
     */
    public function updateOrder(Request $request, Course $course, Section $section)
    {
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false], 403);
        }
        if ($section->course_id !== $course->id) {
            return response()->json(['success' => false], 404);
        }

        $request->validate(['lessons' => 'required|array']);

        try {
            foreach ($request->lessons as $index => $id) {
                Lesson::where('id', $id)->where('section_id', $section->id)->update(['order' => $index + 1]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
}