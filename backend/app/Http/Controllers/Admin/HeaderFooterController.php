<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderFooter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HeaderFooterController extends Controller
{
    /**
     * Display the header/footer settings form
     */
    public function index()
    {
        $settings = HeaderFooter::getCached();
        
        return view('admin.header-footer.index', compact('settings'));
    }

    /**
     * Update header/footer settings
     */
    public function update(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        }

        try {
            $settings = HeaderFooter::first() ?: new HeaderFooter();
            
            // Basic site information
            $settings->site_title = $request->input('site_title');
            $settings->site_tagline = $request->input('site_tagline');
            $settings->meta_description = $request->input('meta_description');
            $settings->meta_keywords = $request->input('meta_keywords');
            
            // Logo uploads
            $settings->logo = $this->handleFileUpload($request, 'logo', $settings->logo);
            $settings->favicon = $this->handleFileUpload($request, 'favicon', $settings->favicon);
            $settings->footer_logo = $this->handleFileUpload($request, 'footer_logo', $settings->footer_logo);
            $settings->apple_touch_icon = $this->handleFileUpload($request, 'apple_touch_icon', $settings->apple_touch_icon);
            
            // Footer information
            $settings->footer_description = $request->input('footer_description');
            $settings->footer_copyright = $request->input('footer_copyright');
            $settings->footer_email = $request->input('footer_email');
            $settings->footer_phone = $request->input('footer_phone');
            $settings->footer_address = $request->input('footer_address');

            // App links
            $settings->smart_app_link = $request->input('smart_app_link');
            $settings->attendance_app_link = $request->input('attendance_app_link');
            
            // Social media links
            $settings->social_facebook = $request->input('social_facebook');
            $settings->social_twitter = $request->input('social_twitter');
            $settings->social_linkedin = $request->input('social_linkedin');
            $settings->social_instagram = $request->input('social_instagram');
            $settings->social_youtube = $request->input('social_youtube');
            $settings->social_github = $request->input('social_github');
            
            // SEO settings
            $settings->robots_meta = $request->input('robots_meta');
            $settings->canonical_url = $request->input('canonical_url');
            
            // Open Graph settings
            $settings->og_title = $request->input('og_title');
            $settings->og_description = $request->input('og_description');
            $settings->og_type = $request->input('og_type');
            $settings->og_url = $request->input('og_url');
            $settings->og_image = $this->handleFileUpload($request, 'og_image', $settings->og_image);
            
            // Twitter settings
            $settings->twitter_card = $request->input('twitter_card');
            $settings->twitter_site = $request->input('twitter_site');
            $settings->twitter_creator = $request->input('twitter_creator');
            
            // Analytics & Tracking
            $settings->google_analytics_id = $request->input('google_analytics_id');
            $settings->google_tag_manager_id = $request->input('google_tag_manager_id');
            $settings->google_search_console = $request->input('google_search_console');
            $settings->facebook_pixel_id = $request->input('facebook_pixel_id');
            
            // Custom scripts and styles
            $settings->header_scripts = $request->input('header_scripts');
            $settings->footer_scripts = $request->input('footer_scripts');
            $settings->custom_css = $request->input('custom_css');
            $settings->custom_js = $request->input('custom_js');

            // Legal/Policy pages
            $settings->privacy_policy = $request->input('privacy_policy');
            $settings->terms_of_service = $request->input('terms_of_service');
            $settings->cookie_policy = $request->input('cookie_policy');
            $settings->disclaimer = $request->input('disclaimer');

            // Advanced settings
            $settings->schema_markup = $request->input('schema_markup');
            $settings->header_style = $request->input('header_style', 'default');
            $settings->footer_style = $request->input('footer_style', 'default');
            
            // Display options
            $settings->show_search_in_header = $request->boolean('show_search_in_header');
            $settings->show_language_switcher = $request->boolean('show_language_switcher');
            $settings->show_dark_mode_toggle = $request->boolean('show_dark_mode_toggle');

            // Counters (labels and values)
            $counters = $request->input('counters', []);
            // Sanitize counters to expected structure
            $settings->counters = [
                'clients' => [
                    'label' => data_get($counters, 'clients.label') ?: ($settings->counters['clients']['label'] ?? 'Current Clients'),
                    'value' => data_get($counters, 'clients.value') ?: ($settings->counters['clients']['value'] ?? '10+'),
                ],
                'experience' => [
                    'label' => data_get($counters, 'experience.label') ?: ($settings->counters['experience']['label'] ?? 'Years Of Experience'),
                    'value' => data_get($counters, 'experience.value') ?: ($settings->counters['experience']['value'] ?? '35+'),
                ],
                'awards' => [
                    'label' => data_get($counters, 'awards.label') ?: ($settings->counters['awards']['label'] ?? 'Awards Winning'),
                    'value' => data_get($counters, 'awards.value') ?: ($settings->counters['awards']['value'] ?? '10+'),
                ],
                'solutions' => [
                    'label' => data_get($counters, 'solutions.label') ?: ($settings->counters['solutions']['label'] ?? 'Our Solutions'),
                    'value' => data_get($counters, 'solutions.value') ?: ($settings->counters['solutions']['value'] ?? '0+'),
                ],
            ];
            
            $settings->save();
            
            // Clear cache
            Cache::forget('header_footer_settings');
            Cache::forget('navigation_menu');
            
            return back()->with('success', 'Header/Footer settings updated successfully!');
            
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Get validation rules
     */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'site_title' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            
            // File validations
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'favicon' => 'nullable|image|mimes:ico,png|max:20480',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'apple_touch_icon' => 'nullable|image|mimes:png|max:20480',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            
            // Contact information
            'footer_email' => 'nullable|email|max:255',
            'footer_phone' => 'nullable|string|max:50',
            'footer_address' => 'nullable|string|max:500',
            'footer_description' => 'nullable|string|max:1000',
            'footer_copyright' => 'nullable|string|max:255',

            // App links
            'smart_app_link' => 'nullable|url|max:255',
            'attendance_app_link' => 'nullable|url|max:255',
            
            // Social media URLs
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_github' => 'nullable|url|max:255',
            
            // SEO fields
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_type' => 'nullable|string|max:50',
            'og_url' => 'nullable|url|max:255',
            
            // Twitter fields
            'twitter_card' => 'nullable|string|max:50',
            'twitter_site' => 'nullable|string|max:50',
            'twitter_creator' => 'nullable|string|max:50',
            
            // Analytics IDs
            'google_analytics_id' => 'nullable|string|max:50',
            'google_tag_manager_id' => 'nullable|string|max:50',
            'google_search_console' => 'nullable|string|max:100',
            'facebook_pixel_id' => 'nullable|string|max:50',

            // Custom code
            'header_scripts' => 'nullable|string',
            'footer_scripts' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
            'schema_markup' => 'nullable|string',

            // Legal/Policy pages
            'privacy_policy' => 'nullable|string',
            'terms_of_service' => 'nullable|string',
            'cookie_policy' => 'nullable|string',
            'disclaimer' => 'nullable|string',

            // Style options
            'header_style' => 'nullable|string|max:50',
            'footer_style' => 'nullable|string|max:50',
            
            // Boolean options
            'show_search_in_header' => 'boolean',
            'show_language_switcher' => 'boolean',
            'show_dark_mode_toggle' => 'boolean',

            // Counters
            'counters' => 'nullable|array',
            'counters.clients.label' => 'nullable|string|max:100',
            'counters.clients.value' => 'nullable|string|max:20',
            'counters.experience.label' => 'nullable|string|max:100',
            'counters.experience.value' => 'nullable|string|max:20',
            'counters.awards.label' => 'nullable|string|max:100',
            'counters.awards.value' => 'nullable|string|max:20',
            'counters.solutions.label' => 'nullable|string|max:100',
            'counters.solutions.value' => 'nullable|string|max:20',
        ]);
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload(Request $request, string $fieldName, ?string $existingPath = null): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return $existingPath;
        }

        $file = $request->file($fieldName);
        
        if (!$file->isValid()) {
            return $existingPath;
        }

        // Delete existing file
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        // Store new file
        $path = $file->store('header-footer', 'public');
        
        return $path;
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field' => 'required|string|in:logo,favicon,footer_logo,apple_touch_icon,og_image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid field specified'
            ], 400);
        }

        try {
            $settings = HeaderFooter::first();
            
            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Settings not found'
                ], 404);
            }

            $field = $request->input('field');
            $filePath = $settings->{$field};

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Clear the field in database
            $settings->{$field} = null;
            $settings->save();

            // Clear cache
            Cache::forget('header_footer_settings');

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview settings (for testing purposes)
     */
    public function preview()
    {
        $settings = HeaderFooter::getCached();
        
        return response()->json([
            'success' => true,
            'data' => [
                'header' => [
                    'site_title' => $settings->site_title,
                    'logo_url' => $settings->logo_url,
                    'navigation' => [
                        ['title' => 'Solutions', 'url' => '/solutions'],
                        ['title' => 'Products', 'url' => '/products'],
                        ['title' => 'Software', 'url' => '/software'],
                        ['title' => 'About Us', 'url' => '/about-us'],
                        ['title' => 'Blog', 'url' => '/blog'],
                        ['title' => 'Contact', 'url' => '/contact'],
                    ]
                ],
                'footer' => [
                    'footer_logo_url' => $settings->footer_logo_url,
                    'footer_description' => $settings->footer_description,
                    'footer_copyright' => $settings->footer_copyright,
                    'contact' => [
                        'email' => $settings->footer_email,
                        'phone' => $settings->footer_phone,
                        'address' => $settings->footer_address,
                    ],
                    'social_media' => [
                        'facebook' => $settings->social_facebook,
                        'twitter' => $settings->social_twitter,
                        'linkedin' => $settings->social_linkedin,
                        'instagram' => $settings->social_instagram,
                        'youtube' => $settings->social_youtube,
                    ]
                ],
                'seo' => [
                    'title' => $settings->site_title,
                    'description' => $settings->meta_description,
                    'keywords' => $settings->meta_keywords,
                    'og_image' => $settings->og_image_url,
                ]
            ]
        ]);
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            Cache::forget('header_footer_settings');
            Cache::forget('navigation_menu');
            
            return back()->with('success', 'Cache cleared successfully!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error clearing cache: ' . $e->getMessage());
        }
    }
}