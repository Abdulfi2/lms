<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Daftar tiket bantuan milik user yang login.
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->with('category')
            ->withCount('replies')
            ->latest()
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = TicketCategory::where('is_active', true)->orderBy('name')->get();

        return view('tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:ticket_categories,id',
            'description' => 'required|string|max:5000',
            'priority' => 'required|in:rendah,sedang,tinggi',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'baru',
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil dikirim. Tim support akan segera menanggapi.');
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 403);

        $ticket->load(['category', 'replies.user']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Balasan dari pelapor tiket. Kalau tiket sebelumnya menunggu respons
     * pengguna, otomatis kembali ke 'diproses' supaya muncul lagi di antrian
     * support (lihat Support\TicketController).
     */
    public function reply(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_staff_reply' => false,
        ]);

        if ($ticket->status === 'menunggu_user') {
            $ticket->update(['status' => 'diproses']);
        } elseif ($ticket->status === 'selesai') {
            // Tiket yang sudah selesai dibuka lagi kalau pengguna membalas —
            // supaya balasan susulan tidak "hilang" diam-diam di tiket yang
            // dianggap sudah ditutup.
            $ticket->update(['status' => 'diproses', 'closed_at' => null]);
        }

        return back()->with('success', 'Balasan terkirim.');
    }
}
