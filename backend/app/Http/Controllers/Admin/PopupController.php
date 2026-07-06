<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PopupController extends Controller
{
    /**
     * Display a listing of popups
     */
    public function index(Request $request)
    {
        $query = Popup::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $popups = $query->orderBy('priority', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        $types = Popup::getTypes();

        return view('admin.popups.index', compact('popups', 'types'));
    }

    /**
     * Show the form for creating a new popup
     */
    public function create()
    {
        return view('admin.popups.create', [
            'types' => Popup::getTypes(),
            'positions' => Popup::getPositions(),
            'sizes' => Popup::getSizes(),
            'frequencies' => Popup::getFrequencies(),
            'targetUsers' => Popup::getTargetUsers()
        ]);
    }

    /**
     * Store a newly created popup
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Popup::getTypes())),
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:20480',
            'video_url' => 'nullable|url',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'position' => 'required|string|in:' . implode(',', array_keys(Popup::getPositions())),
            'size' => 'required|string|in:' . implode(',', array_keys(Popup::getSizes())),
            'show_after' => 'nullable|integer|min:0',
            'show_on_pages' => 'nullable|array',
            'show_frequency' => 'required|string|in:' . implode(',', array_keys(Popup::getFrequencies())),
            'target_users' => 'required|string|in:' . implode(',', array_keys(Popup::getTargetUsers())),
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'nullable|integer|min:0|max:100',
            'background_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'border_color' => 'nullable|string',
            'border_radius' => 'nullable|string'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('popups', 'public');
        }

        // Handle styles
        $styles = [];
        if ($request->filled('background_color')) {
            $styles['background-color'] = $request->background_color;
        }
        if ($request->filled('text_color')) {
            $styles['color'] = $request->text_color;
        }
        if ($request->filled('border_color')) {
            $styles['border-color'] = $request->border_color;
        }
        if ($request->filled('border_radius')) {
            $styles['border-radius'] = $request->border_radius . 'px';
        }
        $validated['styles'] = $styles;

        // Set defaults
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = $validated['priority'] ?? 0;
        $validated['show_after'] = $validated['show_after'] ?? 3;

        Popup::create($validated);

        return redirect()->route('admin.popups.index')
                        ->with('success', 'Popup created successfully!');
    }

    /**
     * Display the specified popup
     */
    public function show(Popup $popup)
    {
        return view('admin.popups.show', compact('popup'));
    }

    /**
     * Show the form for editing the specified popup
     */
    public function edit(Popup $popup)
    {
        return view('admin.popups.edit', [
            'popup' => $popup,
            'types' => Popup::getTypes(),
            'positions' => Popup::getPositions(),
            'sizes' => Popup::getSizes(),
            'frequencies' => Popup::getFrequencies(),
            'targetUsers' => Popup::getTargetUsers()
        ]);
    }

    /**
     * Update the specified popup
     */
    public function update(Request $request, Popup $popup)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Popup::getTypes())),
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:20480',
            'video_url' => 'nullable|url',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'position' => 'required|string|in:' . implode(',', array_keys(Popup::getPositions())),
            'size' => 'required|string|in:' . implode(',', array_keys(Popup::getSizes())),
            'show_after' => 'nullable|integer|min:0',
            'show_on_pages' => 'nullable|array',
            'show_frequency' => 'required|string|in:' . implode(',', array_keys(Popup::getFrequencies())),
            'target_users' => 'required|string|in:' . implode(',', array_keys(Popup::getTargetUsers())),
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'nullable|integer|min:0|max:100',
            'background_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'border_color' => 'nullable|string',
            'border_radius' => 'nullable|string'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($popup->image && Storage::disk('public')->exists($popup->image)) {
                Storage::disk('public')->delete($popup->image);
            }
            $validated['image'] = $request->file('image')->store('popups', 'public');
        }

        // Handle remove image
        if ($request->has('remove_image')) {
            if ($popup->image && Storage::disk('public')->exists($popup->image)) {
                Storage::disk('public')->delete($popup->image);
            }
            $validated['image'] = null;
        }

        // Handle styles
        $styles = [];
        if ($request->filled('background_color')) {
            $styles['background-color'] = $request->background_color;
        }
        if ($request->filled('text_color')) {
            $styles['color'] = $request->text_color;
        }
        if ($request->filled('border_color')) {
            $styles['border-color'] = $request->border_color;
        }
        if ($request->filled('border_radius')) {
            $styles['border-radius'] = $request->border_radius . 'px';
        }
        $validated['styles'] = $styles;

        $validated['is_active'] = $request->boolean('is_active', $popup->is_active);

        $popup->update($validated);

        return redirect()->route('admin.popups.index')
                        ->with('success', 'Popup updated successfully!');
    }

    /**
     * Remove the specified popup
     */
    public function destroy(Popup $popup)
    {
        // Delete associated image
        if ($popup->image && Storage::disk('public')->exists($popup->image)) {
            Storage::disk('public')->delete($popup->image);
        }

        $popup->delete();

        return redirect()->route('admin.popups.index')
                        ->with('success', 'Popup deleted successfully!');
    }

    /**
     * Toggle popup status
     */
    public function toggleStatus(Popup $popup)
    {
        $popup->update(['is_active' => !$popup->is_active]);
        
        $status = $popup->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Popup {$status} successfully!");
    }

    /**
     * Preview popup
     */
    public function preview(Popup $popup)
    {
        return view('admin.popups.preview', compact('popup'));
    }

    /**
     * Get popups for frontend (API)
     */
    public function getActivePopups(Request $request)
    {
        $page = $request->get('page', 'all');
        
        $popups = Popup::shouldShow()
                      ->forPage($page)
                      ->orderBy('priority', 'desc')
                      ->get();

        return response()->json($popups);
    }
}