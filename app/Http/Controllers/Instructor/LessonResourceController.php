<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LessonResourceController extends Controller
{
    /**
     * Display resources of a lesson.
     */
    public function index(Course $course, Section $section, Lesson $lesson)
    {
        // Check authorization
        if ($course->instructor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $resources = $lesson->resources()->orderBy('order')->get();

        return view('instructor.lessons.resources.index', compact('course', 'section', 'lesson', 'resources'));
    }

    /**
     * Show form to create new resource.
     */
    public function create(Course $course, Section $section, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return view('instructor.lessons.resources.create', compact('course', 'section', 'lesson'));
    }

    /**
     * Store new resource.
     */
    public function store(Request $request, Course $course, Section $section, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_file' => 'required|file|max:20480', // Max 20MB
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('resource_file');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();

            $path = $file->store('lesson-resources/' . $lesson->id, 'public');

            $maxOrder = $lesson->resources()->max('order') ?? 0;

            $resource = $lesson->resources()->create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'file_type' => $fileType,
                'order' => $maxOrder + 1,
            ]);

            DB::commit();

            return redirect()->route('instructor.courses.sections.lessons.resources.index', [$course, $section, $lesson])
                ->with('success', 'Resource berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambah resource: ' . $e->getMessage());
        }
    }

    /**
     * Edit resource.
     */
    public function edit(Course $course, Section $section, Lesson $lesson, LessonResource $resource)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return view('instructor.lessons.resources.edit', compact('course', 'section', 'lesson', 'resource'));
    }

    /**
     * Update resource.
     */
    public function update(Request $request, Course $course, Section $section, Lesson $lesson, LessonResource $resource)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_file' => 'nullable|file|max:20480',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['title', 'description']);

            if ($request->hasFile('resource_file')) {
                // Delete old file
                if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                    Storage::disk('public')->delete($resource->file_path);
                }

                $file = $request->file('resource_file');
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileType = $file->getMimeType();

                $path = $file->store('lesson-resources/' . $lesson->id, 'public');

                $data['file_path'] = $path;
                $data['file_name'] = $originalName;
                $data['file_size'] = $fileSize;
                $data['file_type'] = $fileType;
            }

            $resource->update($data);

            DB::commit();

            return redirect()->route('instructor.courses.sections.lessons.resources.index', [$course, $section, $lesson])
                ->with('success', 'Resource berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui resource: ' . $e->getMessage());
        }
    }

    /**
     * Delete resource.
     */
    public function destroy(Course $course, Section $section, Lesson $lesson, LessonResource $resource)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Delete file from storage
            if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $resource->delete();

            DB::commit();

            return redirect()->route('instructor.courses.sections.lessons.resources.index', [$course, $section, $lesson])
                ->with('success', 'Resource berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus resource: ' . $e->getMessage());
        }
    }

    /**
     * Update order (drag & drop).
     */
    public function updateOrder(Request $request, Course $course, Section $section, Lesson $lesson)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'resources' => 'required|array',
            'resources.*' => 'exists:lesson_resources,id',
        ]);

        try {
            foreach ($request->resources as $index => $id) {
                LessonResource::where('id', $id)->update(['order' => $index + 1]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Download resource (for student).
     */
    public function download(Course $course, Section $section, Lesson $lesson, LessonResource $resource)
    {
        // Admin & instruktur pemilik kursus selalu boleh mengunduh.
        $isPrivileged = auth()->user()->hasRole('admin')
            || ($course->instructor_id === auth()->id() && auth()->user()->hasRole('instructor'));

        if (!$isPrivileged) {
            // Samakan aturan akses dengan Student\LessonController::show() — enrollment harus aktif
            // DAN lunas. Sebelumnya di sini hanya dicek "punya baris enrollment" tanpa cek payment_status,
            // sehingga siswa yang belum bayar kursus berbayar tetap bisa mengunduh materinya langsung.
            $isEnrolled = \App\Models\Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->whereIn('status', ['active', 'completed'])
                ->where('payment_status', 'paid')
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'Anda harus terdaftar dan menyelesaikan pembayaran kursus ini untuk mendownload resource.');
            }
        }

        // Increment download count
        $resource->increment('download_count');

        $filePath = storage_path('app/public/' . $resource->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, $resource->file_name);
    }
}