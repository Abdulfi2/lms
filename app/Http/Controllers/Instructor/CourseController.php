<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('categories')
            ->where('instructor_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $tags = Tag::active()->get();
        return view('instructor.courses.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:courses,slug',
            'short_description' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sale_starts_at' => 'nullable|date',
            'sale_ends_at' => 'nullable|date|after:sale_starts_at',
            'level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'language' => 'required|string|max:10',
            'duration_total' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,pending,published,archived',
            'is_featured' => 'nullable|boolean',
            'has_certificate' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'requirements' => 'nullable|array',
            'requirements.*' => 'nullable|string|max:255',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'nullable|string|max:255',
            'target_audience' => 'nullable|array',
            'target_audience.*' => 'nullable|string|max:255',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['thumbnail', 'categories', 'tags']);
            $data['instructor_id'] = auth()->id();
            $data['slug'] = $request->slug ?: \Illuminate\Support\Str::slug($request->title) . '-' . uniqid();

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                $data['thumbnail'] = $path;
            }

            $course = Course::create($data);

            if ($request->has('categories')) {
                $course->categories()->sync($request->categories);
            }
            if ($request->has('tags')) {
                $course->tags()->sync($request->tags);
                foreach ($request->tags as $tagId) {
                    \App\Models\Tag::where('id', $tagId)->increment('usage_count');
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Kursus berhasil dibuat.']);
            }

            return redirect()->route('instructor.courses.index')
                ->with('success', 'Kursus berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal membuat kursus: ' . $e->getMessage()], 422);
            }

            return back()->with('error', 'Gagal membuat kursus: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Course $course)
    {
        // Pastikan instructor hanya bisa edit kursus miliknya
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }
        $categories = Category::active()->get();
        $tags = Tag::active()->get();
        $selectedCategories = $course->categories->pluck('id')->toArray();
        $selectedTags = $course->tags->pluck('id')->toArray();
        return view('instructor.courses.edit', compact('course', 'categories', 'tags', 'selectedCategories', 'selectedTags'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('courses')->ignore($course->id)],
            'short_description' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sale_starts_at' => 'nullable|date',
            'sale_ends_at' => 'nullable|date|after:sale_starts_at',
            'level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'language' => 'required|string|max:10',
            'duration_total' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,pending,published,archived',
            'is_featured' => 'nullable|boolean',
            'has_certificate' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'requirements' => 'nullable|array',
            'requirements.*' => 'nullable|string|max:255',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'nullable|string|max:255',
            'target_audience' => 'nullable|array',
            'target_audience.*' => 'nullable|string|max:255',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['thumbnail', 'categories', 'tags']);
            $data['slug'] = $request->slug ?: \Illuminate\Support\Str::slug($request->title) . '-' . $course->id;

            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                $data['thumbnail'] = $path;
            }

            $course->update($data);

            // Sync categories
            $course->categories()->sync($request->categories ?? []);

            // Sync tags with usage_count adjustment
            $oldTags = $course->tags->pluck('id')->toArray();
            $newTags = $request->tags ?? [];
            $toRemove = array_diff($oldTags, $newTags);
            $toAdd = array_diff($newTags, $oldTags);
            foreach ($toRemove as $tagId) {
                \App\Models\Tag::where('id', $tagId)->decrement('usage_count');
            }
            foreach ($toAdd as $tagId) {
                \App\Models\Tag::where('id', $tagId)->increment('usage_count');
            }
            $course->tags()->sync($newTags);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Kursus berhasil diperbarui.']);
            }

            return redirect()->route('instructor.courses.index')
                ->with('success', 'Kursus berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal memperbarui kursus: ' . $e->getMessage()], 422);
            }

            return back()->with('error', 'Gagal memperbarui kursus: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $course->categories()->detach();
            $course->tags()->detach();
            $course->delete();
            DB::commit();

            return redirect()->route('instructor.courses.index')
                ->with('success', 'Kursus berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus kursus.');
        }
    }

    /**
     * Duplikasi kursus beserta section & lesson-nya sebagai draft baru.
     * Tidak menyalin quiz, assignment, atau lesson resource — hanya struktur materi.
     */
    public function duplicate(Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $newCourse = $course->replicate();
            $newCourse->title = $course->title . ' (Copy)';
            $newCourse->slug = \Illuminate\Support\Str::slug($newCourse->title) . '-' . uniqid();
            $newCourse->status = 'draft';
            $newCourse->total_students = 0;
            $newCourse->average_rating = 0;
            $newCourse->rating_count = 0;
            $newCourse->reviews_count = 0;
            $newCourse->enrolled_count = 0;
            $newCourse->wishlist_count = 0;
            $newCourse->published_at = null;
            $newCourse->save();

            $newCourse->categories()->sync($course->categories->pluck('id'));
            $newCourse->tags()->sync($course->tags->pluck('id'));
            foreach ($course->tags as $tag) {
                $tag->increment('usage_count');
            }

            foreach ($course->sections()->orderBy('order')->get() as $section) {
                $newSection = $section->replicate();
                $newSection->course_id = $newCourse->id;
                $newSection->save();

                foreach ($section->lessons()->orderBy('order')->get() as $lesson) {
                    $newLesson = $lesson->replicate();
                    $newLesson->section_id = $newSection->id;
                    $newLesson->save();
                }
            }

            DB::commit();

            return redirect()->route('instructor.courses.edit', $newCourse)
                ->with('success', 'Kursus berhasil diduplikasi sebagai draft. Materi (section & lesson) ikut disalin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menduplikasi kursus: ' . $e->getMessage());
        }
    }

    /**
     * Preview kursus sebagaimana akan dilihat calon siswa, termasuk saat masih draft.
     */
    public function preview(Course $course)
    {
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $course->load([
            'instructor',
            'categories',
            'sections' => fn ($q) => $q->orderBy('order'),
            'sections.lessons' => fn ($q) => $q->orderBy('order'),
        ]);

        $isEnrolled = false;
        $enrollment = null;
        $isWishlisted = false;
        $averageRating = $course->reviews()->approved()->avg('rating') ?? 0;
        $ratingCount = $course->reviews()->approved()->count();
        $recentReviews = $course->reviews()->approved()->latest()->limit(5)->get();
        $otherCourses = Course::where('instructor_id', $course->instructor_id)
            ->where('id', '!=', $course->id)
            ->where('status', 'published')
            ->limit(3)
            ->get();
        $previewMode = true;

        return view('courses.show', compact(
            'course',
            'isEnrolled',
            'enrollment',
            'isWishlisted',
            'averageRating',
            'ratingCount',
            'recentReviews',
            'otherCourses',
            'previewMode'
        ));
    }
}