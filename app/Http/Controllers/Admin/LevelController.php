<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::orderBy('level_number')->paginate(20);

        return view('admin.levels.index', compact('levels'));
    }

    public function create()
    {
        return view('admin.levels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level_number' => 'required|integer|min:1|unique:levels,level_number',
            'points_required' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Level::create($request->only(['name', 'level_number', 'points_required', 'icon', 'description']));

        return redirect()->route('admin.levels.index')->with('success', 'Level berhasil dibuat.');
    }

    public function edit(Level $level)
    {
        return view('admin.levels.edit', compact('level'));
    }

    public function update(Request $request, Level $level)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level_number' => 'required|integer|min:1|unique:levels,level_number,' . $level->id,
            'points_required' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $level->update($request->only(['name', 'level_number', 'points_required', 'icon', 'description']));

        return redirect()->route('admin.levels.index')->with('success', 'Level berhasil diperbarui.');
    }

    public function destroy(Level $level)
    {
        $level->delete();

        return response()->json(['success' => true, 'message' => 'Level berhasil dihapus.']);
    }
}
