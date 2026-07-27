<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageReplied;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'new');

        $messages = ContactMessage::where('status', $status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.contact-messages.index', compact('messages', 'status'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if ($contactMessage->status === 'new') {
            $contactMessage->update(['status' => 'read']);
        }

        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    public function reply(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ]);

        $contactMessage->update([
            'admin_reply' => $validated['admin_reply'],
            'status' => 'replied',
            'replied_by' => auth()->id(),
            'replied_at' => now(),
        ]);

        try {
            Mail::to($contactMessage->email)->send(new ContactMessageReplied($contactMessage));
        } catch (\Throwable $e) {
            \Log::error('Gagal kirim email balasan kontak: ' . $e->getMessage());
        }

        return back()->with('success', 'Balasan berhasil dikirim ke ' . $contactMessage->email . '.');
    }

    public function close(ContactMessage $contactMessage)
    {
        $contactMessage->update(['status' => 'closed']);

        return back()->with('success', 'Pesan ditutup.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Pesan dihapus.');
    }
}
