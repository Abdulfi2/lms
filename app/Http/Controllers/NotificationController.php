<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Daftar lengkap notifikasi milik user yang login.
     */
    public function index()
    {
        $notifications = auth()->user()->appNotifications()
            ->latest('sent_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai satu notifikasi terbaca, lalu arahkan ke action_url jika ada.
     */
    public function markRead(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return $notification->action_url
            ? redirect($notification->action_url)
            : redirect()->route('notifications.index');
    }

    /**
     * Tandai semua notifikasi milik user sebagai terbaca.
     */
    public function markAllRead()
    {
        auth()->user()->appNotifications()->unread()->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai terbaca.');
    }
}
