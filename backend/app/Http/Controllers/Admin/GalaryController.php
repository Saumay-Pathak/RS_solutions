<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = GalleryItem::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status') === '1');
        }

        $items = $query->ordered()->paginate(15);

        return view('admin.galary.index', compact('items'));
    }

    public function create()
    {
        return view('admin.galary.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:image,video',
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,webp,mp4,webm,ogg|max:204800',
        ]);

        $slug = Str::slug($request->input('title'));
        $slug = $this->ensureUniqueSlug($slug);

        $path = $request->file('file')->store('gallery', 'public');

        $item = GalleryItem::create([
            'title' => $request->input('title'),
            'slug' => $slug,
            'type' => $request->input('type'),
            'file' => $path,
            'status' => true,
            'sort_order' => 0,
        ]);

        // Use direct public file URL for convenience
        $publicUrl = $item->file_url;

        return redirect()->route('admin.galary.index')
                         ->with('success', 'Item uploaded. Direct link: ' . $publicUrl);
    }

    public function toggleStatus(GalleryItem $item)
    {
        $item->status = !$item->status;
        $item->save();
        return back()->with('success', 'Status updated successfully.');
    }

    public function destroy(GalleryItem $item)
    {
        if ($item->file) {
            Storage::disk('public')->delete($item->file);
        }
        $item->delete();
        return back()->with('success', 'Item deleted successfully.');
    }

    private function ensureUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $i = 1;
        while (GalleryItem::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }
        return $slug;
    }
}