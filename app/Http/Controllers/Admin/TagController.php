<?php
// app/Http/Controllers/Admin/TagController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $tags = $query->orderBy('name')->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($tags);
        }

        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:tags,name',
                'slug' => 'nullable|string|unique:tags,slug',
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:7',
                'is_active' => 'nullable|boolean',
            ]);
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            DB::beginTransaction();

            $tag = Tag::create([
                'name' => $request->name,
                'slug' => $request->slug ?: \Illuminate\Support\Str::slug($request->name),
                'description' => $request->description,
                'color' => $request->color,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tag berhasil ditambahkan.',
                    'data' => $tag
                ]);
            }

            return redirect()->route('admin.tags.index')
                ->with('success', 'Tag berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambah tag: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Gagal menambah tag: ' . $e->getMessage());
        }
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('tags')->ignore($tag->id)],
                'slug' => ['nullable', 'string', Rule::unique('tags')->ignore($tag->id)],
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:7',
                'is_active' => 'nullable|boolean',
            ]);
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            DB::beginTransaction();

            $tag->update([
                'name' => $request->name,
                'slug' => $request->slug ?: \Illuminate\Support\Str::slug($request->name),
                'description' => $request->description,
                'color' => $request->color,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tag berhasil diperbarui.',
                    'data' => $tag
                ]);
            }

            return redirect()->route('admin.tags.index')
                ->with('success', 'Tag berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui tag: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Gagal memperbarui tag: ' . $e->getMessage());
        }
    }

    public function destroy(Tag $tag)
    {
        try {
            DB::beginTransaction();
            // Hapus relasi taggables
            $tag->taggables()->delete();
            $tag->delete();
            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tag berhasil dihapus.'
                ]);
            }
            return redirect()->route('admin.tags.index')->with('success', 'Tag berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus tag: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menghapus tag.');
        }
    }

    public function toggleStatus(Tag $tag)
    {
        $tag->update(['is_active' => !$tag->is_active]);
        return response()->json([
            'success' => true,
            'is_active' => $tag->is_active,
            'message' => 'Status tag berhasil diubah.'
        ]);
    }

    public function apiList()
    {
        $tags = Tag::active()->orderBy('name')->get();
        return response()->json($tags);
    }
}