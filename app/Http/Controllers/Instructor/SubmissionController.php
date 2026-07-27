<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Tampilkan semua assignment dari kursus yang diajar oleh instruktur.
     */
    public function index()
    {
        $instructorId = Auth::id();

        $assignments = Assignment::whereHas('course', function ($query) use ($instructorId) {
            $query->where('instructor_id', $instructorId);
        })
        ->withCount(['submissions as pending_count' => function ($query) {
            $query->whereIn('status', ['submitted', 'late']);
        }])
        ->with(['course'])
        ->latest()
        ->paginate(10);

        return view('instructor.submissions.index', compact('assignments'));
    }

    /**
     * Tampilkan daftar submission untuk assignment tertentu.
     */
    public function show(Assignment $assignment)
    {
        // Pastikan assignment milik kursus instruktur ini
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $submissions = $assignment->submissions()
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);

        return view('instructor.submissions.show', compact('assignment', 'submissions'));
    }

    /**
     * Tampilkan form penilaian untuk satu submission.
     */
    public function edit(Submission $submission)
    {
        // Load assignment dan course untuk validasi owner
        $submission->load(['assignment.course', 'student']);

        if ($submission->assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        return view('instructor.submissions.edit', compact('submission'));
    }

    /**
     * Simpan nilai dan feedback untuk submission.
     */
    public function update(Request $request, Submission $submission)
    {
        $submission->load('assignment.course');

        if ($submission->assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'score' => 'required|integer|min:0|max:' . $submission->assignment->max_score,
            'feedback' => 'nullable|string',
            'status' => 'required|in:graded,returned',
            'rubric_scores' => 'nullable|array',
            'rubric_scores.*' => 'nullable|integer|min:0',
        ]);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => $request->status,
            'rubric_scores' => $request->input('rubric_scores'),
            'graded_at' => now(),
            'graded_by' => Auth::id(),
        ]);

        // Update statistik assignment
        $submission->assignment->updateStats();

        return redirect()
            ->route('instructor.submissions.show', $submission->assignment_id)
            ->with('success', 'Penilaian berhasil disimpan.');
    }
}
