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
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'has_certificate' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
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

            return redirect()->route('instructor.courses.index')
                ->with('success', 'Kursus berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
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
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'has_certificate' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
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

            return redirect()->route('instructor.courses.index')
                ->with('success', 'Kursus berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
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
}