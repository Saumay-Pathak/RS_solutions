<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with('author');
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->get('status') !== '') {
            if ($request->get('status') === '1' || $request->get('status') === 'published') {
                $query->published();
            } elseif ($request->get('status') === '0' || $request->get('status') === 'draft') {
                $query->draft();
            }
        }

        if ($request->has('author_id') && $request->get('author_id')) {
            $query->where('author_id', $request->get('author_id'));
        }

        if ($request->has('category') && $request->get('category')) {
            $query->where('category', $request->get('category'));
        }

        $blogs = $query->latest()->paginate(15);
        $authors = User::where('status', true)->get();
        
        // Get unique categories
        $categories = Blog::distinct('category')->pluck('category')->filter();

        return view('admin.blogs.index', compact('blogs', 'authors', 'categories'));
    }

    public function create()
    {
        $authors = User::where('status', true)->get();
        return view('admin.blogs.create', compact('authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'author_id' => 'required|exists:users,_id',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $blogData = $request->only(['title', 'content', 'excerpt', 'author_id', 'category', 'meta_title', 'meta_description']);
        $blogData['slug'] = $request->slug ?: Str::slug($request->title);
        $blogData['status'] = $request->has('status');

        // Ensure slug is unique
        $originalSlug = $blogData['slug'];
        $counter = 1;
        while (Blog::where('slug', $blogData['slug'])->exists()) {
            $blogData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle tags
        if ($request->tags) {
            $blogData['tags'] = array_map('trim', explode(',', $request->tags));
        } else {
            $blogData['tags'] = [];
        }

        // Handle published date
        if ($request->published_at) {
            $blogData['published_at'] = Carbon::parse($request->published_at);
        } elseif ($blogData['status']) {
            $blogData['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $blogData['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        $blog = Blog::create($blogData);
        
        // Calculate reading time
        $blog->calculateReadingTime()->save();

        return redirect()->route('admin.blogs.index')
                        ->with('success', 'Blog post created successfully.');
    }

    public function show(Blog $blog)
    {
        $blog->load('author');
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $authors = User::where('status', true)->get();
        return view('admin.blogs.edit', compact('blog', 'authors'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->_id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'author_id' => 'required|exists:users,_id',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $blogData = $request->only(['title', 'content', 'excerpt', 'author_id', 'category', 'meta_title', 'meta_description']);
        $blogData['slug'] = $request->slug ?: Str::slug($request->title);
        $blogData['status'] = $request->has('status');

        // Ensure slug is unique (excluding current blog)
        $originalSlug = $blogData['slug'];
        $counter = 1;
        while (Blog::where('slug', $blogData['slug'])->where('_id', '!=', $blog->_id)->exists()) {
            $blogData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle tags
        if ($request->tags) {
            $blogData['tags'] = array_map('trim', explode(',', $request->tags));
        } else {
            $blogData['tags'] = [];
        }

        // Handle published date
        if ($request->published_at) {
            $blogData['published_at'] = Carbon::parse($request->published_at);
        } elseif ($blogData['status'] && !$blog->published_at) {
            $blogData['published_at'] = now();
        } elseif (!$blogData['status']) {
            $blogData['published_at'] = null;
        }

        if ($request->hasFile('featured_image')) {
            // Delete old featured image
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $blogData['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        // Handle remove featured image
        if ($request->has('remove_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $blogData['featured_image'] = null;
        }

        $blog->update($blogData);
        
        // Recalculate reading time
        $blog->calculateReadingTime()->save();

        return redirect()->route('admin.blogs.index')
                        ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        // Delete featured image if exists
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')
                        ->with('success', 'Blog post deleted successfully.');
    }

    public function toggleStatus(Blog $blog)
    {
        $status = !$blog->status;
        
        $updateData = ['status' => $status];
        
        // If publishing, set published_at if not already set
        if ($status && !$blog->published_at) {
            $updateData['published_at'] = now();
        } elseif (!$status) {
            $updateData['published_at'] = null;
        }
        
        $blog->update($updateData);
        
        return response()->json([
            'success' => true,
            'status' => $blog->status,
            'message' => 'Blog post status updated successfully.'
        ]);
    }
}