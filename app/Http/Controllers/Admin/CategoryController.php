<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent')->withCount('courses');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->orderBy('order')->paginate(15);

        // Tambahkan parent_name untuk setiap kategori (opsional)
        $categories->getCollection()->transform(function ($cat) {
            $cat->parent_name = $cat->parent?->name;
            return $cat;
        });

        if ($request->wantsJson()) {
            return response()->json($categories);
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::parents()->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        // Validasi dengan penanganan AJAX
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'slug' => 'nullable|string|unique:categories,slug',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'icon' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:7',
                'order' => 'nullable|integer',
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

            $category = Category::create([
                'name' => $request->name,
                'slug' => $request->slug ?: \Illuminate\Support\Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'icon' => $request->icon,
                'color' => $request->color,
                'order' => $request->order ?? 0,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori berhasil ditambahkan.',
                    'data' => $category
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambah kategori: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Gagal menambah kategori: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        $parents = Category::where('id', '!=', $category->id)->parents()->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
                'slug' => ['nullable', 'string', Rule::unique('categories')->ignore($category->id)],
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'icon' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:7',
                'order' => 'nullable|integer',
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

            $category->update([
                'name' => $request->name,
                'slug' => $request->slug ?: \Illuminate\Support\Str::slug($request->name),
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'icon' => $request->icon,
                'color' => $request->color,
                'order' => $request->order ?? 0,
                'is_active' => $request->boolean('is_active'),
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori berhasil diperbarui.',
                    'data' => $category
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kategori: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            // Pindahkan child categories ke parent dari category ini (atau null)
            Category::where('parent_id', $category->id)->update(['parent_id' => $category->parent_id]);
            $category->courses()->detach();
            $category->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori berhasil dihapus.'
                ]);
            }
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menghapus kategori.');
        }
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return response()->json([
            'success' => true,
            'is_active' => $category->is_active,
            'message' => 'Status kategori berhasil diubah.'
        ]);
    }

    public function apiList()
    {
        $categories = Category::active()->parents()->with([
            'children' => function ($q) {
                $q->active()->orderBy('order');
            }
        ])->withCount('courses')->orderBy('order')->get();

        return response()->json($categories);
    }
}