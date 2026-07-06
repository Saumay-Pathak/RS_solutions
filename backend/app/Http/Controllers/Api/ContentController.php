<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Solution;
use App\Models\Software;
use App\Models\Product;
use App\Models\Category;
use App\Models\Testimonial;
use App\Models\Page;
use App\Models\Popup;
use App\Models\HeaderFooter;
use App\Models\ContactInfo;
use App\Models\AboutUs;
use App\Models\HeroSlide;
use App\Models\JobOpening;
use App\Models\Service;
use App\Models\Faq;
use App\Models\IntegrationModule;
use App\Models\Client;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    /**
     * Get clients with filtering
     */
    public function clients(Request $request)
    {
        try {
            $query = Client::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Featured filter
            if ($request->filled('featured')) {
                $query->where('featured', $request->boolean('featured'));
            }

            // Search by name
            $this->applySearchFilter($query, $request, ['name']);

            // Sorting (no pagination - fetch all at once)
            $allowedSortFields = ['sort_order', 'created_at', 'name', 'featured'];
            $sortField = $request->get('sort_by', $allowedSortFields[0]);
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = $allowedSortFields[0];
            }
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            $clients = $query->get();

            return response()->json([
                'success' => true,
                'data' => $clients,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching clients', $e);
        }
    }

    /**
     * Get certifications with filtering
     */
    public function certifications(Request $request)
    {
        try {
            $query = Certification::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Search by name
            $this->applySearchFilter($query, $request, ['name']);

            // Sorting and pagination
            return $this->paginateAndRespond($query, $request, ['sort_order', 'created_at', 'name']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching certifications', $e);
        }
    }

    public function blogs(Request $request)
    {
        try {
            $query = Blog::query();

            // Status filter (default to published)
            if ($request->has('status')) {
                if ($request->status === 'published') {
                    $query->published();
                } elseif ($request->status === 'draft') {
                    $query->draft();
                } else {
                    $query->where('status', $request->boolean('status'));
                }
            } else {
                $query->published();
            }

            // Apply common filters
            $this->applyCommonFilters($query, $request, ['category', 'author_id']);
            $this->applyDateFilters($query, $request, 'published_at');
            $this->applySearchFilter($query, $request, ['title', 'excerpt', 'content']);
            
            // Blog-specific filters
            if ($request->filled('tags')) {
                $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
                $query->where(function ($q) use ($tags) {
                    foreach ($tags as $tag) {
                        $q->orWhere('tags', 'like', '%' . trim($tag) . '%');
                    }
                });
            }

            if ($request->filled('reading_time_min')) {
                $query->where('reading_time', '>=', $request->reading_time_min);
            }

            if ($request->filled('reading_time_max')) {
                $query->where('reading_time', '<=', $request->reading_time_max);
            }

            // Include author relationship
            $query->with('author:_id,name,email');

            return $this->paginateAndRespond($query, $request, ['published_at', 'title', 'reading_time', 'created_at']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching blogs', $e);
        }
    }

    /**
     * Get solutions with filtering
     */
    public function solutions(Request $request)
    {
        try {
            $query = Solution::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);
            
            // Featured filter
            if ($request->filled('featured')) {
                $query->where('featured', $request->boolean('featured'));
            }

            // Apply common filters
            $this->applyCommonFilters($query, $request, ['category', 'price_range']);
            $this->applySearchFilter($query, $request, ['title', 'short_description', 'description']);

            // Solution-specific filters
            if ($request->filled('technologies')) {
                $technologies = is_array($request->technologies) ? $request->technologies : explode(',', $request->technologies);
                $query->where(function ($q) use ($technologies) {
                    foreach ($technologies as $tech) {
                        $q->orWhere('technologies', 'like', '%' . trim($tech) . '%');
                    }
                });
            }

            if ($request->filled('delivery_time_max')) {
                $query->where('delivery_time', '<=', $request->delivery_time_max);
            }

            return $this->paginateAndRespond($query, $request, ['sort_order', 'created_at', 'title', 'featured']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching solutions', $e);
        }
    }

    /**
     * Get services with filtering
     */
    public function services(Request $request)
    {
        try {
            $query = Service::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Common filters
            $this->applySearchFilter($query, $request, ['title', 'short_description', 'description']);

            // Hide from homepage filter (optional). If not provided, include all (true/false/null).
            if ($request->filled('hide_from_homepage')) {
                $query->where('hide_from_homepage', $request->boolean('hide_from_homepage'));
            }

            return $this->paginateAndRespond($query, $request, ['sort_order', 'created_at', 'title']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching services', $e);
        }
    }

    /**
     * Get integration modules with filtering
     */
    public function integrationModules(Request $request)
    {
        try {
            $query = IntegrationModule::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Common search across fields
            $this->applySearchFilter($query, $request, ['title', 'description', 'slug']);

            // Optional filter by slug
            if ($request->filled('slug')) {
                $query->where('slug', $request->slug);
            }

            // Optional filter by environment URL presence
            if ($request->filled('env')) {
                if ($request->env === 'production') {
                    $query->whereNotNull('production_base_url')->where('production_base_url', '!=', '');
                } elseif ($request->env === 'staging') {
                    $query->whereNotNull('staging_base_url')->where('staging_base_url', '!=', '');
                }
            }

            // Sorting fields
            return $this->paginateAndRespond($query, $request, ['sort_order', 'created_at', 'title']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching integration modules', $e);
        }
    }

    /**
     * Get FAQs with filtering
     */
    public function faqs(Request $request)
    {
        try {
            $query = Faq::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Common filters and search
            $this->applyCommonFilters($query, $request, ['sort_order']);
            $this->applySearchFilter($query, $request, ['question', 'answer']);

            return $this->paginateAndRespond($query, $request, ['sort_order', 'created_at']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching FAQs', $e);
        }
    }

    /**
     * Get software with filtering
     */
    public function software(Request $request)
    {
        try {
            $query = Software::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Featured filter
            if ($request->filled('featured')) {
                $query->where('featured', $request->boolean('featured'));
            }

            // Free/Paid filter
            if ($request->filled('is_free')) {
                $query->where('is_free', $request->boolean('is_free'));
            }

            // Apply common filters
            $this->applyCommonFilters($query, $request, ['main_category', 'sub_category', 'license', 'developer']);
            $this->applySearchFilter($query, $request, ['title', 'description', 'one_line_description']);
            $this->applyDateFilters($query, $request, 'released_at');

            // Software-specific filters
            if ($request->filled('platforms')) {
                $platforms = is_array($request->platforms) ? $request->platforms : explode(',', $request->platforms);
                $query->where(function ($q) use ($platforms) {
                    foreach ($platforms as $platform) {
                        $q->orWhere('platforms', 'like', '%' . trim($platform) . '%');
                    }
                });
            }

            if ($request->filled('tags')) {
                $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
                $query->where(function ($q) use ($tags) {
                    foreach ($tags as $tag) {
                        $q->orWhere('tags', 'like', '%' . trim($tag) . '%');
                    }
                });
            }

            if ($request->filled('version')) {
                $query->where('version', 'like', '%' . $request->version . '%');
            }

            if ($request->filled('min_downloads')) {
                $query->where('download_count', '>=', $request->min_downloads);
            }

            return $this->paginateAndRespond($query, $request, ['sort_order', 'created_at', 'title', 'download_count', 'featured']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching software', $e);
        }
    }

    /**
     * Increment the download count for a specific software
     */
    public function incrementDownload(Request $request, $slug)
    {
        try {
            // Find the active software by slug or ID
            $software = Software::active()
                ->where(function($query) use ($slug) {
                    $query->where('slug', $slug)->orWhere('_id', $slug);
                })
                ->first();

            if (!$software) {
                return response()->json([
                    'success' => false,
                    'message' => 'Software not found.'
                ], 404);
            }

            // Use the helper method defined in your Software model
            $software->incrementDownloadCount();

            return response()->json([
                'success' => true,
                'message' => 'Download count incremented successfully.',
                'data' => [
                    'title' => $software->title,
                    'new_download_count' => $software->download_count
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error incrementing download count', $e);
        }
    }

    /**
     * Get products with filtering
     */
    public function products(Request $request)
    {
        try {
            $query = Product::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Apply common filters
            $this->applyCommonFilters($query, $request, ['category_id']);
            $this->applySearchFilter($query, $request, ['title', 'description', 'slug']);

            // Specific slug filter
            if ($request->filled('slug')) {
                $query->where('slug', $request->slug);
            }

            // Include category relationship with parent (main category)
            $query->with(['category' => function ($q) {
                $q->select('_id', 'name', 'slug', 'parent_id')
                  ->with('parent:_id,name,slug');
            }]);

            // Sorting (keep behavior consistent with paginateAndRespond)
            $allowedSortFields = ['sort_order', 'created_at', 'title'];
            $sortField = $request->get('sort_by', $allowedSortFields[0]);
            $sortOrder = $request->get('sort_order', 'desc');
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = $allowedSortFields[0];
            }
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $results = $query->paginate($perPage);

            // Map items to include explicit A+ content HTML alias, normalize FAQs,
            // and add features_extended with full SVG content for Tabler icons
            $controller = $this;
            $items = collect($results->items())->map(function ($product) use ($controller) {
                $data = $product->toArray();
                $data['a_plus_content_html'] = $product->a_plus_content ?? '';
                // Ensure FAQs is always an array for clients
                $data['faqs'] = is_array($product->faqs) ? $product->faqs : [];

                // Ensure document URLs are explicitly present
                $data['datasheet_url'] = $product->datasheet_url ?? null;
                $data['connection_diagram_url'] = $product->connection_diagram_url ?? null;
                $data['user_manual_url'] = $product->user_manual_url ?? null;
                $data['catalogue_url'] = $product->catalogue_url ?? null;

                // Normalize features and attach SVG for Tabler icons
                $features = is_array($product->features) ? $product->features : [];
                $normalized = collect($features)->map(function ($item) use ($controller) {
                    if (is_array($item)) {
                        $title = (string)($item['title'] ?? '');
                        $icon = $item['icon'] ?? '';
                    } else {
                        $title = is_string($item) ? $item : '';
                        $icon = '';
                    }

                    $svg = $controller->resolveTablerSvg($icon);
                    return [
                        'title' => $title,
                        'icon' => $icon ?: null,
                        'svg' => $svg ?: null,
                    ];
                })->toArray();

                $data['features_extended'] = $normalized;
                return $data;
            });
            $response = response()->json([
                'success' => true,
                'data' => $items,
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                    'from' => $results->firstItem(),
                    'to' => $results->lastItem(),
                ],
                'links' => [
                    'first' => $results->url(1),
                    'last' => $results->url($results->lastPage()),
                    'prev' => $results->previousPageUrl(),
                    'next' => $results->nextPageUrl(),
                ]
            ]);

            // Ensure SVG strings are readable by disabling hex-tag escaping
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return $response;

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching products', $e);
        }
    }

    /**
     * Get featured products with all details
     */
    public function featuredProducts(Request $request)
    {
        try {
            $query = Product::query();

            // Status filter (active only by default for public API)
            $query->where('status', true);
            
            // Featured filter
            $query->where('featured', true);

            // Include category relationship with parent (main category)
            $query->with(['category' => function ($q) {
                $q->select('_id', 'name', 'slug', 'parent_id')
                  ->with('parent:_id,name,slug');
            }]);

            // Default sorting
            $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');

            // Get all results
            $products = $query->get();

            // Map items (same logic as products method)
            $controller = $this;
            $items = $products->map(function ($product) use ($controller) {
                $data = $product->toArray();
                $data['a_plus_content_html'] = $product->a_plus_content ?? '';
                $data['faqs'] = is_array($product->faqs) ? $product->faqs : [];
                $data['datasheet_url'] = $product->datasheet_url ?? null;
                $data['connection_diagram_url'] = $product->connection_diagram_url ?? null;
                $data['user_manual_url'] = $product->user_manual_url ?? null;
                $data['catalogue_url'] = $product->catalogue_url ?? null;

                $features = is_array($product->features) ? $product->features : [];
                $normalized = collect($features)->map(function ($item) use ($controller) {
                    if (is_array($item)) {
                        $title = (string)($item['title'] ?? '');
                        $icon = $item['icon'] ?? '';
                    } else {
                        $title = is_string($item) ? $item : '';
                        $icon = '';
                    }

                    $svg = $controller->resolveTablerSvg($icon);
                    return [
                        'title' => $title,
                        'icon' => $icon ?: null,
                        'svg' => $svg ?: null,
                    ];
                })->toArray();

                $data['features_extended'] = $normalized;
                return $data;
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'total' => $items->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching featured products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories with filtering and hierarchy
     */
    public function categories(Request $request)
    {
        try {
            $query = Category::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);
            
            // Filter by slug
            if ($request->filled('slug')) {
                $query->where('slug', $request->slug);
            }

            // Filter by id (MongoDB _id)
            if ($request->filled('id')) {
                $query->where('_id', $request->id);
            }

            // Include products for the selected category when requested or when narrowed by slug/id
            if ($request->boolean('with_products') || $request->filled('slug') || $request->filled('id')) {
                $query->with(['products' => function ($q) {
                    $q->where('status', true)
                      ->select('_id', 'title', 'slug', 'description', 'a_plus_content', 'features', 'specifications', 'faqs', 'images', 'datasheet_document', 'connection_diagram_document', 'user_manual_document', 'catalogue_document', 'status', 'sort_order', 'category_id')
                      ->orderBy('sort_order', 'asc')
                      ->orderBy('title', 'asc');
                }]);
            }

            // Parent/child filter
            if ($request->filled('parent_only')) {
                $query->whereNull('parent_id');
            }

            if ($request->filled('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            }

            // Apply common filters
            $this->applySearchFilter($query, $request, ['name', 'description']);

            // Include relationships
            $query->with(['parent:_id,name,slug', 'children:_id,name,slug,parent_id']);

            // Include product count if requested
            if ($request->boolean('with_product_count')) {
                $query->withCount('products');
            }

            // If requester wants all categories at once (no pagination), return sorted by sort_order asc
            if ($request->boolean('all') || $request->get('per_page') === 'all' || $request->get('paginate') === 'false') {
                $results = $query
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('name', 'asc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $results
                ]);
            }

            // Default: paginated response, allow sorting by permitted fields
            return $this->paginateAndRespond($query, $request, ['sort_order', 'name', 'created_at']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching categories', $e);
        }
    }

    /**
     * Get all categories organized as a tree structure with subcategories
     */
    public function categoriesTree(Request $request)
    {
        try {
            // Get all parent categories (top level)
            $categories = Category::with(['children' => function ($query) {
                                $query->where('status', true)
                                      ->orderBy('sort_order', 'asc')
                                      ->orderBy('name', 'asc');
                            }])
                            ->where('status', true)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order', 'asc')
                            ->orderBy('name', 'asc');

            // Include product count if requested
            if ($request->boolean('with_product_count')) {
                $categories->withCount('products');
            }

            $results = $categories->get();

            // Format the response
            $formattedResults = $results->map(function ($category) use ($request) {
                $result = [
                    'id' => $category->_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $category->image,
                    'sort_order' => $category->sort_order,
                ];

                if ($request->boolean('with_product_count')) {
                    $result['products_count'] = $category->products_count ?? 0;
                }

                // Add subcategories
                if ($category->children->isNotEmpty()) {
                    $result['subcategories'] = $category->children->map(function ($child) use ($request) {
                        $subcategory = [
                            'id' => $child->_id,
                            'name' => $child->name,
                            'slug' => $child->slug,
                            'description' => $child->description,
                            'image' => $child->image,
                            'sort_order' => $child->sort_order,
                        ];

                        if ($request->boolean('with_product_count')) {
                            $subcategory['products_count'] = $child->products_count ?? 0;
                        }

                        return $subcategory;
                    })->toArray();
                } else {
                    $result['subcategories'] = [];
                }

                return $result;
            });

            return response()->json([
                'success' => true,
                'data' => $formattedResults,
                'meta' => [
                    'total_categories' => $results->count(),
                    'total_subcategories' => $results->sum(fn($cat) => $cat->children->count()),
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching categories tree', $e);
        }
    }

    /**
     * Get testimonials with filtering
     */
    public function testimonials(Request $request)
    {
        try {
            $query = Testimonial::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Featured filter
            if ($request->filled('featured')) {
                $query->where('featured', $request->boolean('featured'));
            }

            // Rating filter
            if ($request->filled('min_rating')) {
                $query->where('rating', '>=', $request->min_rating);
            }

            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            // Apply common filters
            $this->applyCommonFilters($query, $request, ['company', 'position']);
            $this->applySearchFilter($query, $request, ['name', 'content', 'company', 'position']);

            return $this->paginateAndRespond($query, $request, ['sort_order', 'rating', 'created_at', 'featured']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching testimonials', $e);
        }
    }

    /**
     * Get pages with filtering
     */
    public function pages(Request $request)
    {
        try {
            $query = Page::query();

            // Status filter (default to active)
            $this->applyStatusFilter($query, $request);

            // Template filter
            if ($request->filled('template')) {
                $query->where('template', $request->template);
            }

            // Apply common filters
            $this->applySearchFilter($query, $request, ['title', 'content', 'excerpt']);

            return $this->paginateAndRespond($query, $request, ['sort_order', 'title', 'created_at']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching pages', $e);
        }
    }

    /**
     * Get popups with filtering
     */
    public function popups(Request $request)
    {
        try {
            $query = Popup::query();

            // Active and should show filter
            if ($request->boolean('active_only', true)) {
                $query->shouldShow();
            }

            // Type filter
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Position filter
            if ($request->filled('position')) {
                $query->where('position', $request->position);
            }

            // Page filter
            if ($request->filled('page')) {
                $query->forPage($request->page);
            }

            // Target users filter
            if ($request->filled('target_users')) {
                $query->where('target_users', $request->target_users);
            }

            // Apply common filters
            $this->applySearchFilter($query, $request, ['title', 'content']);
            $this->applyDateFilters($query, $request, 'start_date', 'end_date');

            return $this->paginateAndRespond($query, $request, ['priority', 'created_at', 'title']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching popups', $e);
        }
    }

    /**
     * Get hero slides
     */
    public function heroSlides(Request $request)
    {
        try {
            $query = HeroSlide::query();

            // Active and displayable filter (default to active)
            if ($request->boolean('active_only', true)) {
                $slides = HeroSlide::getActiveSlides();

                // Map to array and include explicit HTML alias (prefers uploaded file if present)
                $items = $slides->map(function ($slide) {
                    $data = $slide->toArray();
                    $data['content_html'] = $slide->content_html ?? '';
                    // Provide full URL for uploaded HTML file when present
                    if (!empty($slide->content_file)) {
                        if (Storage::disk('public')->exists($slide->content_file)) {
                            $data['content_file'] = Storage::disk('public')->url($slide->content_file);
                        } else {
                            // Fallback to asset path if file URL resolution fails
                            $data['content_file'] = asset('storage/' . ltrim($slide->content_file, '/'));
                        }
                    }
                    return $data;
                });

                $response = response()->json([
                    'success' => true,
                    'data' => $items,
                    'count' => $slides->count()
                ]);
                // Explicitly disable hex-tag escaping so HTML remains readable
                $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return $response;
            }

            // Status filter
            $this->applyStatusFilter($query, $request);

            // Display schedule filters
            if ($request->filled('displayable_only')) {
                $now = now();
                $query->where(function ($q) use ($now) {
                    $q->where('display_from', '<=', $now)
                        ->orWhereNull('display_from');
                })
                ->where(function ($q) use ($now) {
                    $q->where('display_to', '>=', $now)
                        ->orWhereNull('display_to');
                });
            }

            // Position filter
            if ($request->filled('position')) {
                $query->where('content_position', $request->position);
            }

            // Apply search filter
            $this->applySearchFilter($query, $request, ['title', 'subtitle', 'button_text']);

            // Sorting
            $sortField = $request->get('sort_by', 'order');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $results = $query->paginate($perPage);

            // Map items to include explicit HTML alias for clients (prefers uploaded file if present)
            $items = collect($results->items())->map(function ($slide) {
                $data = $slide->toArray();
                $data['content_html'] = $slide->content_html ?? '';
                return $data;
            });

            $response = response()->json([
                'success' => true,
                'data' => $items,
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                    'from' => $results->firstItem(),
                    'to' => $results->lastItem(),
                ],
                'links' => [
                    'first' => $results->url(1),
                    'last' => $results->url($results->lastPage()),
                    'prev' => $results->previousPageUrl(),
                    'next' => $results->nextPageUrl(),
                ]
            ]);
            // Explicitly disable hex-tag escaping so HTML remains readable
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return $response;

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching hero slides', $e);
        }
    }

    /**
     * Get job openings with filtering
     */
    public function jobOpenings(Request $request)
    {
        try {
            $query = JobOpening::query();

            // Active filter: explicit 'is_active' param takes precedence over 'active_only' default
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            } elseif ($request->boolean('active_only', true)) {
                $query->where('is_active', true);
            }

            // Display schedule filters (default to displayable only)
            // If user explicitly asks for 'displayable_only=false', we skip this.
            if ($request->boolean('displayable_only', true)) {
                $now = now();
                $query->where(function ($q) use ($now) {
                    $q->whereNull('display_from')
                      ->orWhere('display_from', '')
                      ->orWhere('display_from', '<=', $now);
                })->where(function ($q) use ($now) {
                    $q->whereNull('display_to')
                      ->orWhere('display_to', '')
                      ->orWhere('display_to', '>=', $now);
                });
            } else {
                // Optional range filter if not strict displayable_only
                $this->applyDateFilters($query, $request, 'display_from', 'display_to');
            }

            // Search across common fields
            $this->applySearchFilter($query, $request, ['title', 'description', 'location', 'employment_type']);

            // Location filter
            if ($request->filled('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            // Employment type filter
            if ($request->filled('employment_type')) {
                $query->where('employment_type', $request->employment_type);
            }

            // Default ordering by custom order then recency
            $query->orderBy('order')->orderByDesc('created_at');

            return $this->paginateAndRespond($query, $request, ['order', 'created_at', 'title']);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching job openings', $e);
        }
    }

    /**
     * Get a single job opening by ID
     */
    public function jobOpening(Request $request, $identifier)
    {
        try {
            $job = null;

            // Try to find by ID (works for numeric or MongoDB ObjectId strings)
            $job = JobOpening::find($identifier);

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job opening not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $job
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching job opening', $e);
        }
    }

    /**
     * Get contact information
     */
    public function contactInfo(Request $request)
    {
        try {
            $contactInfo = ContactInfo::getActive();
            
            if (!$contactInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact information not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $contactInfo
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching contact information', $e);
        }
    }

    /**
     * Get about us information
     */
    public function aboutUs(Request $request)
    {
        try {
            $aboutUs = AboutUs::getPublished();
            
            if (!$aboutUs) {
                return response()->json([
                    'success' => false,
                    'message' => 'About us information not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $aboutUs
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching about us information', $e);
        }
    }

    /**
     * Get single item by slug or ID from any content type
     */
    public function show(Request $request, $type, $identifier)
    {
        try {
            // Handle site policy and legal pages via site settings
            if ($type === 'site') {
                return $this->getSiteContentResponse($identifier);
            }

            $model = $this->getModel($type);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid content type'
                ], 400);
            }

            $query = $model::query();

            // Apply status filter based on type
            if (method_exists($model, 'scopeActive')) {
                $query->active();
            } elseif (method_exists($model, 'scopePublished')) {
                $query->published();
            }

            // Try to find by slug first, then by ID
            // Try to find by ID (numeric or MongoDB ObjectId) first, then fallback to slug
            $item = (clone $query)->where('_id', $identifier)->first();
            
            if (!$item && !is_numeric($identifier)) {
                $item = (clone $query)->where('slug', $identifier)->first();
            }

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($type) . ' not found'
                ], 404);
            }

            // Load specific relationships based on type
            $this->loadRelationships($item, $type);

            return response()->json([
                'success' => true,
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching ' . $type, $e);
        }
    }

    /**
     * Return site policy/legal content from HeaderFooter settings
     */
    private function getSiteContentResponse($identifier)
    {
        // Map URL slug to HeaderFooter field
        $map = [
            'privacy-policy' => 'privacy_policy',
            'terms-of-service' => 'terms_of_service',
            'cookie-policy' => 'cookie_policy',
            'disclaimer' => 'disclaimer',
        ];

        if (!array_key_exists($identifier, $map)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid site content identifier'
            ], 400);
        }

        $settings = HeaderFooter::first();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Site settings not found'
            ], 404);
        }

        $field = $map[$identifier];
        $content = $settings->{$field} ?? null;

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found'
            ], 404);
        }

        // Build a simple, consistent payload
        $titleMap = [
            'privacy-policy' => 'Privacy Policy',
            'terms-of-service' => 'Terms of Service',
            'cookie-policy' => 'Cookie Policy',
            'disclaimer' => 'Disclaimer',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'slug' => $identifier,
                'title' => $titleMap[$identifier] ?? $identifier,
                'content' => $content,
            ]
        ]);
    }

    /**
     * Get statistics for all content types
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [
                'blogs' => [
                    'total' => Blog::published()->count(),
                    'categories' => Blog::select('category')->whereNotNull('category')->distinct()->count(),
                    'recent' => Blog::published()->where('published_at', '>=', now()->subDays(30))->count(),
                ],
                'solutions' => [
                    'total' => Solution::active()->count(),
                    'featured' => Solution::active()->featured()->count(),
                    'categories' => Solution::select('category')->whereNotNull('category')->distinct()->count(),
                ],
                'software' => [
                    'total' => Software::active()->count(),
                    'featured' => Software::active()->featured()->count(),
                    'free' => Software::active()->free()->count(),
                ],
                'products' => [
                    'total' => Product::where('status', true)->count(),
                    'categories' => Category::where('status', true)->count(),
                ],
                'testimonials' => [
                    'total' => Testimonial::active()->count(),
                    'featured' => Testimonial::active()->featured()->count(),
                    'average_rating' => round(Testimonial::active()->avg('rating') ?? 0, 1),
                ],
                'pages' => [
                    'total' => Page::active()->count(),
                ],
                'popups' => [
                    'total' => Popup::active()->count(),
                    'active_now' => Popup::shouldShow()->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching statistics', $e);
        }
    }

    /**
     * Get dropdown options for filters
     */
    public function filterOptions(Request $request)
    {
        try {
            $options = [
                'blog_categories' => Blog::select('category')->whereNotNull('category')->distinct()->pluck('category')->sort()->values(),
                'solution_categories' => Solution::select('category')->whereNotNull('category')->distinct()->pluck('category')->sort()->values(),
                'software_categories' => Software::select('main_category')->whereNotNull('main_category')->distinct()->pluck('main_category')->sort()->values(),
                'software_platforms' => $this->getUniqueArrayValues(Software::active()->get(), 'platforms'),
                'popup_types' => collect(Popup::getTypes())->keys(),
                'popup_positions' => collect(Popup::getPositions())->keys(),
                'testimonial_companies' => Testimonial::select('company')->whereNotNull('company')->distinct()->pluck('company')->sort()->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $options
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching filter options', $e);
        }
    }

    // Helper Methods

    /**
     * Resolve full SVG markup for a Tabler icon class (e.g., "tabler-credit-card").
     * Returns null if not a Tabler icon or not found.
     */
    private function resolveTablerSvg(?string $iconClass): ?string
    {
        if (!$iconClass || stripos($iconClass, 'tabler-') !== 0) {
            return null;
        }

        $map = $this->getTablerIconMap();
        return $map[$iconClass] ?? null;
    }

    /**
     * Build and cache a map of Tabler icon classes to decoded SVG strings
     * by parsing public/assets/vendor/fonts/iconify-icons.css.
     */
    private function getTablerIconMap(): array
    {
        return Cache::remember('tabler_icon_map', 3600, function () {
            $path = public_path('assets/vendor/fonts/iconify-icons.css');
            if (!File::exists($path)) {
                return [];
            }

            $css = File::get($path);
            $map = [];
            $pattern = '/\.tabler-([a-z0-9\-]+)\s*\{[^}]*--svg:\s*url\(["\']data:image\/(?:svg\+)?xml,([^"\']+)["\']\)/i';
            if (preg_match_all($pattern, $css, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $name = $m[1];
                    $encoded = $m[2];
                    $svg = urldecode($encoded);
                    $map['tabler-' . $name] = $svg;
                }
            }

            return $map;
        });
    }

    private function applyStatusFilter($query, $request)
    {
        if ($request->has('status')) {
            $query->where('status', $request->boolean('status'));
        } else {
            // Default to active/published
            if (method_exists($query->getModel(), 'scopeActive')) {
                $query->active();
            } else {
                $query->where('status', true);
            }
        }
    }

    private function applyCommonFilters($query, $request, $fields = [])
    {
        foreach ($fields as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }
    }

    private function applyDateFilters($query, $request, $dateField = 'created_at', $endDateField = null)
    {
        if ($request->filled('date_from')) {
            $query->whereDate($dateField, '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $field = $endDateField ?: $dateField;
            $query->whereDate($field, '<=', $request->date_to);
        }

        if ($request->filled('year')) {
            $query->whereYear($dateField, $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth($dateField, $request->month);
        }
    }

    private function applySearchFilter($query, $request, $fields)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', '%' . $search . '%');
                }
            });
        }
    }

    private function paginateAndRespond($query, $request, $allowedSortFields = ['created_at'])
    {
        // Sorting
        $sortField = $request->get('sort_by', $allowedSortFields[0]);
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = $allowedSortFields[0];
        }
        
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $results = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
            ],
            'links' => [
                'first' => $results->url(1),
                'last' => $results->url($results->lastPage()),
                'prev' => $results->previousPageUrl(),
                'next' => $results->nextPageUrl(),
            ]
        ]);
    }

    private function errorResponse($message, $exception = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') && $exception ? $exception->getMessage() : 'Internal server error'
        ], 500);
    }

    private function getModel($type)
    {
        $models = [
            'blog' => Blog::class,
            'blogs' => Blog::class,
            'solution' => Solution::class,
            'solutions' => Solution::class,
            'service' => Service::class,
            'services' => Service::class,
            'integration-module' => IntegrationModule::class,
            'integration-modules' => IntegrationModule::class,
            'software' => Software::class,
            'product' => Product::class,
            'products' => Product::class,
            'category' => Category::class,
            'categories' => Category::class,
            'testimonial' => Testimonial::class,
            'testimonials' => Testimonial::class,
            'faq' => Faq::class,
            'faqs' => Faq::class,
            'page' => Page::class,
            'pages' => Page::class,
            'popup' => Popup::class,
            'popups' => Popup::class,
            'job-opening' => JobOpening::class,
            'job-openings' => JobOpening::class,
            'client' => Client::class,
            'clients' => Client::class,
            'certification' => Certification::class,
            'certifications' => Certification::class,
        ];

        return $models[$type] ?? null;
    }

    private function loadRelationships($item, $type)
    {
        switch ($type) {
            case 'blog':
            case 'blogs':
                $item->load('author:_id,name,email');
                break;
            case 'product':
            case 'products':
                $item->load('category:_id,name,slug');
                break;
            case 'category':
            case 'categories':
                $item->load(['parent:_id,name,slug', 'children:_id,name,slug,parent_id']);
                break;
        }
    }

    private function getUniqueArrayValues($collection, $field)
    {
        $values = collect();
        foreach ($collection as $item) {
            if (is_array($item->$field)) {
                $values = $values->merge($item->$field);
            }
        }
        return $values->unique()->sort()->values();
    }
}

