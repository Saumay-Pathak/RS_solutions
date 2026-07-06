<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Solution::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', true);
            } elseif ($request->status === 'inactive') {
                $query->where('status', false);
            }
        }

        // Filter by featured
        if ($request->filled('featured')) {
            if ($request->featured === 'yes') {
                $query->where('featured', true);
            } elseif ($request->featured === 'no') {
                $query->where('featured', false);
            }
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $solutions = $query->ordered()->paginate(10)->appends($request->query());
        $categories = Solution::active()->distinct('category')->pluck('category')->filter();

        return view('admin.solutions.index', compact('solutions', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->getCategories();
        return view('admin.solutions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:solutions,slug',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:20480',
            'category' => 'nullable|string|max:100',
            'price_range' => 'nullable|string|max:100',
            'delivery_time' => 'nullable|string|max:100',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
            'status' => 'boolean',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('solutions', 'public');
            $validated['image'] = $imagePath;
        }

        // Set default values
        $validated['status'] = $request->boolean('status', true);
        $validated['featured'] = $request->boolean('featured', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Solution::create($validated);

        return redirect()->route('admin.solutions.index')
                        ->with('success', 'Solution created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Solution $solution)
    {
        return view('admin.solutions.show', compact('solution'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solution $solution)
    {
        $categories = $this->getCategories();
        return view('admin.solutions.edit', compact('solution', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solution $solution)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('solutions', 'slug')->ignore($solution->id)],
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:20480',
            'category' => 'nullable|string|max:100',
            'price_range' => 'nullable|string|max:100',
            'delivery_time' => 'nullable|string|max:100',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'benefits' => 'nullable|array', 
            'benefits.*' => 'string|max:255',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
            'status' => 'boolean',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($solution->image && Storage::disk('public')->exists($solution->image)) {
                Storage::disk('public')->delete($solution->image);
            }
            
            $imagePath = $request->file('image')->store('solutions', 'public');
            $validated['image'] = $imagePath;
        }

        // Handle remove image (either remove_image or remove_current_image for compatibility)
        if ($request->has('remove_image') || $request->has('remove_current_image')) {
            if ($solution->image && Storage::disk('public')->exists($solution->image)) {
                Storage::disk('public')->delete($solution->image);
            }
            $validated['image'] = null;
        }

        // Set boolean values
        $validated['status'] = $request->boolean('status', true);
        $validated['featured'] = $request->boolean('featured', false);
        $validated['sort_order'] = $validated['sort_order'] ?? $solution->sort_order;

        $solution->update($validated);

        return redirect()->route('admin.solutions.index')
                        ->with('success', 'Solution updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solution $solution)
    {
        // Delete associated image
        if ($solution->image && Storage::disk('public')->exists($solution->image)) {
            Storage::disk('public')->delete($solution->image);
        }

        $solution->delete();

        return redirect()->route('admin.solutions.index')
                        ->with('success', 'Solution deleted successfully!');
    }

    /**
     * Toggle solution status
     */
    public function toggleStatus(Solution $solution)
    {
        $solution->update(['status' => !$solution->status]);
        
        $status = $solution->status ? 'activated' : 'deactivated';
        return back()->with('success', "Solution {$status} successfully!");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Solution $solution)
    {
        $solution->update(['featured' => !$solution->featured]);
        
        $status = $solution->featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Solution {$status} successfully!");
    }

    /**
     * Get available categories
     */
    private function getCategories()
    {
        return [
            'Web Development',
            'Mobile Development', 
            'E-commerce',
            'Digital Marketing',
            'Cloud Solutions',
            'AI & Machine Learning',
            'Consulting',
            'Custom Software',
            'Other'
        ];
    }
}
