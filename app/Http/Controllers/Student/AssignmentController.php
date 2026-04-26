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
                })->where('due_date', '>', now());
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

        $isLate = $assignment->due_date < now() && !$submission;
        $canSubmit = !$submission || ($submission->status === 'draft');
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

        try {
            DB::beginTransaction();

            $isLate = $assignment->due_date < now();
            $latePenalty = 0;

            if ($isLate && $assignment->allow_late_submission) {
                $daysLate = now()->diffInDays($assignment->due_date);
                $latePenalty = $assignment->late_penalty * $daysLate;
            }

            $submission = Submission::updateOrCreate(
                ['assignment_id' => $assignment->id, 'student_id' => auth()->id()],
                [
                    'content' => $request->input('content'),
                    'status' => 'submitted',
                    'submitted_at' => now(),
                    'is_late' => $isLate,
                    'submission_count' => DB::raw('submission_count + 1'),
                ]
            );

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

            $message = $isLate
                ? 'Tugas berhasil dikirim (terlambat ' . $daysLate . ' hari).'
                : 'Tugas berhasil dikirim.';

            return redirect()->route('student.assignments.show', $assignment)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim tugas: ' . $e->getMessage());
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