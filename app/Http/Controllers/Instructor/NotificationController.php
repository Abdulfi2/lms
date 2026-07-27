<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Riwayat pengumuman yang pernah dikirim instruktur ini.
     */
    public function index()
    {
        $broadcasts = Notification::where('type', 'course_announcement')
            ->whereJsonContains('data->sender_id', auth()->id())
            ->selectRaw("title, message, JSON_UNQUOTE(JSON_EXTRACT(data, '$.course_id')) as course_id, sent_at, COUNT(*) as recipients")
            ->groupBy('title', 'message', 'course_id', 'sent_at')
            ->orderByDesc('sent_at')
            ->paginate(15);

        return view('instructor.announcements.index', compact('broadcasts'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', auth()->id())->get(['id', 'title']);

        return view('instructor.announcements.create', compact('courses'));
    }

    /**
     * Broadcast pengumuman ke siswa yang terdaftar aktif di salah satu kursus instruktur ini.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'action_url' => 'nullable|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::where('id', $validated['course_id'])
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        $userIds = User::whereHas('enrollments', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('status', 'active');
        })->pluck('id');

        if ($userIds->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa aktif di kursus tersebut.')->withInput();
        }

        $sentAt = now();

        $rows = $userIds->map(fn ($userId) => [
            'user_id' => $userId,
            'type' => 'course_announcement',
            'title' => $validated['title'],
            'message' => $validated['message'],
            'action_url' => $validated['action_url'] ?? null,
            'data' => json_encode(['sender_id' => auth()->id(), 'course_id' => $course->id]),
            'channel' => 'database',
            'sent_at' => $sentAt,
            'created_at' => $sentAt,
            'updated_at' => $sentAt,
        ])->toArray();

        foreach (array_chunk($rows, 500) as $chunk) {
            Notification::insert($chunk);
        }

        return redirect()->route('instructor.announcements.index')
            ->with('success', "Pengumuman berhasil dikirim ke {$userIds->count()} siswa di kursus \"{$course->title}\".");
    }
}
