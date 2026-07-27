<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public const CONDITION_TYPES = [
        'course_completed' => 'Jumlah kursus diselesaikan',
        'quiz_passed' => 'Jumlah quiz lulus',
        'assignment_submitted' => 'Jumlah tugas dikumpulkan',
        'streak_days' => 'Jumlah hari streak belajar',
        'forum_posts' => 'Jumlah post forum',
        'total_points' => 'Total poin terkumpul',
    ];

    public function index(Request $request)
    {
        $query = Achievement::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $achievements = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.achievements.index', [
            'achievements' => $achievements,
            'conditionTypes' => self::CONDITION_TYPES,
        ]);
    }

    public function create()
    {
        return view('admin.achievements.create', ['conditionTypes' => self::CONDITION_TYPES]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'description' => 'required|string',
            'points_reward' => 'required|integer|min:0',
            'condition_type' => 'required|in:' . implode(',', array_keys(self::CONDITION_TYPES)),
            'condition_value' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        Achievement::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'points_reward' => $request->points_reward,
            'condition_type' => $request->condition_type,
            'condition_value' => $request->condition_value,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement berhasil dibuat.');
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.edit', ['achievement' => $achievement, 'conditionTypes' => self::CONDITION_TYPES]);
    }

    public function update(Request $request, Achievement $achievement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'description' => 'required|string',
            'points_reward' => 'required|integer|min:0',
            'condition_type' => 'required|in:' . implode(',', array_keys(self::CONDITION_TYPES)),
            'condition_value' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $achievement->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
            'points_reward' => $request->points_reward,
            'condition_type' => $request->condition_type,
            'condition_value' => $request->condition_value,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();

        return response()->json(['success' => true, 'message' => 'Achievement berhasil dihapus.']);
    }

    public function toggleStatus(Achievement $achievement)
    {
        $achievement->update(['is_active' => !$achievement->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $achievement->is_active,
            'message' => 'Status achievement berhasil diubah.',
        ]);
    }
}
