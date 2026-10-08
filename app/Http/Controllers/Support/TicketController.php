<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Daftar semua tiket. Tab 'pending' = butuh perhatian pertama (baru/menunggu
     * balasan support), 'all' = semua tiket tanpa filter status bawaan.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        $query = Ticket::with(['user', 'category'])->withCount('replies');

        // Filter status eksplisit dari dropdown lebih spesifik daripada status
        // bawaan tab 'pending' — kalau keduanya dipaksa aktif sekaligus, hasilnya
        // selalu kosong (mis. tab pending + filter status 'selesai').
        if ($tab === 'pending' && !$request->filled('status')) {
            $query->whereIn('status', ['baru', 'diproses']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();

        $pendingCount = Ticket::whereIn('status', ['baru', 'diproses'])->count();

        return view('support.tickets.index', compact('tickets', 'tab', 'pendingCount'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'category', 'replies.user']);

        return view('support.tickets.show', compact('ticket'));
    }

    /**
     * Balasan dari agent support. Otomatis set status 'menunggu_user' supaya
     * jelas giliran siapa merespons selanjutnya — kecuali agent secara eksplisit
     * menutup tiket lewat updateStatus().
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_staff_reply' => true,
        ]);

        $this->logActivity('ticket_reply', 'tickets', $ticket->id, null, null,
            Auth::user()->name . ' memberikan balasan pada tiket #' . $this->ticketCode($ticket));

        // Membalas tiket yang sudah selesai otomatis membuka kembali tiketnya
        // (lihat juga TicketController::reply untuk sisi pengguna) — supaya
        // tidak ada balasan yang "hilang" diam-diam di tiket yang dianggap tertutup.
        $ticket->update(['status' => 'menunggu_user', 'closed_at' => null]);

        return back()->with('success', 'Balasan terkirim ke pengguna.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:baru,diproses,menunggu_user,selesai',
        ]);

        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => $validated['status'],
            'closed_at' => $validated['status'] === 'selesai' ? now() : null,
        ]);

        if ($oldStatus !== $validated['status']) {
            $this->logActivity('ticket_status', 'tickets', $ticket->id, ['status' => $oldStatus], ['status' => $validated['status']],
                Auth::user()->name . ' mengubah status tiket #' . $this->ticketCode($ticket) . ' menjadi ' . $ticket->statusLabel());
        }

        return back()->with('success', 'Status tiket diperbarui.');
    }

    public function updatePriority(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'priority' => 'required|in:rendah,sedang,tinggi',
        ]);

        $ticket->update(['priority' => $validated['priority']]);

        return back()->with('success', 'Prioritas tiket diperbarui.');
    }

    /**
     * Buat kategori tiket baru langsung dari form (mirip pola quick-add
     * kategori artikel untuk author/editor).
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ticket_categories,name',
        ]);

        $category = TicketCategory::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'category' => ['id' => $category->id, 'name' => $category->name],
        ]);
    }

    private function ticketCode(Ticket $ticket): string
    {
        return str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT);
    }
}
