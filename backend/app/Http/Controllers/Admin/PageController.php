<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status') === '1');
        }

        if ($request->has('template') && $request->get('template')) {
            $query->where('template', $request->get('template'));
        }

        $pages = $query->orderBy('sort_order')->paginate(15);
        $systemPages = Page::getSystemPages();
        $templates = ['default', 'home', 'about', 'contact', 'services', 'custom'];

        return view('admin.pages.index', compact('pages', 'systemPages', 'templates'));
    }

    public function create()
    {
        $systemPages = Page::getSystemPages();
        $templates = ['default', 'home', 'about', 'contact', 'services', 'custom'];
        return view('admin.pages.create', compact('systemPages', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'template' => 'required|string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $pageData = $request->only(['title', 'content', 'excerpt', 'template', 'meta_title', 'meta_description', 'custom_css', 'custom_js', 'sort_order']);
        $pageData['slug'] = $request->slug ?: Str::slug($request->title);
        $pageData['status'] = $request->has('status');
        $pageData['sections'] = []; // Initialize empty sections array

        // Ensure slug is unique
        $originalSlug = $pageData['slug'];
        $counter = 1;
        while (Page::where('slug', $pageData['slug'])->exists()) {
            $pageData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        if ($request->hasFile('featured_image')) {
            $pageData['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        Page::create($pageData);

        return redirect()->route('admin.pages.index')
                        ->with('success', 'Page created successfully.');
    }

    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        $systemPages = Page::getSystemPages();
        $templates = ['default', 'home', 'about', 'contact', 'services', 'custom'];
        return view('admin.pages.edit', compact('page', 'systemPages', 'templates'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->_id,
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'template' => 'required|string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $pageData = $request->only(['title', 'content', 'excerpt', 'template', 'meta_title', 'meta_description', 'custom_css', 'custom_js', 'sort_order']);
        $pageData['slug'] = $request->slug ?: Str::slug($request->title);
        $pageData['status'] = $request->has('status');

        // Ensure slug is unique (excluding current page)
        $originalSlug = $pageData['slug'];
        $counter = 1;
        while (Page::where('slug', $pageData['slug'])->where('_id', '!=', $page->_id)->exists()) {
            $pageData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        if ($request->hasFile('featured_image')) {
            // Delete old featured image
            if ($page->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $pageData['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        // Handle remove featured image
        if ($request->has('remove_image')) {
            if ($page->featured_image) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $pageData['featured_image'] = null;
        }

        $page->update($pageData);

        return redirect()->route('admin.pages.index')
                        ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        // Delete featured image if exists
        if ($page->featured_image) {
            Storage::disk('public')->delete($page->featured_image);
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
                        ->with('success', 'Page deleted successfully.');
    }

    public function toggleStatus(Page $page)
    {
        $page->update(['status' => !$page->status]);
        
        return response()->json([
            'success' => true,
            'status' => $page->status,
            'message' => 'Page status updated successfully.'
        ]);
    }

    public function addSection(Request $request, Page $page)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $section = [
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'image' => $request->image,
            'link' => $request->link,
            'order' => $request->order ?: count($page->sections ?: []),
            'created_at' => now()->toISOString(),
        ];

        $page->addSection($section);
        $page->save();

        return response()->json([
            'success' => true,
            'message' => 'Section added successfully.',
            'section' => $section
        ]);
    }

    public function removeSection(Page $page, $index)
    {
        $page->removeSection($index);
        $page->save();

        return response()->json([
            'success' => true,
            'message' => 'Section removed successfully.'
        ]);
    }

    public function updateSection(Request $request, Page $page, $index)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $section = [
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'image' => $request->image,
            'link' => $request->link,
            'order' => $request->order,
            'updated_at' => now()->toISOString(),
        ];

        $page->updateSection($index, $section);
        $page->save();

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully.',
            'section' => $section
        ]);
    }
}