<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public const TYPES = [
        'course' => 'Kursus diselesaikan',
        'quiz' => 'Quiz lulus',
        'assignment' => 'Tugas dikumpulkan',
        'streak' => 'Streak belajar (hari)',
        'forum' => 'Post forum',
        'special' => 'Khusus (diberikan manual)',
    ];

    public function index(Request $request)
    {
        $query = Badge::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $badges = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.badges.index', ['badges' => $badges, 'types' => self::TYPES]);
    }

    public function create()
    {
        return view('admin.badges.create', ['types' => self::TYPES]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'description' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'required_value' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        Badge::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'icon' => $request->icon,
            'color' => $request->color ?: '#FBBF24',
            'description' => $request->description,
            'type' => $request->type,
            'required_value' => $request->required_value,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.badges.index')->with('success', 'Badge berhasil dibuat.');
    }

    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', ['badge' => $badge, 'types' => self::TYPES]);
    }

    public function update(Request $request, Badge $badge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'description' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'required_value' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $badge->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'color' => $request->color ?: '#FBBF24',
            'description' => $request->description,
            'type' => $request->type,
            'required_value' => $request->required_value,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.badges.index')->with('success', 'Badge berhasil diperbarui.');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();

        return response()->json(['success' => true, 'message' => 'Badge berhasil dihapus.']);
    }

    public function toggleStatus(Badge $badge)
    {
        $badge->update(['is_active' => !$badge->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $badge->is_active,
            'message' => 'Status badge berhasil diubah.',
        ]);
    }
}
