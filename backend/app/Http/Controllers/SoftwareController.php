<?php

namespace App\Http\Controllers;

use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoftwareController extends Controller
{
    /**
     * Display software page
     */
    public function index(Request $request)
    {
        $query = Software::active()->ordered();
        
        // Filter by main category
        if ($request->has('category') && $request->category) {
            $query->where('main_category', $request->category);
        }
        
        // Filter by sub category
        if ($request->has('sub_category') && $request->sub_category) {
            $query->where('sub_category', $request->sub_category);
        }
        
        // Filter by free/paid
        if ($request->has('type')) {
            if ($request->type === 'free') {
                $query->where('is_free', true);
            } elseif ($request->type === 'paid') {
                $query->where('is_free', false);
            }
        }
        
        $featuredSoftware = Software::active()
            ->featured()
            ->ordered()
            ->take(6)
            ->get();
            
        $allSoftware = $query->paginate(12);
        $categories = Software::active()->distinct('main_category')->pluck('main_category')->filter();
            
        return view('software', compact('featuredSoftware', 'allSoftware', 'categories'));
    }

    /**
     * Display single software
     */
    public function show($slug)
    {
        $software = Software::active()
            ->where(function($query) use ($slug) {
                $query->where('slug', $slug)->orWhere('_id', $slug);
            })
            ->firstOrFail();
            
        $relatedSoftware = Software::active()
            ->where('main_category', $software->main_category)
            ->where('_id', '!=', $software->id)
            ->ordered()
            ->take(4)
            ->get();
            
        return view('software.show', compact('software', 'relatedSoftware'));
    }

    /**
     * Handle software download
     */
    public function download($slug)
    {
        $software = Software::active()
            ->where(function($query) use ($slug) {
                $query->where('slug', $slug)->orWhere('_id', $slug);
            })
            ->firstOrFail();
            
        if (!$software->hasDownload()) {
            abort(404, 'Download not available');
        }
        
        // Increment download count
        $software->incrementDownloadCount();
        
        // If it's an external URL, redirect to it
        if ($software->external_url) {
            return redirect($software->external_url);
        }
        
        // If it's a file, serve the download
        if ($software->file && Storage::exists($software->file)) {
            $filename = $software->title . '-v' . ($software->version ?: '1.0') . '.' . pathinfo($software->file, PATHINFO_EXTENSION);
            return Storage::download($software->file, $filename);
        }
        
        abort(404, 'File not found');
    }

    /**
     * Get software for API or AJAX requests
     */
    public function api(Request $request)
    {
        $query = Software::active()->ordered();
        
        if ($request->has('featured')) {
            $query->featured();
        }
        
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }
        
        if ($request->has('free_only')) {
            $query->where('is_free', true);
        }
        
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }
        
        $software = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $software
        ]);
    }
}
