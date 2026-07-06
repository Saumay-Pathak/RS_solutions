<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeaderFooter;
use App\Models\SiteSetting;
use App\Models\Solution;
use App\Models\Software;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HeaderFooterController extends Controller
{
    /**
     * Get complete header data including navigation menu
     */
    public function header(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            // System statuses sourced from SiteSetting
            $maintenanceMode = (bool) SiteSetting::getValue('maintenance_mode', false);
            $customActivityTracker = (bool) SiteSetting::getValue('custom_activity_tracker', false);
            
            // Get navigation menu items with dynamic data
            $headerData = [
                'branding' => [
                    'site_title' => $settings->site_title,
                    'site_tagline' => $settings->site_tagline,
                    'logo_url' => $settings->logo_url,
                    'favicon_url' => $settings->favicon_url,
                ],
                'navigation' => $this->getNavigationMenu(),
                'settings' => [
                    'show_search_in_header' => $settings->show_search_in_header,
                    'show_language_switcher' => $settings->show_language_switcher,
                    'show_dark_mode_toggle' => $settings->show_dark_mode_toggle,
                    'header_style' => $settings->header_style ?? 'default',
                ],
                'apps' => [
                    'smart_app_link' => $settings->smart_app_link,
                    'attendance_app_link' => $settings->attendance_app_link,
                ],
                'status' => [
                    'maintenance_mode' => $maintenanceMode,
                    'custom_activity_tracker' => $customActivityTracker,
                ],
                'scripts' => [
                    'header_scripts' => $settings->header_scripts,
                    'google_analytics_id' => $settings->google_analytics_id,
                    'google_tag_manager_id' => $settings->google_tag_manager_id,
                ],
                'custom_css' => $settings->custom_css,
            ];

            return response()->json([
                'success' => true,
                'data' => $headerData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching header data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get complete footer data
     */
    public function footer(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            
            $footerData = [
                'branding' => [
                    'footer_logo_url' => $settings->footer_logo_url,
                    'footer_description' => $settings->footer_description,
                    'footer_copyright' => $settings->footer_copyright,
                ],
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
                    'github' => $settings->social_github,
                ],
                'apps' => [
                    'smart_app_link' => $settings->smart_app_link,
                    'attendance_app_link' => $settings->attendance_app_link,
                ],
                'quick_links' => $this->getFooterQuickLinks(),
                'settings' => [
                    'footer_style' => $settings->footer_style ?? 'default',
                ],
                'scripts' => [
                    'footer_scripts' => $settings->footer_scripts,
                    'facebook_pixel_id' => $settings->facebook_pixel_id,
                ],
                'custom_js' => $settings->custom_js,
            ];

            return response()->json([
                'success' => true,
                'data' => $footerData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching footer data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get SEO meta tags and structured data
     */
    public function seo(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            
            // Get current page URL for canonical and OG URL
            $currentUrl = $request->get('url', url('/'));
            $pageTitle = $request->get('page_title', $settings->site_title);
            $pageDescription = $request->get('page_description', $settings->meta_description);
            
            $seoData = [
                'meta' => [
                    'title' => $pageTitle,
                    'description' => $pageDescription,
                    'keywords' => $settings->meta_keywords,
                    'robots' => $settings->robots_meta ?? 'index, follow',
                    'canonical' => $settings->canonical_url ?: $currentUrl,
                ],
                'open_graph' => [
                    'og:title' => $settings->og_title ?: $pageTitle,
                    'og:description' => $settings->og_description ?: $pageDescription,
                    'og:type' => $settings->og_type ?? 'website',
                    'og:url' => $settings->og_url ?: $currentUrl,
                    'og:image' => $settings->og_image_url,
                    'og:site_name' => $settings->site_title,
                ],
                'twitter' => [
                    'twitter:card' => $settings->twitter_card ?? 'summary_large_image',
                    'twitter:site' => $settings->twitter_site,
                    'twitter:creator' => $settings->twitter_creator,
                    'twitter:title' => $settings->og_title ?: $pageTitle,
                    'twitter:description' => $settings->og_description ?: $pageDescription,
                    'twitter:image' => $settings->og_image_url,
                ],
                'icons' => [
                    'favicon' => $settings->favicon_url,
                    'apple_touch_icon' => $settings->apple_touch_icon_url,
                ],
                'schema' => $this->getSchemaMarkup($settings, $currentUrl),
                'analytics' => [
                    'google_analytics' => $settings->google_analytics_id,
                    'google_tag_manager' => $settings->google_tag_manager_id,
                    'google_search_console' => $settings->google_search_console,
                    'facebook_pixel' => $settings->facebook_pixel_id,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $seoData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching SEO data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get complete header and footer data in one request
     */
    public function all(Request $request)
    {
        try {
            $headerResponse = $this->header($request);
            $footerResponse = $this->footer($request);
            $seoResponse = $this->seo($request);

            if ($headerResponse->status() !== 200 || $footerResponse->status() !== 200 || $seoResponse->status() !== 200) {
                throw new \Exception('Error fetching header/footer data');
            }

            // Maintenance mode status
            $maintenanceSetting = SiteSetting::getValue('maintenance_mode', false);
            $maintenanceEnabled = is_array($maintenanceSetting)
                ? (bool)($maintenanceSetting['value'] ?? $maintenanceSetting['enabled'] ?? false)
                : (bool)$maintenanceSetting;

            $frameworkMaintenance = app()->isDownForMaintenance();

            return response()->json([
                'success' => true,
                'data' => [
                    'header' => $headerResponse->getData()->data,
                    'footer' => $footerResponse->getData()->data,
                    'seo' => $seoResponse->getData()->data,
                    'maintenance' => [
                        'effective' => ($frameworkMaintenance || $maintenanceEnabled),
                        'framework' => $frameworkMaintenance,
                        'site_setting' => $maintenanceEnabled,
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching header/footer data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get site counters (labels and values)
     */
    public function counters(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            $counters = $settings->counters ?? [];

            // Normalize to an array list for frontend consumption
            $list = [
                [
                    'key' => 'clients',
                    'label' => data_get($counters, 'clients.label', 'Current Clients'),
                    'value' => data_get($counters, 'clients.value', '10+'),
                ],
                [
                    'key' => 'experience',
                    'label' => data_get($counters, 'experience.label', 'Years Of Experience'),
                    'value' => data_get($counters, 'experience.value', '35+'),
                ],
                [
                    'key' => 'awards',
                    'label' => data_get($counters, 'awards.label', 'Awards Winning'),
                    'value' => data_get($counters, 'awards.value', '10+'),
                ],
                [
                    'key' => 'solutions',
                    'label' => data_get($counters, 'solutions.label', 'Our Solutions'),
                    'value' => data_get($counters, 'solutions.value', '0+'),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $list,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching counters',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get predefined home sections with enabled flags and order
     */
    public function sections(Request $request)
    {
        try {
            $defaults = [
                ['key' => 'hero', 'component' => 'HeroSection', 'title' => 'Hero Section', 'enabled' => true, 'order' => 1],
                ['key' => 'spotlight', 'component' => 'SpotlightSection', 'title' => 'Spotlight Section', 'enabled' => true, 'order' => 2],
                ['key' => 'services', 'component' => 'ServicesSections', 'title' => 'Services Sections', 'enabled' => true, 'order' => 3],
                ['key' => 'features', 'component' => 'FeaturesSection', 'title' => 'Features Section', 'enabled' => true, 'order' => 4],
                ['key' => 'stats', 'component' => 'StatsCounter', 'title' => 'Stats Counter', 'enabled' => true, 'order' => 5],
                ['key' => 'solutions', 'component' => 'SolutionsSection', 'title' => 'Solutions Section', 'enabled' => true, 'order' => 6],
                ['key' => 'testimonials', 'component' => 'TestimonialCarousel', 'title' => 'Testimonial Carousel', 'enabled' => true, 'order' => 7],
                ['key' => 'blog', 'component' => 'BlogSection', 'title' => 'Blog Section', 'enabled' => true, 'order' => 8],
                ['key' => 'contact', 'component' => 'ContactSection', 'title' => 'Contact Section', 'enabled' => true, 'order' => 9],
            ];

            $sections = SiteSetting::getValue('home_sections', $defaults);

            // Normalize and sort
            $list = collect($sections)
                ->map(function ($item, $index) {
                    return [
                        'key' => $item['key'] ?? (is_string($item) ? $item : 'section_' . $index),
                        'component' => $item['component'] ?? $item['title'] ?? 'Section',
                        'title' => $item['title'] ?? ucfirst(str_replace('_', ' ', $item['key'] ?? 'Section')),
                        'enabled' => (bool)($item['enabled'] ?? true),
                        'order' => (int)($item['order'] ?? ($index + 1)),
                    ];
                })
                ->sortBy('order')
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $list,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching home sections',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get navigation menu items
     */
    private function getNavigationMenu()
    {
        return Cache::remember('navigation_menu', 1800, function () {
            return [
                [
                    'title' => 'Solutions',
                    'url' => '/solutions',
                    'type' => 'dropdown',
                    'children' => $this->getSolutionsMenu(),
                ],
                [
                    'title' => 'Products',
                    'url' => '/products',
                    'type' => 'dropdown',
                    'children' => $this->getProductsMenu(),
                ],
                [
                    'title' => 'Software',
                    'url' => '/software',
                    'type' => 'dropdown',
                    'children' => $this->getSoftwareMenu(),
                ],
                [
                    'title' => 'About Us',
                    'url' => '/about-us',
                    'type' => 'single',
                ],
                [
                    'title' => 'Blog',
                    'url' => '/blog',
                    'type' => 'single',
                ],
                [
                    'title' => 'Support',
                    'url' => '/support',
                    'type' => 'single',
                ],
            ];
        });
    }

    /**
     * Get solutions menu items
     */
    private function getSolutionsMenu()
    {
        return Solution::select('title', 'slug')
                      ->active()
                      ->ordered()
                      ->get()
                      ->map(function ($solution) {
                          return [
                              'title' => $solution->title,
                              'url' => '/solutions/' . $solution->slug,
                              'slug' => $solution->slug,
                          ];
                      })
                      ->toArray();
    }

    /**
     * Get products menu items (categories with subcategories)
     */
    private function getProductsMenu()
    {
        // Load parent categories with children using supported MongoDB operations
        $parents = Category::with(['children' => function ($query) {
                            $query->where('status', true)
                                  ->select('_id', 'name', 'slug', 'parent_id', 'sort_order')
                                  ->orderBy('sort_order', 'asc')
                                  ->orderBy('name', 'asc');
                        }])
                        ->where('status', true)
                        ->whereNull('parent_id')
                        ->select('_id', 'name', 'slug', 'sort_order')
                        ->orderBy('sort_order', 'asc')
                        ->orderBy('name', 'asc')
                        ->get();

        // Filter out categories without products (and keep parents if any child has products)
        $filtered = $parents->filter(function ($category) {
            // Direct products under this category
            if ($category->products()->exists()) {
                return true;
            }
            // Any child with products
            foreach ($category->children as $child) {
                if ($child->products()->exists()) {
                    return true;
                }
            }
            return false;
        });

        // Map to menu format including category ids and products under each category
        return $filtered->map(function ($category) {
                    // Base category entry
                    $result = [
                        'id' => $category->_id,
                        'title' => $category->name,
                        'url' => '/products/category/' . $category->slug,
                        'slug' => $category->slug,
                    ];

                    // Fetch products directly under this parent category
                    $categoryProducts = $category->products()
                        ->where('status', true)
                        ->select('_id', 'title', 'slug', 'category_id')
                        ->orderBy('sort_order', 'asc')
                        ->orderBy('title', 'asc')
                        ->get()
                        ->map(function ($product) {
                            return [
                                'id' => $product->_id,
                                'title' => $product->title,
                                'url' => '/products/' . $product->slug,
                                'slug' => $product->slug,
                            ];
                        })
                        ->toArray();

                    // Include only child categories that have products and attach their products
                    $childrenWithProducts = $category->children
                        ->filter(function ($child) {
                            return $child->products()->exists();
                        })
                        ->map(function ($child) {
                            // Child category entry
                            $childEntry = [
                                'id' => $child->_id,
                                'title' => $child->name,
                                'url' => '/products/category/' . $child->slug,
                                'slug' => $child->slug,
                            ];

                            // Products under this child category
                            $childProducts = $child->products()
                                ->where('status', true)
                                ->select('_id', 'title', 'slug', 'category_id')
                                ->orderBy('sort_order', 'asc')
                                ->orderBy('title', 'asc')
                                ->get()
                                ->map(function ($product) {
                                    return [
                                        'id' => $product->_id,
                                        'title' => $product->title,
                                        'url' => '/products/' . $product->slug,
                                        'slug' => $product->slug,
                                    ];
                                })
                                ->toArray();

                            // Attach products list to the child category object
                            $childEntry['products'] = $childProducts;
                            return $childEntry;
                        })
                        ->values()
                        ->toArray();

                    // Attach children and products to the parent category object
                    $result['children'] = $childrenWithProducts;
                    $result['products'] = $categoryProducts;

                    return $result;
                })
                ->values()
                ->toArray();
    }

    /**
     * Get software menu items
     */
    private function getSoftwareMenu()
    {
        return Software::select('title', 'slug')
                      ->active()
                      ->orderBy('title', 'asc')
                      ->get()
                      ->map(function ($software) {
                          return [
                              'title' => $software->title,
                              'url' => '/software/' . $software->slug,
                              'slug' => $software->slug,
                          ];
                      })
                      ->toArray();
    }

    /**
     * Get footer quick links
     */
    private function getFooterQuickLinks()
    {
        return [
            'company' => [
                ['title' => 'About Us', 'url' => '/about-us'],
                ['title' => 'Contact', 'url' => '/contact'],
                ['title' => 'Careers', 'url' => '/careers'],
                ['title' => 'News', 'url' => '/blog'],
            ],
            'products' => [
                ['title' => 'Solutions', 'url' => '/solutions'],
                ['title' => 'Products', 'url' => '/products'],
                ['title' => 'Software', 'url' => '/software'],
                ['title' => 'Downloads', 'url' => '/downloads'],
            ],
            'support' => [
                ['title' => 'Help Center', 'url' => '/support'],
                ['title' => 'Documentation', 'url' => '/docs'],
                ['title' => 'API Reference', 'url' => '/api-docs'],
                ['title' => 'Contact Support', 'url' => '/support/contact'],
            ],
            'legal' => [
                ['title' => 'Privacy Policy', 'url' => '/privacy-policy'],
                ['title' => 'Terms of Service', 'url' => '/terms-of-service'],
                ['title' => 'Cookie Policy', 'url' => '/cookie-policy'],
                ['title' => 'Disclaimer', 'url' => '/disclaimer'],
            ],
        ];
    }

    /**
     * Generate schema markup
     */
    private function getSchemaMarkup($settings, $currentUrl)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $settings->site_title,
            'description' => $settings->meta_description,
            'url' => $currentUrl,
            'logo' => $settings->logo_url,
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => $settings->footer_phone,
                'email' => $settings->footer_email,
                'contactType' => 'customer service'
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings->footer_address,
            ],
            'sameAs' => array_filter([
                $settings->social_facebook,
                $settings->social_twitter,
                $settings->social_linkedin,
                $settings->social_instagram,
                $settings->social_youtube,
            ])
        ];

        // Add custom schema if provided
        if ($settings->schema_markup) {
            try {
                $customSchema = json_decode($settings->schema_markup, true);
                if ($customSchema) {
                    $schema = array_merge($schema, $customSchema);
                }
            } catch (\Exception $e) {
                // Ignore invalid JSON
            }
        }

        return $schema;
    }

    /**
     * Get Privacy Policy content
     */
    public function privacyPolicy(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            $data = [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => $settings->privacy_policy,
                'updated_at' => optional($settings->updated_at)->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Privacy Policy',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Terms of Service content
     */
    public function termsOfService(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            $data = [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => $settings->terms_of_service,
                'updated_at' => optional($settings->updated_at)->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Terms of Service',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Cookie Policy content
     */
    public function cookiePolicy(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            $data = [
                'title' => 'Cookie Policy',
                'slug' => 'cookie-policy',
                'content' => $settings->cookie_policy,
                'updated_at' => optional($settings->updated_at)->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Cookie Policy',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get Disclaimer content
     */
    public function disclaimer(Request $request)
    {
        try {
            $settings = HeaderFooter::getCached();
            $data = [
                'title' => 'Disclaimer',
                'slug' => 'disclaimer',
                'content' => $settings->disclaimer,
                'updated_at' => optional($settings->updated_at)->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Disclaimer',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}