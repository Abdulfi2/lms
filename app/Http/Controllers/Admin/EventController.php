<?php
// app/Http/Controllers/Admin/EventController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $query = Event::with('organizer');

        // Filter by role (event manager only see own events)
        if (auth()->user()->hasRole('event_manager') && !auth()->user()->hasRole('admin')) {
            $query->where('organizer_id', auth()->id());
        }

        // Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('speaker', 'like', "%{$request->search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date filter
        if ($request->filled('date_range')) {
            if ($request->date_range == 'upcoming') {
                $query->where('start_time', '>=', now());
            } elseif ($request->date_range == 'past') {
                $query->where('start_time', '<', now());
            }
        }

        $events = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => Event::count(),
            'published' => Event::where('status', 'published')->count(),
            'upcoming' => Event::where('status', 'published')->where('start_time', '>=', now())->count(),
            'draft' => Event::where('status', 'draft')->count(),
            'cancelled' => Event::where('status', 'cancelled')->count(),
            'total_participants' => EventRegistration::count(),
        ];

        // Get types for filter
        $eventTypes = Event::distinct()->pluck('type');

        if ($request->wantsJson()) {
            return response()->json($events);
        }

        return view('admin.events.index', compact('events', 'stats', 'eventTypes'));
    }

    /**
     * Show form to create new event.
     */
    public function create()
    {
        $organizers = User::role('event_manager')->orWhere('default_role', 'admin')->get(['id', 'name', 'email']);
        return view('admin.events.create', compact('organizers'));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:webinar,workshop,parenting,live_class,zoom_meeting,seminar',
            'category' => 'required|in:education,parenting,technology,business,health,other',
            'speaker' => 'nullable|string|max:255',
            'speaker_bio' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'zoom_link' => 'nullable|url',
            'meeting_id' => 'nullable|string|max:100',
            'passcode' => 'nullable|string|max:50',
            'max_participants' => 'nullable|integer|min:1',
            'price_type' => 'required|in:free,paid',
            'price' => 'required_if:price_type,paid|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published,cancelled',
            'is_featured' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['image', '_token', '_method']);
            $data['slug'] = Str::slug($request->title) . '-' . uniqid();
            $data['organizer_id'] = auth()->id();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('events', 'public');
                $data['image'] = $path;
            }

            $event = Event::create($data);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil dibuat.',
                    'data' => $event
                ]);
            }

            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat event: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal membuat event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load('organizer', 'registrations.user');

        $registrations = $event->registrations()
            ->with('user')
            ->latest()
            ->paginate(20);

        $statistics = [
            'total_registrations' => $event->registrations()->count(),
            'confirmed' => $event->registrations()->where('status', 'confirmed')->count(),
            'attended' => $event->registrations()->where('status', 'attended')->count(),
            'cancelled' => $event->registrations()->where('status', 'cancelled')->count(),
            'revenue' => $event->registrations()->where('status', 'confirmed')->sum('amount_paid'),
        ];

        return view('admin.events.show', compact('event', 'registrations', 'statistics'));
    }

    /**
     * Show form to edit event.
     */
    public function edit(Event $event)
    {
        // Check permission for event manager
        if (auth()->user()->hasRole('event_manager') && !auth()->user()->hasRole('admin')) {
            if ($event->organizer_id !== auth()->id()) {
                abort(403, 'Anda hanya bisa mengedit event sendiri.');
            }
        }

        $organizers = User::role('event_manager')->orWhere('default_role', 'admin')->get(['id', 'name', 'email']);
        return view('admin.events.edit', compact('event', 'organizers'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        // Check permission
        if (auth()->user()->hasRole('event_manager') && !auth()->user()->hasRole('admin')) {
            if ($event->organizer_id !== auth()->id()) {
                abort(403);
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:webinar,workshop,parenting,live_class,zoom_meeting,seminar',
            'category' => 'required|in:education,parenting,technology,business,health,other',
            'speaker' => 'nullable|string|max:255',
            'speaker_bio' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'zoom_link' => 'nullable|url',
            'meeting_id' => 'nullable|string|max:100',
            'passcode' => 'nullable|string|max:50',
            'max_participants' => 'nullable|integer|min:1',
            'price_type' => 'required|in:free,paid',
            'price' => 'required_if:price_type,paid|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published,cancelled',
            'is_featured' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['image', '_token', '_method']);

            if ($request->hasFile('image')) {
                if ($event->image && Storage::disk('public')->exists($event->image)) {
                    Storage::disk('public')->delete($event->image);
                }
                $path = $request->file('image')->store('events', 'public');
                $data['image'] = $path;
            }

            $event->update($data);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil diperbarui.',
                    'data' => $event
                ]);
            }

            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui event: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal memperbarui event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        // Check permission
        if (auth()->user()->hasRole('event_manager') && !auth()->user()->hasRole('admin')) {
            if ($event->organizer_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        try {
            DB::beginTransaction();

            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }

            // Delete registrations first
            $event->registrations()->delete();
            $event->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Event berhasil dihapus.'
                ]);
            }

            return redirect()->route('admin.events.index')
                ->with('success', 'Event berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus event: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal menghapus event: ' . $e->getMessage());
        }
    }

    /**
     * Toggle event status (publish/unpublish).
     */
    public function toggleStatus(Event $event)
    {
        if (auth()->user()->hasRole('event_manager') && !auth()->user()->hasRole('admin')) {
            if ($event->organizer_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $newStatus = $event->status === 'published' ? 'draft' : 'published';
        $event->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "Event berhasil di" . ($newStatus === 'published' ? 'publikasikan' : 'nonaktifkan')
        ]);
    }

    /**
     * Display event registrations.
     */
    public function registrations(Event $event)
    {
        if (auth()->user()->hasRole('event_manager') && !auth()->user()->hasRole('admin')) {
            if ($event->organizer_id !== auth()->id()) {
                abort(403);
            }
        }

        $registrations = $event->registrations()
            ->with('user')
            ->latest()
            ->paginate(30);

        $stats = [
            'total' => $event->registrations()->count(),
            'confirmed' => $event->registrations()->where('status', 'confirmed')->count(),
            'pending' => $event->registrations()->where('status', 'pending')->count(),
            'cancelled' => $event->registrations()->where('status', 'cancelled')->count(),
            'attended' => $event->registrations()->where('status', 'attended')->count(),
        ];

        return view('admin.events.registrations', compact('event', 'registrations', 'stats'));
    }

    /**
     * Update registration status.
     */
    public function updateRegistrationStatus(Request $request, EventRegistration $registration)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,attended'
        ]);

        $oldStatus = $registration->status;
        $registration->update([
            'status' => $request->status,
            'checked_in_at' => $request->status === 'attended' ? now() : $registration->checked_in_at,
        ]);

        // If status changed to confirmed, send notification (optional)
        if ($oldStatus !== 'confirmed' && $request->status === 'confirmed') {
            // Mail::to($registration->email)->send(new RegistrationConfirmed($registration));
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pendaftaran diperbarui.',
            'status' => $request->status
        ]);
    }

    /**
     * Bulk update registration status.
     */
    public function bulkUpdateRegistration(Request $request, Event $event)
    {
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
            'status' => 'required|in:pending,confirmed,cancelled,attended'
        ]);

        try {
            $count = EventRegistration::whereIn('id', $request->registration_ids)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => "{$count} pendaftaran berhasil diperbarui."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pendaftaran.'
            ], 500);
        }
    }

    /**
     * Export registrations to CSV.
     */
    public function exportRegistrations(Event $event)
    {
        if (auth()->user()->hasRole('event_manager') && !auth()->user()->hasRole('admin')) {
            if ($event->organizer_id !== auth()->id()) {
                abort(403);
            }
        }

        $registrations = $event->registrations()->with('user')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="registrations_' . $event->slug . '.csv"',
        ];

        $callback = function () use ($registrations) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama', 'Email', 'Telepon', 'Status', 'Tanggal Daftar', 'Check-in']);

            foreach ($registrations as $reg) {
                fputcsv($file, [
                    $reg->id,
                    $reg->name,
                    $reg->email,
                    $reg->phone,
                    $reg->status,
                    $reg->created_at->format('d/m/Y H:i'),
                    $reg->checked_in_at ? $reg->checked_in_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Duplicate event.
     */
    public function duplicate(Event $event)
    {
        try {
            DB::beginTransaction();

            $newEvent = $event->replicate();
            $newEvent->title = $event->title . ' (Copy)';
            $newEvent->slug = Str::slug($event->title) . '-copy-' . uniqid();
            $newEvent->status = 'draft';
            $newEvent->created_at = now();
            $newEvent->save();

            DB::commit();

            return redirect()->route('admin.events.edit', $newEvent)
                ->with('success', 'Event berhasil diduplikasi. Silakan edit sesuai kebutuhan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menduplikasi event: ' . $e->getMessage());
        }
    }

    /**
     * Get event statistics for dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total_events' => Event::count(),
            'published_events' => Event::where('status', 'published')->count(),
            'upcoming_events' => Event::where('status', 'published')->where('start_time', '>=', now())->count(),
            'total_registrations' => EventRegistration::count(),
            'confirmed_registrations' => EventRegistration::where('status', 'confirmed')->count(),
            'revenue' => EventRegistration::where('status', 'confirmed')->sum('amount_paid'),
        ];

        // Monthly registrations chart
        $monthlyData = EventRegistration::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'monthly_data' => $monthlyData
        ]);
    }
}