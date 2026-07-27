<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function index(Course $course)
    {
        $sections = $course->sections()->orderBy('order')->get();
        return view('admin.courses.sections.index', compact('course', 'sections'));
    }

    public function create(Course $course)
    {
        return view('admin.courses.sections.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
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

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Section berhasil ditambahkan.', 'data' => $section]);
            }

            return redirect()->route('admin.sections.index', $course)
                ->with('success', 'Section berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambah section: ' . $e->getMessage());
        }
    }

    public function edit(Course $course, Section $section)
    {
        if ($section->course_id !== $course->id) {
            abort(404);
        }

        return view('admin.courses.sections.edit', compact('course', 'section'));
    }

    public function update(Request $request, Course $course, Section $section)
    {
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

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Section berhasil diperbarui.']);
            }

            return redirect()->route('admin.sections.index', $course)
                ->with('success', 'Section berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui section: ' . $e->getMessage());
        }
    }

    public function destroy(Course $course, Section $section)
    {
        if ($section->course_id !== $course->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();
            $section->delete();
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Section berhasil dihapus.']);
            }
            return redirect()->route('admin.sections.index', $course)
                ->with('success', 'Section berhasil dihapus.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus section.'], 500);
        }
    }

    public function updateOrder(Request $request, Course $course)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*' => 'exists:sections,id',
        ]);

        try {
            foreach ($request->sections as $index => $sectionId) {
                Section::where('id', $sectionId)->update(['order' => $index + 1]);
            }
            return response()->json(['success' => true, 'message' => 'Urutan section diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate urutan.']);
        }
    }
}