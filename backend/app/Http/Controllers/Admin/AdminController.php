<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Page;
use App\Models\WebsiteVisit;
use App\Models\ContactFormSubmission;
use App\Models\PartnerRegistration;
use App\Models\UserActivity;
use App\Models\DashboardStat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch stats from database (no fallback calculation here to ensure speed)
        $statsData = DashboardStat::where('key', 'admin.dashboard.stats')->first();
        $stats = $statsData ? $statsData->value : [
            'total_users' => 0, 'active_users' => 0, 'total_roles' => 0,
            'total_categories' => 0, 'active_categories' => 0,
            'total_products' => 0, 'active_products' => 0,
            'total_blogs' => 0, 'published_blogs' => 0,
            'total_pages' => 0, 'active_pages' => 0,
        ];
        $lastUpdated = $statsData ? $statsData->last_updated_at : null;

        $analyticsData = DashboardStat::where('key', 'admin.dashboard.analytics')->first();
        $analytics = $analyticsData ? $analyticsData->value : [
            'total_visits' => 0, 'today_visits' => 0, 'unique_visitors_today' => 0,
            'this_week_visits' => 0, 'bounce_rate' => 0, 'avg_time_on_page' => 0,
            'top_pages' => [], 'device_breakdown' => [], 'visits_trend' => [],
        ];

        $contactStatsData = DashboardStat::where('key', 'admin.dashboard.contact_stats')->first();
        $contactStats = $contactStatsData ? $contactStatsData->value : [
            'total' => 0, 'new' => 0, 'read' => 0, 'replied' => 0, 'closed' => 0, 'today' => 0
        ];

        $partnerStatsData = DashboardStat::where('key', 'admin.dashboard.partner_stats')->first();
        $partnerStats = $partnerStatsData ? $partnerStatsData->value : [
            'total' => 0, 'new' => 0, 'under_review' => 0, 'approved' => 0, 'rejected' => 0, 'on_hold' => 0, 'today' => 0, 'this_month' => 0
        ];

        $activityStatsData = DashboardStat::where('key', 'admin.dashboard.activity_stats')->first();
        $activityStats = $activityStatsData ? $activityStatsData->value : [
            'total' => 0, 'today' => 0, 'clicks' => 0, 'scrolls' => 0, 'form_submits' => 0, 'downloads' => 0
        ];

        $recentDataEntry = DashboardStat::where('key', 'admin.dashboard.recent_data')->first();
        $recentData = $recentDataEntry ? $recentDataEntry->value : [];

        // Helper to recursively convert arrays to Fluent objects
        $toFluent = function ($item) use (&$toFluent) {
            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    $item[$key] = $toFluent($value);
                }
                return new \Illuminate\Support\Fluent($item);
            }
            return $item;
        };

        // Helper to convert array of items to collection of Fluent objects
        $toFluentCollection = function ($items) use ($toFluent) {
            return collect($items ?? [])->map(function ($item) use ($toFluent) {
                return $toFluent($item);
            });
        };

        $recent_users = $toFluentCollection($recentData['users'] ?? []);
        $recent_products = $toFluentCollection($recentData['products'] ?? []);
        $recent_blogs = $toFluentCollection($recentData['blogs'] ?? []);
        $recent_partners = $toFluentCollection($recentData['partners'] ?? []);

        $recent_contacts = $toFluentCollection($recentData['contacts'] ?? [])->map(function($item) {
            if (isset($item->created_at)) {
                $item->created_at = \Carbon\Carbon::parse($item->created_at);
            }
            return $item;
        });

        $recent_visits = $toFluentCollection($recentData['visits'] ?? [])->map(function($item) {
            if (isset($item->visited_at)) {
                $item->visited_at = \Carbon\Carbon::parse($item->visited_at);
            }
            return $item;
        });

        return view('admin.dashboard', compact(
            'stats', 
            'analytics', 
            'contactStats', 
            'partnerStats', 
            'activityStats',
            'recent_users', 
            'recent_products', 
            'recent_blogs',
            'recent_contacts',
            'recent_partners',
            'recent_visits',
            'lastUpdated'
        ));
    }

    public function refreshStats()
    {
        try {
            Artisan::call('dashboard:update-stats');
            return redirect()->route('admin.dashboard')
                            ->with('success', 'Dashboard statistics updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                            ->with('error', 'Failed to update statistics: ' . $e->getMessage());
        }
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->_id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        $userData = $request->only(['name', 'email', 'phone', 'address']);

        if ($request->hasFile('profile_image')) {
            // Delete old profile image
            if ($user->profile_image) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $userData['profile_image'] = $request->file('profile_image')->store('users', 'public');
        }

        $user->update($userData);

        return redirect()->route('admin.profile')
                        ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => $request->password
        ]);

        return redirect()->route('admin.profile')
                        ->with('success', 'Password changed successfully.');
    }

    public function getStats()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', true)->count(),
                'inactive' => User::where('status', false)->count(),
            ],
            'products' => [
                'total' => Product::count(),
                'active' => Product::where('status', true)->count(),
                'inactive' => Product::where('status', false)->count(),
            ],
            'blogs' => [
                'total' => Blog::count(),
                'published' => Blog::where('status', true)->count(),
                'draft' => Blog::where('status', false)->count(),
            ],
            'categories' => [
                'total' => Category::count(),
                'active' => Category::where('status', true)->count(),
                'inactive' => Category::where('status', false)->count(),
            ],
        ];

        return response()->json($stats);
    }

    public function searchGlobal(Request $request)
    {
        $search = $request->get('q');
        $results = [];

        if (strlen($search) >= 2) {
            // Search users
            $users = User::where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->limit(5)->get();
            
            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'url' => route('admin.users.show', $user),
                ];
            }

            // Search products
            $products = Product::where('title', 'like', "%{$search}%")
                             ->limit(5)->get();
            
            foreach ($products as $product) {
                $results[] = [
                    'type' => 'product',
                    'title' => $product->title,
                    'subtitle' => 'Product',
                    'url' => route('admin.products.show', $product),
                ];
            }

            // Search blogs
            $blogs = Blog::where('title', 'like', "%{$search}%")
                        ->limit(5)->get();
            
            foreach ($blogs as $blog) {
                $results[] = [
                    'type' => 'blog',
                    'title' => $blog->title,
                    'subtitle' => 'Blog Post',
                    'url' => route('admin.blogs.show', $blog),
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Calculate bounce rate
     */
    private function calculateBounceRate()
    {
        $total = WebsiteVisit::sum('count');
        if ($total === 0) return 0;
        
        $bounced = WebsiteVisit::where('is_bounce', true)->count();
        return round(($bounced / $total) * 100, 1);
    }

    /**
     * Calculate average time on page
     */
    private function calculateAverageTimeOnPage()
    {
        $average = WebsiteVisit::whereNotNull('time_on_page')
                             ->where('time_on_page', '>', 0)
                             ->avg('time_on_page');
        
        return $average ? round($average / 1000, 1) : 0; // Convert to seconds
    }

    /**
     * Get top pages
     */
    private function getTopPages($limit = 5)
    {
        try {
            $result = WebsiteVisit::raw(function($collection) use ($limit) {
                return $collection->aggregate([
                    [
                        '$group' => [
                            '_id' => ['url' => '$url', 'page_title' => '$page_title'],
                            'visits' => ['$sum' => ['$ifNull' => ['$count', 1]]]
                        ]
                    ],
                    ['$sort' => ['visits' => -1]],
                    ['$limit' => $limit]
                ]);
            });

            return collect($result)->map(function($item) {
                return [
                    'url' => $item['_id']['url'] ?? 'Unknown',
                    'page_title' => $item['_id']['page_title'] ?? 'Untitled',
                    'visits' => $item['visits'] ?? 0
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get device breakdown
     */
    private function getDeviceBreakdown()
    {
        try {
            $result = WebsiteVisit::raw(function($collection) {
                return $collection->aggregate([
                    [
                        '$match' => [
                            'device_type' => ['$exists' => true, '$ne' => null]
                        ]
                    ],
                    [
                        '$group' => [
                            '_id' => '$device_type',
                            'count' => ['$sum' => ['$ifNull' => ['$count', 1]]]
                        ]
                    ],
                    ['$sort' => ['count' => -1]]
                ]);
            });

            return collect($result)->map(function($item) {
                return [
                    'device' => ucfirst($item['_id'] ?? 'Unknown'),
                    'count' => $item['count'] ?? 0
                ];
            });
        } catch (\Exception $e) {
            return collect([
                ['device' => 'Desktop', 'count' => 0],
                ['device' => 'Mobile', 'count' => 0],
                ['device' => 'Tablet', 'count' => 0],
            ]);
        }
    }

    /**
     * Get visits trend for the last 7 days
     */
    private function getVisitsTrend()
    {
        try {
            $startDate = Carbon::now()->subDays(7)->startOfDay();
            
            $result = WebsiteVisit::raw(function($collection) use ($startDate) {
                return $collection->aggregate([
                    [
                        '$match' => [
                            'visited_at' => ['$gte' => $startDate->toDateTime()]
                        ]
                    ],
                    [
                        '$group' => [
                            '_id' => [
                                'year' => ['$year' => '$visited_at'],
                                'month' => ['$month' => '$visited_at'],
                                'day' => ['$dayOfMonth' => '$visited_at']
                            ],
                            'visits' => ['$sum' => ['$ifNull' => ['$count', 1]]]
                        ]
                    ],
                    ['$sort' => ['_id' => 1]]
                ]);
            });

            // Fill in missing dates with 0 visits
            $trendData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateKey = $date->format('Y-m-d');
                
                $found = false;
                foreach ($result as $item) {
                    $itemDate = sprintf('%04d-%02d-%02d', 
                        $item['_id']['year'], 
                        $item['_id']['month'], 
                        $item['_id']['day']
                    );
                    
                    if ($itemDate === $dateKey) {
                        $trendData[] = [
                            'date' => $date->format('M d'),
                            'visits' => $item['visits']
                        ];
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $trendData[] = [
                        'date' => $date->format('M d'),
                        'visits' => 0
                    ];
                }
            }

            return $trendData;
        } catch (\Exception $e) {
            // Return default data if aggregation fails
            $trendData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $trendData[] = [
                    'date' => $date->format('M d'),
                    'visits' => 0
                ];
            }
            return $trendData;
        }
    }
}
