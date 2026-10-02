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
use Illuminate\Support\Arr;
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

            $data = Arr::except($request->validated(), ['thumbnail', 'categories', 'tags']);

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                $data['thumbnail'] = $path;
            }

            $data['slug'] = $request->slug ?: \Illuminate\Support\Str::slug($request->title);
            // Input datetime-local yang dikosongkan mengirim '' (bukan absen), dan '' bukan
            // nilai DATETIME yang valid di MySQL — normalisasi ke null di sini.
            $data['sale_starts_at'] = $data['sale_starts_at'] ?: null;
            $data['sale_ends_at'] = $data['sale_ends_at'] ?: null;

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

            $data = Arr::except($request->validated(), ['thumbnail', 'categories', 'tags']);
            // Input datetime-local yang dikosongkan mengirim '' (bukan absen), dan '' bukan
            // nilai DATETIME yang valid di MySQL — normalisasi ke null di sini.
            $data['sale_starts_at'] = $data['sale_starts_at'] ?: null;
            $data['sale_ends_at'] = $data['sale_ends_at'] ?: null;

            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
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

            // Course pakai SoftDeletes, jadi FK onDelete('cascade') di DB tidak pernah
            // ter-trigger untuk konten strukturalnya. Hapus eksplisit di sini supaya
            // sections/lessons/quiz tidak nyangkut merujuk ke course yang sudah "dihapus".
            // Data riwayat siswa (enrollment, sertifikat, review, payment) SENGAJA tidak
            // disentuh supaya sertifikat/riwayat belajar siswa tetap ada.
            $course->sections()->delete();
            $course->quizzes()->delete();

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
        // Kursus yang masih menunggu approval tidak boleh ikut ke-toggle langsung jadi
        // published lewat sini — harus lewat approve()/reject() supaya alurnya jelas.
        if ($course->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Kursus ini masih menunggu approval. Gunakan tombol Setujui/Tolak.'
            ], 422);
        }

        $newStatus = $course->status === 'published' ? 'draft' : 'published';
        $course->update(['status' => $newStatus, 'published_at' => $newStatus === 'published' ? now() : null]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Status kursus berhasil diubah.'
        ]);
    }

    /**
     * Setujui kursus yang statusnya pending -> published.
     */
    public function approve(Course $course)
    {
        if ($course->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Kursus ini tidak sedang menunggu approval.'], 422);
        }

        $course->update([
            'status' => 'published',
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Kursus berhasil disetujui dan dipublikasikan.']);
    }

    /**
     * Tolak kursus yang statusnya pending -> kembali ke draft, dengan alasan untuk instruktur.
     */
    public function reject(Request $request, Course $course)
    {
        if ($course->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Kursus ini tidak sedang menunggu approval.'], 422);
        }

        $request->validate(['reason' => 'required|string|max:1000']);

        $course->update([
            'status' => 'draft',
            'rejection_reason' => $request->reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Kursus ditolak dan dikembalikan ke instruktur sebagai draft.']);
    }

    public function show(Course $course)
    {
        $course->load('instructor', 'categories', 'tags', 'sections.lessons');
        return view('admin.courses.show', compact('course'));
    }
}