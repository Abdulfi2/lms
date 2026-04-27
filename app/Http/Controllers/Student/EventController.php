<?php
// app/Http/Controllers/Student/EventController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Menampilkan daftar event yang tersedia untuk student.
     */
    public function index(Request $request)
    {
        $query = Event::with('organizer')
            ->where('status', 'published')
            ->where('start_time', '>=', now());

        // Filter berdasarkan tipe event
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan gratis/berbayar
        if ($request->filled('price_type')) {
            $query->where('price_type', $request->price_type);
        }

        // Pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('speaker', 'like', "%{$request->search}%");
            });
        }

        $events = $query->orderBy('start_time')->paginate(12);

        // Ambil event yang sudah student daftar
        $registeredEventIds = EventRegistration::where('user_id', auth()->id())
            ->pluck('event_id')
            ->toArray();

        // Statistik
        $upcomingCount = Event::where('status', 'published')
            ->where('start_time', '>=', now())
            ->count();

        $myEventsCount = EventRegistration::where('user_id', auth()->id())
            ->whereHas('event', function ($q) {
                $q->where('start_time', '>=', now());
            })
            ->count();

        $eventTypes = Event::distinct()->pluck('type');
        $categories = Event::distinct()->pluck('category');

        return view('student.events.index', compact('events', 'registeredEventIds', 'upcomingCount', 'myEventsCount', 'eventTypes', 'categories'));
    }

    /**
     * Menampilkan detail event.
     */
    public function show(Event $event)
    {
        $user = auth()->user();

        // Cek apakah sudah terdaftar
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        $isRegistered = !is_null($registration);
        $isConfirmed = $registration && $registration->status === 'confirmed';

        // Hitung sisa kuota
        $remainingSlots = $event->max_participants
            ? max(0, $event->max_participants - $event->total_registrations)
            : null;

        $isFull = $event->max_participants && $event->total_registrations >= $event->max_participants;
        $isExpired = $event->start_time < now();

        // Event lain yang direkomendasikan
        $relatedEvents = Event::where('status', 'published')
            ->where('id', '!=', $event->id)
            ->where(function ($q) use ($event) {
                $q->where('type', $event->type)
                    ->orWhere('category', $event->category);
            })
            ->where('start_time', '>=', now())
            ->limit(3)
            ->get();

        return view('student.events.show', compact('event', 'isRegistered', 'isConfirmed', 'remainingSlots', 'isFull', 'isExpired', 'relatedEvents'));
    }

    /**
     * Mendaftar ke event.
     */
    public function register(Request $request, Event $event)
    {
        $user = auth()->user();

        // Validasi event
        if ($event->status !== 'published') {
            return back()->with('error', 'Event ini belum dipublikasikan.');
        }

        if ($event->start_time < now()) {
            return back()->with('error', 'Event sudah lewat, tidak dapat mendaftar.');
        }

        // Cek kuota
        if ($event->max_participants && $event->total_registrations >= $event->max_participants) {
            return back()->with('error', 'Maaf, kuota peserta sudah penuh.');
        }

        // Cek sudah terdaftar
        $exists = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah terdaftar di event ini.');
        }

        // Validasi untuk event berbayar
        if ($event->price_type === 'paid' && $event->price > 0) {
            $request->validate([
                'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);
        }

        try {
            DB::beginTransaction();

            $data = [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $request->phone ?? $user->profile->phone ?? null,
                'notes' => $request->notes,
                'status' => $event->price_type === 'free' ? 'confirmed' : 'pending',
                'amount_paid' => $event->price_type === 'free' ? 0 : $event->price,
            ];

            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('event-payments', 'public');
                $data['payment_proof'] = $path;
            }

            $registration = EventRegistration::create($data);

            // Update total registrations
            $event->increment('total_registrations');

            DB::commit();

            $message = $event->price_type === 'free'
                ? 'Pendaftaran berhasil! Silakan cek email Anda untuk detail event.'
                : 'Pendaftaran berhasil! Silakan lakukan pembayaran untuk mengkonfirmasi kehadiran.';

            return redirect()->route('student.events.show', $event)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan daftar event yang diikuti student.
     */
    public function myEvents()
    {
        $registrations = EventRegistration::with('event')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $upcomingEvents = $registrations->filter(function ($reg) {
            return $reg->event->start_time >= now();
        });

        $pastEvents = $registrations->filter(function ($reg) {
            return $reg->event->start_time < now();
        });

        return view('student.events.my-events', compact('registrations', 'upcomingEvents', 'pastEvents'));
    }

    /**
     * Membatalkan pendaftaran event.
     */
    public function cancelRegistration(EventRegistration $registration)
    {
        if ($registration->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($registration->event->start_time < now()) {
            return back()->with('error', 'Event sudah berlangsung, tidak dapat membatalkan pendaftaran.');
        }

        try {
            DB::beginTransaction();

            $registration->update(['status' => 'cancelled']);
            $registration->event->decrement('total_registrations');

            DB::commit();

            return back()->with('success', 'Pendaftaran berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * Upload bukti pembayaran (untuk event berbayar).
     */
    public function uploadPaymentProof(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($registration->event->price_type !== 'paid') {
            return back()->with('error', 'Event ini gratis, tidak perlu upload bukti pembayaran.');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            if ($registration->payment_proof && Storage::disk('public')->exists($registration->payment_proof)) {
                Storage::disk('public')->delete($registration->payment_proof);
            }

            $path = $request->file('payment_proof')->store('event-payments', 'public');
            $registration->update([
                'payment_proof' => $path,
                'status' => 'pending',
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload bukti pembayaran: ' . $e->getMessage());
        }
    }
}