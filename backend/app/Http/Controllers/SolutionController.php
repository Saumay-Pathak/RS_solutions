<?php

namespace App\Http\Controllers;

use App\Models\Solution;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    /**
     * Display solutions page
     */
    public function index(Request $request)
    {
        $query = Solution::active()->ordered();
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        $featuredSolutions = Solution::active()
            ->featured()
            ->ordered()
            ->take(6)
            ->get();
            
        $allSolutions = $query->paginate(12);
        $categories = Solution::active()->distinct('category')->pluck('category')->filter();
            
        return view('solutions', compact('featuredSolutions', 'allSolutions', 'categories'));
    }

    /**
     * Display single solution
     */
    public function show($slug)
    {
        $solution = Solution::active()
            ->where(function($query) use ($slug) {
                $query->where('slug', $slug)->orWhere('_id', $slug);
            })
            ->firstOrFail();
            
        $relatedSolutions = Solution::active()
            ->where('category', $solution->category)
            ->where('_id', '!=', $solution->id)
            ->ordered()
            ->take(3)
            ->get();
            
        return view('solutions.show', compact('solution', 'relatedSolutions'));
    }

    /**
     * Get solutions for API or AJAX requests
     */
    public function api(Request $request)
    {
        $query = Solution::active()->ordered();
        
        if ($request->has('featured')) {
            $query->featured();
        }
        
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }
        
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }
        
        $solutions = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $solutions
        ]);
    }
}
