<?php
// app/Http/Controllers/UserSettingController.php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $settings = UserSetting::getForUser(Auth::id());
        return view('settings.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'language' => 'required|string|in:id,en',
            'timezone' => 'required|timezone',
            'date_format' => 'required|string|in:d/m/Y,Y-m-d,m/d/Y',
            'dark_mode' => 'nullable|boolean',
            'compact_view' => 'nullable|boolean',
        ]);

        $settings = UserSetting::getForUser(Auth::id());
        $settings->update([
            'language' => $request->language,
            'timezone' => $request->timezone,
            'date_format' => $request->date_format,
            'dark_mode' => $request->has('dark_mode'),
            'compact_view' => $request->has('compact_view'),
        ]);

        // Update session locale
        app()->setLocale($request->language);
        session(['locale' => $request->language]);

        return back()->with('success', 'Pengaturan umum berhasil diperbarui.');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'assignment_reminder' => 'nullable|boolean',
            'quiz_reminder' => 'nullable|boolean',
            'course_update_notification' => 'nullable|boolean',
            'forum_reply_notification' => 'nullable|boolean',
            'certificate_notification' => 'nullable|boolean',
            'event_reminder' => 'nullable|boolean',
        ]);

        $settings = UserSetting::getForUser(Auth::id());
        $settings->update([
            'email_notifications' => $request->has('email_notifications'),
            'push_notifications' => $request->has('push_notifications'),
            'assignment_reminder' => $request->has('assignment_reminder'),
            'quiz_reminder' => $request->has('quiz_reminder'),
            'course_update_notification' => $request->has('course_update_notification'),
            'forum_reply_notification' => $request->has('forum_reply_notification'),
            'certificate_notification' => $request->has('certificate_notification'),
            'event_reminder' => $request->has('event_reminder'),
        ]);

        return back()->with('success', 'Pengaturan notifikasi berhasil diperbarui.');
    }

    public function updateLearning(Request $request)
    {
        $request->validate([
            'auto_play_video' => 'nullable|boolean',
            'show_subtitles' => 'nullable|boolean',
            'video_quality' => 'required|in:auto,1080p,720p,480p,360p',
            'auto_mark_complete' => 'nullable|boolean',
            'daily_goal_minutes' => 'nullable|integer|min:0|max:480',
        ]);

        $settings = UserSetting::getForUser(Auth::id());
        $settings->update([
            'auto_play_video' => $request->has('auto_play_video'),
            'show_subtitles' => $request->has('show_subtitles'),
            'video_quality' => $request->video_quality,
            'auto_mark_complete' => $request->has('auto_mark_complete'),
            'daily_goal_minutes' => $request->daily_goal_minutes ?? 30,
        ]);

        return back()->with('success', 'Pengaturan pembelajaran berhasil diperbarui.');
    }

    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'profile_public' => 'nullable|boolean',
            'show_progress' => 'nullable|boolean',
            'show_certificates' => 'nullable|boolean',
            'allow_messages' => 'nullable|boolean',
        ]);

        $settings = UserSetting::getForUser(Auth::id());
        $settings->update([
            'profile_public' => $request->has('profile_public'),
            'show_progress' => $request->has('show_progress'),
            'show_certificates' => $request->has('show_certificates'),
            'allow_messages' => $request->has('allow_messages'),
        ]);

        return back()->with('success', 'Pengaturan privasi berhasil diperbarui.');
    }

    public function updateAccessibility(Request $request)
    {
        $request->validate([
            'high_contrast' => 'nullable|boolean',
            'large_text' => 'nullable|boolean',
            'screen_reader' => 'nullable|boolean',
        ]);

        $settings = UserSetting::getForUser(Auth::id());
        $settings->update([
            'high_contrast' => $request->has('high_contrast'),
            'large_text' => $request->has('large_text'),
            'screen_reader' => $request->has('screen_reader'),
        ]);

        return back()->with('success', 'Pengaturan aksesibilitas berhasil diperbarui.');
    }

    public function resetToDefault()
    {
        $settings = UserSetting::getForUser(Auth::id());
        $settings->delete();

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil direset ke default.');
    }
}