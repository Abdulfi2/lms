<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function index(Course $course, Section $section)
    {
        $lessons = $section->lessons()->orderBy('order')->get();
        return view('admin.courses.sections.lessons.index', compact('course', 'section', 'lessons'));
    }

    public function create(Course $course, Section $section)
    {
        return view('admin.courses.sections.lessons.create', compact('course', 'section'));
    }

    public function store(Request $request, Course $course, Section $section)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz,assignment,live,discussion',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'video_url' => 'nullable|url',
            'is_free_preview' => 'nullable|boolean',
            'points' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
        ]);

        try {
            DB::beginTransaction();

            $maxOrder = $section->lessons()->max('order') ?? 0;
            $lesson = $section->lessons()->create([
                'title' => $request->title,
                'type' => $request->type,
                'content' => $request->input('content'),
                'duration' => $request->duration ?? 0,
                'order' => $maxOrder + 1,
                'video_url' => $request->video_url,
                'is_free_preview' => $request->has('is_free_preview'),
                'points' => $request->points ?? 0,
                'status' => $request->status,
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Lesson berhasil ditambahkan.', 'data' => $lesson]);
            }

            return redirect()->route('admin.courses.sections.lessons.index', [$course, $section])
                ->with('success', 'Lesson berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambah lesson: ' . $e->getMessage());
        }
    }

    public function edit(Course $course, Section $section, Lesson $lesson)
    {
        return view('admin.courses.sections.lessons.edit', compact('course', 'section', 'lesson'));
    }

    public function update(Request $request, Course $course, Section $section, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz,assignment,live,discussion',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'video_url' => 'nullable|url',
            'is_free_preview' => 'nullable|boolean',
            'points' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
        ]);

        try {
            DB::beginTransaction();

            $lesson->update([
                'title' => $request->title,
                'type' => $request->type,
                'content' => $request->input('content'),
                'duration' => $request->duration ?? 0,
                'video_url' => $request->video_url,
                'is_free_preview' => $request->has('is_free_preview'),
                'points' => $request->points ?? 0,
                'status' => $request->status,
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Lesson berhasil diperbarui.']);
            }

            return redirect()->route('admin.courses.sections.lessons.index', [$course, $section])
                ->with('success', 'Lesson berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui lesson: ' . $e->getMessage());
        }
    }

    public function destroy(Course $course, Section $section, Lesson $lesson)
    {
        try {
            DB::beginTransaction();
            $lesson->delete();
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Lesson berhasil dihapus.']);
            }
            return redirect()->route('admin.courses.sections.lessons.index', [$course, $section])
                ->with('success', 'Lesson berhasil dihapus.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus lesson.'], 500);
        }
    }

    public function updateOrder(Request $request, Course $course, Section $section)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*' => 'exists:lessons,id',
        ]);

        try {
            foreach ($request->lessons as $index => $lessonId) {
                Lesson::where('id', $lessonId)->update(['order' => $index + 1]);
            }
            return response()->json(['success' => true, 'message' => 'Urutan lesson diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate urutan.']);
        }
    }
}