<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Riwayat broadcast yang pernah dikirim (dikelompokkan per pengiriman).
     */
    public function index()
    {
        $broadcasts = Notification::where('type', 'announcement')
            ->selectRaw('title, message, action_url, sent_at, COUNT(*) as recipients')
            ->groupBy('title', 'message', 'action_url', 'sent_at')
            ->orderByDesc('sent_at')
            ->paginate(15);

        return view('admin.notifications.index', compact('broadcasts'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.notifications.create', compact('courses'));
    }

    /**
     * Broadcast notifikasi ke audiens yang dipilih.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'action_url' => 'nullable|string|max:255',
            'audience' => 'required|in:all,students,instructors,course',
            'course_id' => 'required_if:audience,course|nullable|exists:courses,id',
        ]);

        $query = User::query();

        if ($validated['audience'] === 'students') {
            $query->role('student');
        } elseif ($validated['audience'] === 'instructors') {
            $query->role('instructor');
        } elseif ($validated['audience'] === 'course') {
            $query->whereHas('enrollments', function ($q) use ($validated) {
                $q->where('course_id', $validated['course_id']);
            });
        }

        $userIds = $query->pluck('id');

        if ($userIds->isEmpty()) {
            return back()->with('error', 'Tidak ada user yang cocok dengan audiens yang dipilih.')->withInput();
        }

        $sentAt = now();

        $rows = $userIds->map(fn ($userId) => [
            'user_id' => $userId,
            'type' => 'announcement',
            'title' => $validated['title'],
            'message' => $validated['message'],
            'action_url' => $validated['action_url'] ?? null,
            'channel' => 'database',
            'sent_at' => $sentAt,
            'created_at' => $sentAt,
            'updated_at' => $sentAt,
        ])->toArray();

        foreach (array_chunk($rows, 500) as $chunk) {
            Notification::insert($chunk);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notifikasi berhasil dikirim ke {$userIds->count()} user.");
    }
}
