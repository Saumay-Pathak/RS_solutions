<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;

class GalaryController extends Controller
{
    public function index(Request $request)
    {
        $items = GalleryItem::active()->ordered()->paginate(20);
        return view('galary.index', compact('items'));
    }

    public function show($slug)
    {
        $item = GalleryItem::where('slug', $slug)->firstOrFail();
        return view('galary.show', compact('item'));
    }
}