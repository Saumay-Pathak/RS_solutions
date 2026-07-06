<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
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
use Carbon\Carbon;

class UpdateDashboardStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:update-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and store admin dashboard statistics in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting dashboard stats calculation...');
        $now = Carbon::now();

        // 1. Basic Counts (Fast)
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', true)->count(),
            'total_roles' => Role::count(),
            'total_categories' => Category::count(),
            'active_categories' => Category::where('status', true)->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('status', true)->count(),
            'total_blogs' => Blog::count(),
            'published_blogs' => Blog::where('status', true)->count(),
            'total_pages' => Page::count(),
            'active_pages' => Page::where('status', true)->count(),
        ];

        DashboardStat::updateOrCreate(
            ['key' => 'admin.dashboard.stats'],
            ['value' => $stats, 'last_updated_at' => $now]
        );
        $this->info('Basic stats stored.');

        // 2. Analytics (Heavy)
        $this->info('Calculating analytics...');
        
        $analytics = [
            'total_visits' => WebsiteVisit::sum('count'),
            'today_visits' => WebsiteVisit::whereDate('visited_at', Carbon::today())->sum('count'),
            'unique_visitors_today' => WebsiteVisit::whereDate('visited_at', Carbon::today())
                                                 ->where('is_unique_visitor', true)->count(),
            'this_week_visits' => WebsiteVisit::whereBetween('visited_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->sum('count'),
            'bounce_rate' => $this->calculateBounceRate(),
            'avg_time_on_page' => $this->calculateAverageTimeOnPage(),
            'top_pages' => $this->getTopPages(),
            'device_breakdown' => $this->getDeviceBreakdown(),
            'visits_trend' => $this->getVisitsTrend(),
        ];

        DashboardStat::updateOrCreate(
            ['key' => 'admin.dashboard.analytics'],
            ['value' => $analytics, 'last_updated_at' => $now]
        );
        $this->info('Analytics stored.');

        // 3. Other Stats (Heavy counts)
        $this->info('Calculating other stats...');

        $contactStats = ContactFormSubmission::getStats();
        DashboardStat::updateOrCreate(
            ['key' => 'admin.dashboard.contact_stats'],
            ['value' => $contactStats, 'last_updated_at' => $now]
        );

        $partnerStats = PartnerRegistration::getStats();
        DashboardStat::updateOrCreate(
            ['key' => 'admin.dashboard.partner_stats'],
            ['value' => $partnerStats, 'last_updated_at' => $now]
        );

        $activityStats = UserActivity::getStats();
        DashboardStat::updateOrCreate(
            ['key' => 'admin.dashboard.activity_stats'],
            ['value' => $activityStats, 'last_updated_at' => $now]
        );

        $this->info('Other stats stored.');

        // 4. Recent Items (Queries)
        $this->info('Fetching recent items...');

        $recentData = [
            'users' => User::with('role')->latest()->limit(5)->get(),
            'products' => Product::with('category')->latest()->limit(5)->get(),
            'blogs' => Blog::with('author')->latest()->limit(5)->get(),
            'contacts' => ContactFormSubmission::latest()->limit(5)->get(),
            'partners' => PartnerRegistration::latest()->limit(3)->get(),
            'visits' => WebsiteVisit::latest('visited_at')->limit(10)->get(),
        ];

        DashboardStat::updateOrCreate(
            ['key' => 'admin.dashboard.recent_data'],
            ['value' => $recentData, 'last_updated_at' => $now]
        );
        $this->info('Recent items stored.');
        
        $this->info('Dashboard stats updated successfully.');
    }

    private function calculateBounceRate()
    {
        $total = WebsiteVisit::sum('count');
        if ($total === 0) return 0;
        
        $bounced = WebsiteVisit::where('is_bounce', true)->count(); // Bounces are still tracked individually if we still used them, or we might need to adjust this too if bounces are aggregated
        return round(($bounced / $total) * 100, 1);
    }

    private function calculateAverageTimeOnPage()
    {
        $average = WebsiteVisit::whereNotNull('time_on_page')
                             ->where('time_on_page', '>', 0)
                             ->avg('time_on_page');
        
        return $average ? round($average / 1000, 1) : 0;
    }

    private function getTopPages($limit = 5)
    {
        try {
            $result = WebsiteVisit::raw(function($collection) use ($limit) {
                return $collection->aggregate([
                    [
                        '$group' => [
                            '_id' => ['url' => '$url', 'page_title' => '$page_title'],
                            'visits' => ['$sum' => ['$ifNull' => ['$count', 1]]] // Handle both legacy 1-per-doc and new aggregated count field
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

            // Fill in missing dates
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
            return [];
        }
    }
}
