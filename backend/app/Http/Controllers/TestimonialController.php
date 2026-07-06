<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display testimonials page
     */
    public function index()
    {
        $featuredTestimonials = Testimonial::active()
            ->featured()
            ->ordered()
            ->take(6)
            ->get();
            
        $allTestimonials = Testimonial::active()
            ->ordered()
            ->paginate(12);
            
        return view('testimonials', compact('featuredTestimonials', 'allTestimonials'));
    }

    /**
     * Get testimonials for API or AJAX requests
     */
    public function api(Request $request)
    {
        $query = Testimonial::active()->ordered();
        
        if ($request->has('featured')) {
            $query->featured();
        }
        
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }
        
        $testimonials = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $testimonials
        ]);
    }
}
