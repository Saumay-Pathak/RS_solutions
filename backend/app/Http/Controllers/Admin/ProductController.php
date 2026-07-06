<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->get('status') === '1') {
                $query->where('status', true);
            } elseif ($request->get('status') === '0') {
                $query->where('status', false);
            }
        }

        if ($request->filled('featured')) {
            if ($request->get('featured') === '1') {
                $query->where('featured', true);
            } elseif ($request->get('featured') === '0') {
                $query->where(function($q) {
                    $q->where('featured', false)
                      ->orWhereNull('featured');
                });
            }
        }

        if ($request->filled('category_id')) {
            $catId = $request->get('category_id');
            try {
                $query->where('category_id', new ObjectId($catId));
            } catch (\Throwable $e) {
                $query->where('category_id', $catId);
            }
        }

        $products = $query->orderBy('sort_order')->paginate(15);
        $categories = Category::where('status', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('status', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:mongodb.products,slug',
            'description' => 'required|string',
            'a_plus_content' => 'nullable|string',
            'a_plus_content_file' => 'nullable|file|max:10240',
            'category_id' => 'required|exists:mongodb.categories,_id',
            // Backward compatibility: allow old features[] while supporting new feature_titles/icons
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'feature_titles' => 'nullable|array',
            'feature_titles.*' => 'string|max:255',
            'feature_icons' => 'nullable|array',
            'feature_icons.*' => 'nullable|string|max:255',
            'specification_titles' => 'nullable|array',
            'specification_values' => 'nullable|array',
            'specification_titles.*' => 'string|max:255',
            'specification_values.*' => 'string|max:500',
            'faq_questions' => 'nullable|array',
            'faq_answers' => 'nullable|array',
            'faq_questions.*' => 'nullable|string|max:255',
            'faq_answers.*' => 'nullable|string|max:2000',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:20480',
            'datasheet_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'connection_diagram_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'user_manual_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'catalogue_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'datasheet_document.max' => 'Datasheet must be no larger than 200 MB.',
            'connection_diagram_document.max' => 'Connection diagram must be no larger than 200 MB.',
            'user_manual_document.max' => 'User manual must be no larger than 200 MB.',
            'catalogue_document.max' => 'Catalogue document must be no larger than 200 MB.',
        ]);

        $productData = $request->only(['title', 'description', 'a_plus_content', 'category_id', 'meta_title', 'meta_description', 'sort_order']);
        // Ensure category_id is stored as ObjectId when valid
        try {
            $productData['category_id'] = new ObjectId((string) $productData['category_id']);
        } catch (\Throwable $e) {
            $productData['category_id'] = (string) $productData['category_id'];
        }
        $productData['slug'] = $request->slug ?: Str::slug($request->title);
        $productData['status'] = $request->has('status');

        // Ensure slug is unique
        $originalSlug = $productData['slug'];
        $counter = 1;
        while (Product::where('slug', $productData['slug'])->exists()) {
            $productData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle features with optional icons
        if ($request->feature_titles) {
            $features = [];
            foreach ($request->feature_titles as $i => $title) {
                $title = trim($title ?? '');
                if ($title !== '') {
                    $features[] = [
                        'title' => $title,
                        'icon' => $request->feature_icons[$i] ?? ''
                    ];
                }
            }
            $productData['features'] = $features;
        } else {
            // Fallback to legacy features[] strings
            $productData['features'] = $request->features ? array_filter($request->features) : [];
        }

        // Handle specifications
        $specifications = [];
        if ($request->specification_titles && $request->specification_values) {
            foreach ($request->specification_titles as $index => $title) {
                if (!empty($title) && !empty($request->specification_values[$index])) {
                    $specifications[] = [
                        'title' => $title,
                        'value' => $request->specification_values[$index]
                    ];
                }
            }
        }
        $productData['specifications'] = $specifications;

        // Handle FAQs
        $faqs = [];
        if ($request->faq_questions && $request->faq_answers) {
            foreach ($request->faq_questions as $index => $question) {
                $answer = $request->faq_answers[$index] ?? null;
                if (!empty($question) && !empty($answer)) {
                    $faqs[] = [
                        'question' => $question,
                        'answer' => $answer,
                    ];
                }
            }
        }
        $productData['faqs'] = $faqs;

        // Handle images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products/images', 'public');
            }
        }
        $productData['images'] = $images;

        // Handle product documents
        if ($request->hasFile('datasheet_document')) {
            $productData['datasheet_document'] = $request->file('datasheet_document')->store('products/datasheets', 'public');
        }
        if ($request->hasFile('connection_diagram_document')) {
            $productData['connection_diagram_document'] = $request->file('connection_diagram_document')->store('products/connection-diagrams', 'public');
        }
        if ($request->hasFile('user_manual_document')) {
            $productData['user_manual_document'] = $request->file('user_manual_document')->store('products/user-manuals', 'public');
        }
        if ($request->hasFile('catalogue_document')) {
            $productData['catalogue_document'] = $request->file('catalogue_document')->store('products/catalogues', 'public');
        }

        // Handle A+ content via uploaded HTML file (overrides textarea if present)
        if ($request->hasFile('a_plus_content_file')) {
            $productData['a_plus_content'] = file_get_contents($request->file('a_plus_content_file')->getRealPath());
        }

        Product::create($productData);

        return redirect()->route('admin.products.index')
                        ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:mongodb.products,slug,' . $product->_id . ',_id',
            'description' => 'required|string',
            'a_plus_content' => 'nullable|string',
            'a_plus_content_file' => 'nullable|file|max:10240',
            'category_id' => 'required|exists:mongodb.categories,_id',
            // Backward compatibility: allow old features[] while supporting new feature_titles/icons
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'feature_titles' => 'nullable|array',
            'feature_titles.*' => 'string|max:255',
            'feature_icons' => 'nullable|array',
            'feature_icons.*' => 'nullable|string|max:255',
            'specification_titles' => 'nullable|array',
            'specification_values' => 'nullable|array',
            'specification_titles.*' => 'string|max:255',
            'specification_values.*' => 'string|max:500',
            'faq_questions' => 'nullable|array',
            'faq_answers' => 'nullable|array',
            'faq_questions.*' => 'nullable|string|max:255',
            'faq_answers.*' => 'nullable|string|max:2000',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:20480',
            'datasheet_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'connection_diagram_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'user_manual_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'catalogue_document' => 'nullable|file|mimes:pdf,doc,docx|max:204800',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'datasheet_document.max' => 'Datasheet must be no larger than 200 MB.',
            'connection_diagram_document.max' => 'Connection diagram must be no larger than 200 MB.',
            'user_manual_document.max' => 'User manual must be no larger than 200 MB.',
            'catalogue_document.max' => 'Catalogue document must be no larger than 200 MB.',
        ]);
        try {
            $productData = $request->only(['title', 'description', 'a_plus_content', 'category_id', 'meta_title', 'meta_description', 'sort_order']);
            // Ensure category_id is stored as ObjectId when valid
            try {
                $productData['category_id'] = new ObjectId((string) $productData['category_id']);
            } catch (\Throwable $e) {
                $productData['category_id'] = (string) $productData['category_id'];
            }
            $productData['slug'] = $request->slug ?: Str::slug($request->title);
            $productData['status'] = $request->has('status');

            // Ensure slug is unique (excluding current product)
            $originalSlug = $productData['slug'];
            $counter = 1;
            while (Product::where('slug', $productData['slug'])->where('_id', '!=', $product->_id)->exists()) {
                $productData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Handle features with optional icons
            if ($request->feature_titles) {
                $features = [];
                foreach ($request->feature_titles as $i => $title) {
                    $title = trim($title ?? '');
                    if ($title !== '') {
                        $features[] = [
                            'title' => $title,
                            'icon' => $request->feature_icons[$i] ?? ''
                        ];
                    }
                }
                $productData['features'] = $features;
            } else {
                // Fallback to legacy features[] strings
                $productData['features'] = $request->features ? array_filter($request->features) : [];
            }

            // Handle specifications
            $specifications = [];
            if ($request->specification_titles && $request->specification_values) {
                foreach ($request->specification_titles as $index => $title) {
                    if (!empty($title) && !empty($request->specification_values[$index])) {
                        $specifications[] = [
                            'title' => $title,
                            'value' => $request->specification_values[$index]
                        ];
                    }
                }
            }
            $productData['specifications'] = $specifications;

            // Handle FAQs (replace existing set)
            $faqs = [];
            if ($request->faq_questions && $request->faq_answers) {
                foreach ($request->faq_questions as $index => $question) {
                    $answer = $request->faq_answers[$index] ?? null;
                    if (!empty($question) && !empty($answer)) {
                        $faqs[] = [
                            'question' => $question,
                            'answer' => $answer,
                        ];
                    }
                }
            }
            $productData['faqs'] = $faqs;

            // Handle images - append new images to existing ones instead of replacing
            if ($request->hasFile('images')) {
                $existingImages = $product->images ?? [];
                $newImages = [];
                foreach ($request->file('images') as $image) {
                    $newImages[] = $image->store('products/images', 'public');
                }
                $productData['images'] = array_merge($existingImages, $newImages);
            }

            // Handle product documents
            if ($request->hasFile('datasheet_document')) {
                if ($product->datasheet_document) {
                    Storage::disk('public')->delete($product->datasheet_document);
                }
                $productData['datasheet_document'] = $request->file('datasheet_document')->store('products/datasheets', 'public');
            }
            if ($request->hasFile('connection_diagram_document')) {
                if ($product->connection_diagram_document) {
                    Storage::disk('public')->delete($product->connection_diagram_document);
                }
                $productData['connection_diagram_document'] = $request->file('connection_diagram_document')->store('products/connection-diagrams', 'public');
            }
            if ($request->hasFile('user_manual_document')) {
                if ($product->user_manual_document) {
                    Storage::disk('public')->delete($product->user_manual_document);
                }
                $productData['user_manual_document'] = $request->file('user_manual_document')->store('products/user-manuals', 'public');
            }
            if ($request->hasFile('catalogue_document')) {
                if ($product->catalogue_document) {
                    Storage::disk('public')->delete($product->catalogue_document);
                }
                $productData['catalogue_document'] = $request->file('catalogue_document')->store('products/catalogues', 'public');
            }

            // Handle remove documents
            if ($request->has('remove_datasheet_document')) {
                if ($product->datasheet_document) {
                    Storage::disk('public')->delete($product->datasheet_document);
                }
                $productData['datasheet_document'] = null;
            }
            if ($request->has('remove_connection_diagram_document')) {
                if ($product->connection_diagram_document) {
                    Storage::disk('public')->delete($product->connection_diagram_document);
                }
                $productData['connection_diagram_document'] = null;
            }
            if ($request->has('remove_user_manual_document')) {
                if ($product->user_manual_document) {
                    Storage::disk('public')->delete($product->user_manual_document);
                }
                $productData['user_manual_document'] = null;
            }
            if ($request->has('remove_catalogue_document')) {
                if ($product->catalogue_document) {
                    Storage::disk('public')->delete($product->catalogue_document);
                }
                $productData['catalogue_document'] = null;
            }

            // Handle A+ content via uploaded HTML file (overrides textarea if present)
            if ($request->hasFile('a_plus_content_file')) {
                $productData['a_plus_content'] = file_get_contents($request->file('a_plus_content_file')->getRealPath());
            }

            $product->update($productData);

            return redirect()->route('admin.products.index')
                            ->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Product update failed: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        // Delete product images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete catalogue document
        if ($product->datasheet_document) {
            Storage::disk('public')->delete($product->datasheet_document);
        }
        if ($product->connection_diagram_document) {
            Storage::disk('public')->delete($product->connection_diagram_document);
        }
        if ($product->user_manual_document) {
            Storage::disk('public')->delete($product->user_manual_document);
        }
        if ($product->catalogue_document) {
            Storage::disk('public')->delete($product->catalogue_document);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                        ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['status' => !$product->status]);
        
        return response()->json([
            'success' => true,
            'status' => $product->status,
            'message' => 'Product status updated successfully.'
        ]);
    }

    public function addFeature(Request $request, Product $product)
    {
        $request->validate([
            'feature' => 'required|string|max:255'
        ]);

        $product->addFeature($request->feature);
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Feature added successfully.'
        ]);
    }

    public function removeFeature(Product $product, $index)
    {
        $product->removeFeature($index);
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Feature removed successfully.'
        ]);
    }

    public function addSpecification(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'required|string|max:500'
        ]);

        $product->addSpecification($request->title, $request->value);
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Specification added successfully.'
        ]);
    }

    public function removeSpecification(Product $product, $index)
    {
        $product->removeSpecification($index);
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Specification removed successfully.'
        ]);
    }

    /**
     * Delete individual product image
     */
    public function deleteImage(Request $request, Product $product)
    {
        $request->validate([
            'image_index' => 'required|integer|min:0'
        ]);

        $imageIndex = $request->input('image_index');
        $images = $product->images ?? [];

        if (!isset($images[$imageIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.'
            ], 404);
        }

        // Delete the image file
        $imagePath = $images[$imageIndex];
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        // Remove the image from array
        unset($images[$imageIndex]);
        $images = array_values($images); // Re-index array

        // Update product
        $product->update(['images' => $images]);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
            'remaining_images' => count($images)
        ]);
    }

    public function toggleFeatured(Product $product)
    {
        $product->featured = !$product->featured;
        $product->save();

        return back()->with('success', 'Product featured status updated successfully.');
    }
}


