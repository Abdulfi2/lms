<?php
// app/Http/Controllers/PublicEventController.php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('organizer')
            ->where('status', 'published')
            ->where('start_time', '>=', now());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $events = $query->orderBy('start_time')->paginate(12);
        $upcomingEvents = Event::where('status', 'published')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(3)
            ->get();

        return view('public.events.index', compact('events', 'upcomingEvents'));
    }

    public function show($slug)
    {
        $event = Event::with('organizer', 'registrations.user')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $isRegistered = false;
        $registration = null;
        if (auth()->check()) {
            $registration = EventRegistration::where('event_id', $event->id)
                ->where('user_id', auth()->id())
                ->first();
            $isRegistered = !is_null($registration);
        }

        $relatedEvents = Event::where('status', 'published')
            ->where('id', '!=', $event->id)
            ->where(function ($q) use ($event) {
                $q->where('type', $event->type)
                    ->orWhere('category', $event->category);
            })
            ->limit(3)
            ->get();

        return view('public.events.show', compact('event', 'isRegistered', 'registration', 'relatedEvents'));
    }

    public function register(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->where('status', 'published')->firstOrFail();

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mendaftar.');
        }

        $exists = EventRegistration::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah terdaftar di event ini.');
        }

        if ($event->max_participants && $event->total_registrations >= $event->max_participants) {
            return back()->with('error', 'Maaf, kuota peserta sudah penuh.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'status' => $event->price_type === 'free' ? 'confirmed' : 'pending',
                'amount_paid' => $event->price,
            ]);

            $event->increment('total_registrations');

            DB::commit();

            $message = $event->price_type === 'free'
                ? 'Pendaftaran berhasil! Silakan cek email Anda untuk detail event.'
                : 'Pendaftaran berhasil! Silakan lakukan pembayaran untuk mengkonfirmasi kehadiran.';

            return redirect()->route('events.show', $event->slug)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }
}