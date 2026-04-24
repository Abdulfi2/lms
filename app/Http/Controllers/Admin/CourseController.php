<?php
// app/Http/Controllers/Admin/CourseController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('instructor', 'categories');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        $courses = $query->latest()->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($courses);
        }

        $statuses = ['draft', 'pending', 'published', 'archived'];
        $levels = ['beginner', 'intermediate', 'advanced', 'all_levels'];
        $instructors = User::role('instructor')->get(['id', 'name']);

        return view('admin.courses.index', compact('courses', 'statuses', 'levels', 'instructors'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $tags = Tag::active()->get();
        $instructors = User::role('instructor')->get(['id', 'name']);
        return view('admin.courses.create', compact('categories', 'tags', 'instructors'));
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->except(['thumbnail', 'categories', 'tags']);

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                $data['thumbnail'] = $path;
            }

            $data['slug'] = $request->slug ?: \Illuminate\Support\Str::slug($request->title);
            $data['instructor_id'] = $request->instructor_id ?? auth()->id();

            $course = Course::create($data);

            // Sync categories
            if ($request->has('categories')) {
                $course->categories()->sync($request->categories);
            }

            // Sync tags
            if ($request->has('tags')) {
                $course->tags()->sync($request->tags);
                // Update usage_count (optional)
                foreach ($request->tags as $tagId) {
                    Tag::where('id', $tagId)->increment('usage_count');
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kursus berhasil dibuat.',
                    'data' => $course
                ]);
            }

            return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat kursus: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal membuat kursus: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Course $course)
    {
        $course->load('categories', 'tags');
        $categories = Category::active()->get();
        $tags = Tag::active()->get();
        $instructors = User::role('instructor')->get(['id', 'name']);
        $selectedCategories = $course->categories->pluck('id')->toArray();
        $selectedTags = $course->tags->pluck('id')->toArray();

        return view('admin.courses.edit', compact('course', 'categories', 'tags', 'instructors', 'selectedCategories', 'selectedTags'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        try {
            DB::beginTransaction();

            $data = $request->except(['thumbnail', 'categories', 'tags']);

            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $path = $request->file('thumbnail')->upload('courses/thumbnails', 'public');
                $data['thumbnail'] = $path;
            }

            $data['slug'] = $request->slug ?: Str::slug($request->title);

            $course->update($data);

            // Sync categories
            $course->categories()->sync($request->categories ?? []);

            // Sync tags with usage_count adjustment
            $oldTags = $course->tags->pluck('id')->toArray();
            $newTags = $request->tags ?? [];

            $toRemove = array_diff($oldTags, $newTags);
            $toAdd = array_diff($newTags, $oldTags);

            foreach ($toRemove as $tagId) {
                Tag::where('id', $tagId)->decrement('usage_count');
            }
            foreach ($toAdd as $tagId) {
                Tag::where('id', $tagId)->increment('usage_count');
            }

            $course->tags()->sync($newTags);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kursus berhasil diperbarui.',
                    'data' => $course
                ]);
            }

            return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kursus: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal memperbarui kursus: ' . $e->getMessage());
        }
    }

    public function destroy(Course $course)
    {
        try {
            DB::beginTransaction();
            // Delete thumbnail
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $course->categories()->detach();
            $course->tags()->detach();
            $course->delete();
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kursus berhasil dihapus.'
                ]);
            }
            return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus kursus: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menghapus kursus.');
        }
    }

    public function toggleStatus(Course $course)
    {
        $newStatus = $course->status === 'published' ? 'draft' : 'published';
        $course->update(['status' => $newStatus, 'published_at' => $newStatus === 'published' ? now() : null]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Status kursus berhasil diubah.'
        ]);
    }

    public function show(Course $course)
    {
        $course->load('instructor', 'categories', 'tags', 'sections.lessons');
        return view('admin.courses.show', compact('course'));
    }
}