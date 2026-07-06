<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HeroSlideController extends Controller
{
    public function index(Request $request)
    {
        $query = HeroSlide::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('button_text', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', (bool) $request->status);
        }

        // Filter by content position
        if ($request->has('position') && $request->position) {
            $query->where('content_position', $request->position);
        }

        $slides = $query->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => HeroSlide::count(),
            'active' => HeroSlide::where('is_active', true)->count(),
            'inactive' => HeroSlide::where('is_active', false)->count(),
        ];

        return view('admin.hero-slides.index', compact('slides', 'stats'));
    }

    public function create()
    {
        $nextOrder = HeroSlide::getNextOrder();
        return view('admin.hero-slides.create', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'content_file' => 'nullable|file|mimes:htm,html,txt|max:10240',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'image_alt' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
            'button_style' => 'nullable|string|max:100',
            'secondary_button_text' => 'nullable|string|max:100',
            'secondary_button_link' => 'nullable|string|max:500',
            'secondary_button_style' => 'nullable|string|max:100',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'background_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
            'content_position' => 'nullable|string|in:left,center,right',
            'animation_type' => 'nullable|string|max:50',
            'auto_play_delay' => 'nullable|integer|min:1000|max:20000',
            'display_from' => 'nullable|date',
            // Allow end date without start date; only enforce ordering when both provided
            'display_to' => 'nullable|date',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('hero-slides', $imageName, 'public');
            $validated['image'] = $imagePath;
            
            Log::info('Hero slide image uploaded', [
                'path' => $imagePath,
                'name' => $imageName
            ]);
        }

        // Handle HTML content file upload
        if ($request->hasFile('content_file')) {
            $file = $request->file('content_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('hero-slides/html', $fileName, 'public');
            $validated['content_file'] = $filePath;

            Log::info('Hero slide content file uploaded', [
                'path' => $filePath,
                'name' => $fileName
            ]);
        }

        // Normalize boolean from checkbox (0/1) and hidden default
        $validated['is_active'] = $request->boolean('is_active');

        $slide = HeroSlide::create($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide created successfully!');
    }

    public function show(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.show', compact('heroSlide'));
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.edit', compact('heroSlide'));
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'content_file' => 'nullable|file|mimes:htm,html,txt|max:10240',
            'remove_content_file' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'image_alt' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
            'button_style' => 'nullable|string|max:100',
            'secondary_button_text' => 'nullable|string|max:100',
            'secondary_button_link' => 'nullable|string|max:500',
            'secondary_button_style' => 'nullable|string|max:100',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'background_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
            'content_position' => 'nullable|string|in:left,center,right',
            'animation_type' => 'nullable|string|max:50',
            'auto_play_delay' => 'nullable|integer|min:1000|max:20000',
            'display_from' => 'nullable|date',
            // Allow end date without start date; only enforce ordering when both provided
            'display_to' => 'nullable|date',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroSlide->image && Storage::disk('public')->exists($heroSlide->image)) {
                Storage::disk('public')->delete($heroSlide->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('hero-slides', $imageName, 'public');
            $validated['image'] = $imagePath;
            
            Log::info('Hero slide image updated', [
                'path' => $imagePath,
                'name' => $imageName
            ]);
        }

        // Handle remove image
        if ($request->has('remove_image')) {
            if ($heroSlide->image && Storage::disk('public')->exists($heroSlide->image)) {
                Storage::disk('public')->delete($heroSlide->image);
            }
            $validated['image'] = null;
        }

        // Handle HTML content file upload/removal
        if ($request->boolean('remove_content_file')) {
            if ($heroSlide->content_file && Storage::disk('public')->exists($heroSlide->content_file)) {
                Storage::disk('public')->delete($heroSlide->content_file);
            }
            $validated['content_file'] = null;
        } elseif ($request->hasFile('content_file')) {
            if ($heroSlide->content_file && Storage::disk('public')->exists($heroSlide->content_file)) {
                Storage::disk('public')->delete($heroSlide->content_file);
            }
            $file = $request->file('content_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('hero-slides/html', $fileName, 'public');
            $validated['content_file'] = $filePath;

            Log::info('Hero slide content file updated', [
                'path' => $filePath,
                'name' => $fileName
            ]);
        }

        // Normalize boolean from checkbox (0/1) and hidden default
        $validated['is_active'] = $request->boolean('is_active');

        $heroSlide->update($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide updated successfully!');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        // Delete image if exists
        if ($heroSlide->image && Storage::disk('public')->exists($heroSlide->image)) {
            Storage::disk('public')->delete($heroSlide->image);
        }

        // Delete content file if exists
        if ($heroSlide->content_file && Storage::disk('public')->exists($heroSlide->content_file)) {
            Storage::disk('public')->delete($heroSlide->content_file);
        }

        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide deleted successfully!');
    }

    public function toggleStatus(HeroSlide $heroSlide)
    {
        $heroSlide->is_active = !$heroSlide->is_active;
        $heroSlide->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'is_active' => $heroSlide->is_active
        ]);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|integer'
        ]);

        foreach ($request->orders as $id => $order) {
            HeroSlide::where('_id', $id)->update(['order' => $order]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully!'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|string'
        ]);

        foreach ($request->ids as $id) {
            $slide = HeroSlide::find($id);
            if ($slide) {
                // Delete image if exists
                if ($slide->image && Storage::disk('public')->exists($slide->image)) {
                    Storage::disk('public')->delete($slide->image);
                }
                // Delete content file if exists
                if ($slide->content_file && Storage::disk('public')->exists($slide->content_file)) {
                    Storage::disk('public')->delete($slide->content_file);
                }
                $slide->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' slide(s) deleted successfully!'
        ]);
    }

    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|string',
            'status' => 'required|boolean'
        ]);

        HeroSlide::whereIn('_id', $request->ids)->update(['is_active' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' slide(s) updated successfully!'
        ]);
    }
}
