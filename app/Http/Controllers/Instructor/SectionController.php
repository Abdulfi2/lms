<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    /**
     * Display all sections of a course.
     */
    public function index(Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $sections = $course->sections()->orderBy('order')->get();
        return view('instructor.sections.index', compact('course', 'sections'));
    }

    /**
     * Show form to create a new section.
     */
    public function create(Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('instructor.sections.create', compact('course'));
    }

    /**
     * Store a new section.
     */
    public function store(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $maxOrder = $course->sections()->max('order') ?? 0;
            $section = $course->sections()->create([
                'title' => $request->title,
                'description' => $request->description,
                'order' => $maxOrder + 1,
                'is_published' => $request->has('is_published'),
            ]);

            DB::commit();

            return redirect()->route('instructor.courses.sections.index', $course)
                ->with('success', 'Section berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambah section: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit a section.
     */
    public function edit(Course $course, Section $section)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        if ($section->course_id !== $course->id) {
            abort(404);
        }
        return view('instructor.sections.edit', compact('course', 'section'));
    }

    /**
     * Update a section.
     */
    public function update(Request $request, Course $course, Section $section)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        if ($section->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $section->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_published' => $request->has('is_published'),
            ]);

            DB::commit();

            return redirect()->route('instructor.courses.sections.index', $course)
                ->with('success', 'Section berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui section: ' . $e->getMessage());
        }
    }

    /**
     * Delete a section.
     */
    public function destroy(Request $request, Course $course, Section $section)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        if ($section->course_id !== $course->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();
            $section->delete();
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Section berhasil dihapus.']);
            }

            return redirect()->route('instructor.courses.sections.index', $course)
                ->with('success', 'Section berhasil dihapus.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus section.'], 500);
            }
            return back()->with('error', 'Gagal menghapus section.');
        }
    }

    /**
     * Update order of sections (drag & drop).
     */
    public function updateOrder(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate(['sections' => 'required|array']);

        try {
            foreach ($request->sections as $index => $id) {
                Section::where('id', $id)->where('course_id', $course->id)->update(['order' => $index + 1]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
}