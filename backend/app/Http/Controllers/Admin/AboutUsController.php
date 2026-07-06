<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutUsController extends Controller
{
    /**
     * Display the About Us edit form
     */
    public function index()
    {
        $aboutUs = AboutUs::getInstance();
        return view('admin.about-us.index', compact('aboutUs'));
    }

    /**
     * Update the About Us content
     */
    public function update(Request $request)
    {
        $aboutUs = AboutUs::getInstance();

        $validated = $request->validate([
            // Who We Are Section
            'who_we_are_title' => 'required|string|max:255',
            'who_we_are_subtitle' => 'nullable|string|max:255',
            'who_we_are_content' => 'required|string',
            'who_we_are_image' => 'nullable|image|max:20480', // 20MB max
            'who_we_are_video_url' => 'nullable|url',
            'who_we_are_video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:20480', // 20MB max
            'who_we_are_features' => 'nullable|array',
            'who_we_are_features.*.icon' => 'nullable|string|max:100',
            'who_we_are_features.*.title' => 'nullable|string|max:255',
            'who_we_are_features.*.description' => 'nullable|string|max:500',
            
            // Mission & Vision Section
            'mission_vision_title' => 'required|string|max:255',
            'mission_title' => 'required|string|max:255',
            'mission_content' => 'required|string',
            'mission_image' => 'nullable|image|max:20480',
            'vision_title' => 'required|string|max:255',
            'vision_content' => 'required|string',
            'vision_image' => 'nullable|image|max:20480',
            
            // Custom Sections
            'custom_sections' => 'nullable|array',
            'custom_sections.*.title' => 'nullable|string|max:255',
            'custom_sections.*.content' => 'nullable|string',
            'custom_sections.*.image' => 'nullable|string', // Will handle file uploads separately
            
            // SEO Fields
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:20480',
            'schema_markup' => 'nullable|string',
            
            // Settings
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        // Handle Who We Are image upload
        if ($request->hasFile('who_we_are_image')) {
            // Delete old image
            if ($aboutUs->who_we_are_image && Storage::disk('public')->exists($aboutUs->who_we_are_image)) {
                Storage::disk('public')->delete($aboutUs->who_we_are_image);
            }
            $validated['who_we_are_image'] = $request->file('who_we_are_image')->store('about-us/images', 'public');
        }

        // Handle Who We Are video upload
        if ($request->hasFile('who_we_are_video_file')) {
            // Delete old video
            if ($aboutUs->who_we_are_video_file && Storage::disk('public')->exists($aboutUs->who_we_are_video_file)) {
                Storage::disk('public')->delete($aboutUs->who_we_are_video_file);
            }
            $validated['who_we_are_video_file'] = $request->file('who_we_are_video_file')->store('about-us/videos', 'public');
            // Clear video URL if file is uploaded
            $validated['who_we_are_video_url'] = null;
        }

        // Handle Mission image upload
        if ($request->hasFile('mission_image')) {
            if ($aboutUs->mission_image && Storage::disk('public')->exists($aboutUs->mission_image)) {
                Storage::disk('public')->delete($aboutUs->mission_image);
            }
            $validated['mission_image'] = $request->file('mission_image')->store('about-us/mission', 'public');
        }

        // Handle Vision image upload
        if ($request->hasFile('vision_image')) {
            if ($aboutUs->vision_image && Storage::disk('public')->exists($aboutUs->vision_image)) {
                Storage::disk('public')->delete($aboutUs->vision_image);
            }
            $validated['vision_image'] = $request->file('vision_image')->store('about-us/vision', 'public');
        }

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            if ($aboutUs->og_image && Storage::disk('public')->exists($aboutUs->og_image)) {
                Storage::disk('public')->delete($aboutUs->og_image);
            }
            $validated['og_image'] = $request->file('og_image')->store('about-us/seo', 'public');
        }

        // Handle custom section images
        if ($request->hasFile('custom_section_images')) {
            $customSections = $validated['custom_sections'] ?? [];
            $customSectionImages = $request->file('custom_section_images');
            
            foreach ($customSectionImages as $index => $file) {
                if ($file && isset($customSections[$index])) {
                    $imagePath = $file->store('about-us/custom-sections', 'public');
                    $customSections[$index]['image'] = $imagePath;
                }
            }
            
            $validated['custom_sections'] = $customSections;
        }

        // Clean up features array
        if (isset($validated['who_we_are_features'])) {
            $validated['who_we_are_features'] = array_filter($validated['who_we_are_features'], function ($feature) {
                return !empty($feature['title']) || !empty($feature['description']);
            });
        }

        // Clean up custom sections array
        if (isset($validated['custom_sections'])) {
            $validated['custom_sections'] = array_filter($validated['custom_sections'], function ($section) {
                return !empty($section['title']) || !empty($section['content']);
            });
        }

        // Set defaults
        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['updated_by'] = auth()->user()->name ?? 'Admin';

        $aboutUs->update($validated);

        return redirect()->route('admin.about-us.index')
                        ->with('success', 'About Us page updated successfully!');
    }

    /**
     * Add a new feature to Who We Are section
     */
    public function addFeature(Request $request)
    {
        $request->validate([
            'icon' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500'
        ]);

        $aboutUs = AboutUs::getInstance();
        $features = $aboutUs->who_we_are_features ?? [];
        
        $features[] = [
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description
        ];

        $aboutUs->update(['who_we_are_features' => $features]);

        return response()->json([
            'success' => true,
            'message' => 'Feature added successfully!',
            'feature' => end($features)
        ]);
    }

    /**
     * Remove a feature from Who We Are section
     */
    public function removeFeature(Request $request, $index)
    {
        $aboutUs = AboutUs::getInstance();
        $features = $aboutUs->who_we_are_features ?? [];

        if (isset($features[$index])) {
            unset($features[$index]);
            $features = array_values($features); // Re-index array

            $aboutUs->update(['who_we_are_features' => $features]);

            return response()->json([
                'success' => true,
                'message' => 'Feature removed successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Feature not found!'
        ], 404);
    }

    /**
     * Add a new custom section
     */
    public function addCustomSection(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:20480'
        ]);

        $aboutUs = AboutUs::getInstance();
        $customSections = $aboutUs->custom_sections ?? [];
        
        $newSection = [
            'title' => $request->title,
            'content' => $request->content,
            'image' => null
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $newSection['image'] = $request->file('image')->store('about-us/custom-sections', 'public');
        }

        $customSections[] = $newSection;

        $aboutUs->update(['custom_sections' => $customSections]);

        return response()->json([
            'success' => true,
            'message' => 'Custom section added successfully!',
            'section' => $newSection
        ]);
    }

    /**
     * Remove a custom section
     */
    public function removeCustomSection($index)
    {
        $aboutUs = AboutUs::getInstance();
        $customSections = $aboutUs->custom_sections ?? [];

        if (isset($customSections[$index])) {
            // Delete associated image
            if (!empty($customSections[$index]['image']) && Storage::disk('public')->exists($customSections[$index]['image'])) {
                Storage::disk('public')->delete($customSections[$index]['image']);
            }

            unset($customSections[$index]);
            $customSections = array_values($customSections); // Re-index array

            $aboutUs->update(['custom_sections' => $customSections]);

            return response()->json([
                'success' => true,
                'message' => 'Custom section removed successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Custom section not found!'
        ], 404);
    }

    /**
     * Remove uploaded image/video
     */
    public function removeMedia(Request $request)
    {
        $request->validate([
            'type' => 'required|in:who_we_are_image,who_we_are_video_file,mission_image,vision_image,og_image',
        ]);

        $aboutUs = AboutUs::getInstance();
        $type = $request->type;

        if ($aboutUs->$type && Storage::disk('public')->exists($aboutUs->$type)) {
            Storage::disk('public')->delete($aboutUs->$type);
            $aboutUs->update([$type => null]);

            return response()->json([
                'success' => true,
                'message' => 'Media removed successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Media not found!'
        ], 404);
    }

    /**
     * Generate SEO content automatically
     */
    public function generateSeo()
    {
        $aboutUs = AboutUs::getInstance();

        $metaTitle = $aboutUs->who_we_are_title . ' - About Our Company';
        $content = strip_tags($aboutUs->who_we_are_content);
        $metaDescription = Str::limit($content, 155);
        $keywords = 'about us, company, ' . strtolower($aboutUs->who_we_are_title) . ', mission, vision';

        $aboutUs->update([
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords' => $keywords,
            'og_title' => $metaTitle,
            'og_description' => $metaDescription
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SEO content generated successfully!',
            'seo' => [
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'meta_keywords' => $keywords
            ]
        ]);
    }

    /**
     * Preview the About Us page
     */
    public function preview()
    {
        $aboutUs = AboutUs::getInstance();
        return view('admin.about-us.preview', compact('aboutUs'));
    }
}