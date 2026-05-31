<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function __construct(private CategoryService $service)
    {
        $this->middleware('auth');
        // Permission middleware is handled by route middleware in web.php
        // No need for additional role middleware here
    }

    public function index(Request $request)
    {
        $query = Category::withCount('topics');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Sort functionality (apply before filtering by count for better performance)
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['name', 'slug', 'topics_count', 'created_at', 'updated_at'])) {
            if ($sortBy === 'topics_count') {
                $query->orderBy('topics_count', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        // Get results first
        $categories = $query->paginate(15)->withQueryString();
        
        // Filter by topics count (client-side filtering after getting results)
        // This is necessary because withCount creates an alias that's hard to filter in SQL
        if ($request->filled('topics_min') || $request->filled('topics_max')) {
            $minCount = $request->filled('topics_min') ? (int) $request->topics_min : 0;
            $maxCount = $request->filled('topics_max') ? (int) $request->topics_max : PHP_INT_MAX;
            
            $filtered = $categories->getCollection()->filter(function($category) use ($minCount, $maxCount) {
                $count = $category->topics_count ?? 0;
                return $count >= $minCount && $count <= $maxCount;
            });
            
            $categories->setCollection($filtered);
        }
        
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $category = new Category();
        return view('categories.create', compact('category'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $this->service->create(
                $validated['name'],
                $validated['slug'] ?? null,
                $validated['description'] ?? null
            );

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal membuat kategori: ' . $e->getMessage());
        }
    }

    public function show(Category $category)
    {
        $category->load('topics');
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $this->service->update(
                $category,
                $validated['name'],
                $validated['slug'] ?? null,
                $validated['description'] ?? null
            );

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            $this->service->delete($category);

            return back()->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    /**
     * Store category via AJAX (for topic form)
     */
    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $category = $this->service->create(
                $validated['name'],
                null, // slug will be auto-generated
                null  // no description needed
            );

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori: ' . $e->getMessage()
            ], 422);
        }
    }
}

