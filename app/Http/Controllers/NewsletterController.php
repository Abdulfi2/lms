<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => $validated['email']]);

        if (!$subscriber->wasRecentlyCreated) {
            return back()->with('success', 'Email ini sudah terdaftar di newsletter kami.');
        }

        return back()->with('success', 'Terima kasih sudah berlangganan! Artikel terbaru akan dikirim ke email Anda.');
    }
}
