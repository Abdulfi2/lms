<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Daftar semua tugas student dari kursus yang diikuti.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua course ID yang diikuti student
        $enrolledCourseIds = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('course_id');

        $query = Assignment::with([
            'course',
            'submissions' => function ($q) use ($user) {
                $q->where('student_id', $user->id);
            }
        ])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', true);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            if ($request->status == 'pending') {
                $query->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                })->where(function ($q) {
                    $q->where('due_date', '>', now())->orWhereNull('due_date');
                });
            } elseif ($request->status == 'submitted') {
                $query->whereHas('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id)->where('status', 'submitted');
                });
            } elseif ($request->status == 'graded') {
                $query->whereHas('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id)->where('status', 'graded');
                });
            } elseif ($request->status == 'late') {
                $query->whereHas('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id)->where('is_late', true);
                });
            } elseif ($request->status == 'overdue') {
                $query->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                })->where('due_date', '<', now());
            }
        }

        $assignments = $query->orderBy('due_date')->paginate(10);

        // Statistik
        $totalAssignments = $query->count();
        $completedAssignments = Assignment::whereIn('course_id', $enrolledCourseIds)
            ->whereHas('submissions', function ($q) use ($user) {
                $q->where('student_id', $user->id);
            })->count();
        $pendingAssignments = $totalAssignments - $completedAssignments;
        $averageScore = Submission::where('student_id', $user->id)
            ->whereNotNull('score')
            ->avg('score');

        return view('student.assignments.index', compact(
            'assignments',
            'totalAssignments',
            'completedAssignments',
            'pendingAssignments',
            'averageScore'
        ));
    }

    /**
     * Detail assignment.
     */
    public function show(Assignment $assignment)
    {
        // Cek apakah student terdaftar di course ini
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        // Ambil submission jika sudah ada
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', auth()->id())
            ->first();

        $isLate = $assignment->due_date !== null && $assignment->due_date < now() && !$submission;
        $canSubmit = !$submission || in_array($submission->status, ['draft', 'submitted', 'returned']);
        $isGraded = $submission && $submission->status === 'graded';

        return view('student.assignments.show', compact(
            'assignment',
            'submission',
            'isLate',
            'canSubmit',
            'isGraded'
        ));
    }

    /**
     * Form submit assignment.
     */
    public function create(Assignment $assignment)
    {
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        $submission = Submission::firstOrNew([
            'assignment_id' => $assignment->id,
            'student_id' => auth()->id()
        ]);

        return view('student.assignments.submit', compact('assignment', 'submission'));
    }

    /**
     * Store submission.
     */
    public function store(Request $request, Assignment $assignment)
    {
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $assignment->course_id)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Anda tidak terdaftar di kursus ini.');
        }

        $request->validate([
            'content' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,zip,jpg,png',
        ]);

        $isLate = $assignment->due_date !== null && $assignment->due_date < now();

        // Tegakkan aturan telat di server, bukan hanya lewat atribut `disabled` di tombol HTML.
        if ($isLate && !$assignment->allow_late_submission) {
            return back()->with('error', 'Deadline tugas sudah lewat dan pengiriman terlambat tidak diizinkan.');
        }

        // Submission yang sudah final (graded) tidak boleh ditimpa oleh POST langsung ke store().
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', auth()->id())
            ->first();

        if ($existingSubmission && $existingSubmission->status === 'graded') {
            return back()->with('error', 'Tugas ini sudah dinilai dan tidak dapat dikirim ulang.');
        }

        try {
            DB::beginTransaction();

            $daysLate = $isLate ? now()->diffInDays($assignment->due_date) : 0;

            // Bukan updateOrCreate() dengan DB::raw('submission_count + 1') — pada INSERT (submission
            // pertama) itu menghasilkan referensi kolom yang belum ada baris nilainya sama sekali,
            // sehingga query gagal (Unknown column 'submission_count') setiap kali baris belum ada.
            $submission = Submission::firstOrNew([
                'assignment_id' => $assignment->id,
                'student_id' => auth()->id(),
            ]);
            $submission->content = $request->input('content');
            $submission->status = 'submitted';
            $submission->submitted_at = now();
            $submission->is_late = $isLate;
            $submission->submission_count = ($submission->submission_count ?? 0) + 1;
            $submission->save();

            if ($request->hasFile('attachment')) {
                // Hapus file lama jika ada
                if ($submission->file_url && Storage::disk('public')->exists($submission->file_url)) {
                    Storage::disk('public')->delete($submission->file_url);
                }

                $path = $request->file('attachment')->store('submissions', 'public');
                $submission->file_url = $path;
                $submission->save();
            }

            DB::commit();

            // Poin gamifikasi hanya diberikan sekali di pengiriman pertama, supaya
            // kirim ulang (revisi) tidak bisa dipakai untuk numpuk poin.
            if ($submission->submission_count === 1) {
                try {
                    \App\Services\GamificationService::assignmentSubmitted(auth()->user());
                } catch (\Exception $e) {
                    \Log::warning('Gamification error: ' . $e->getMessage());
                }
            }

            $message = $isLate
                ? 'Tugas berhasil dikirim (terlambat ' . $daysLate . ' hari).'
                : 'Tugas berhasil dikirim.';

            return redirect()->route('student.assignments.show', $assignment)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Assignment submission failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim tugas. Silakan coba lagi.');
        }
    }

    /**
     * Batalkan submission (kembalikan ke draft).
     */
    public function cancel(Assignment $assignment)
    {
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', auth()->id())
            ->where('status', 'submitted')
            ->first();

        if ($submission) {
            $submission->update(['status' => 'draft']);
            return back()->with('success', 'Pengiriman tugas dibatalkan. Anda dapat mengirim ulang.');
        }

        return back()->with('error', 'Tidak ada tugas yang dibatalkan.');
    }
}