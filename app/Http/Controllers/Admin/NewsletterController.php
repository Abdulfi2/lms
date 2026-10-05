<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($request->filled('search')) {
            $query->where('email', 'like', "%{$request->search}%");
        }

        $subscribers = $query->latest()->paginate(20)->withQueryString();

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber dihapus.');
    }
}
