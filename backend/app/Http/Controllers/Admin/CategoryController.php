<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children']);
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status') === '1');
        }

        if ($request->has('parent_id') && $request->get('parent_id')) {
            $query->where('parent_id', $request->get('parent_id'));
        }

        $categories = $query->orderBy('sort_order')->paginate(15);
        $parentCategories = Category::where('parent_id', null)->where('status', true)->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = Category::where('parent_id', null)->where('status', true)->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $categoryData = $request->only(['name', 'description', 'parent_id', 'meta_title', 'meta_description', 'sort_order']);
        $categoryData['slug'] = $request->slug ?: Str::slug($request->name);
        $categoryData['status'] = $request->has('status');

        // Ensure slug is unique
        $originalSlug = $categoryData['slug'];
        $counter = 1;
        while (Category::where('slug', $categoryData['slug'])->exists()) {
            $categoryData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        if ($request->hasFile('image')) {
            $categoryData['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($categoryData);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::where('parent_id', null)
                                  ->where('_id', '!=', $category->_id)
                                  ->where('status', true)
                                  ->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->_id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Prevent category from being its own parent
        if ($request->parent_id === $category->_id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        $categoryData = $request->only(['name', 'description', 'parent_id', 'meta_title', 'meta_description', 'sort_order']);
        $categoryData['slug'] = $request->slug ?: Str::slug($request->name);
        $categoryData['status'] = $request->has('status');

        // Ensure slug is unique (excluding current category)
        $originalSlug = $categoryData['slug'];
        $counter = 1;
        while (Category::where('slug', $categoryData['slug'])->where('_id', '!=', $category->_id)->exists()) {
            $categoryData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $categoryData['image'] = $request->file('image')->store('categories', 'public');
        }

        // Handle remove image
        if ($request->has('remove_image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $categoryData['image'] = null;
        }

        $category->update($categoryData);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                            ->with('error', 'Cannot delete category that has associated products.');
        }

        // Check if category has child categories
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                            ->with('error', 'Cannot delete category that has child categories.');
        }

        // Delete category image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['status' => !$category->status]);
        
        return response()->json([
            'success' => true,
            'status' => $category->status,
            'message' => 'Category status updated successfully.'
        ]);
    }
}