<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebsiteVisit;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Record a website visit
     */
    public function recordVisit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'url' => 'required|url',
            'page_title' => 'nullable|string',
            'referrer' => 'nullable|url',
            'utm_source' => 'nullable|string',
            'utm_medium' => 'nullable|string',
            'utm_campaign' => 'nullable|string',
            'utm_term' => 'nullable|string',
            'utm_content' => 'nullable|string',
            'device_type' => 'nullable|string',
            'browser' => 'nullable|string',
            'browser_version' => 'nullable|string',
            'platform' => 'nullable|string',
            'screen_width' => 'nullable|integer',
            'screen_height' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Get user's IP and User Agent
            $ipAddress = $this->getRealIpAddress($request);
            $userAgent = $request->userAgent();
            
            // Check if this is a unique visitor
            $visitorId = $request->input('visitor_id') ?: $this->generateVisitorId($ipAddress, $userAgent);
            $isUniqueVisitor = !WebsiteVisit::where('visitor_id', $visitorId)
                                          ->whereDate('visited_at', Carbon::today())
                                          ->exists();

            // Get location data (you can integrate with IP geolocation service)
            $locationData = $this->getLocationData($ipAddress);

            $visitData = array_merge($validator->validated(), [
                'visitor_id' => $visitorId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'is_unique_visitor' => $isUniqueVisitor,
                'visited_at' => Carbon::now(),
            ], $locationData);

            // Instead of creating a new doc, we increment a counter for the URL and date
            // We identify the record by URL, date, and a specific type 'aggregated'
            $visit = WebsiteVisit::raw(function($collection) use ($visitData) {
                return $collection->findOneAndUpdate(
                    [
                        'url' => $visitData['url'],
                        'visited_at_date' => Carbon::today()->toDateString(),
                        'type' => 'aggregated'
                    ],
                    [
                        '$inc' => ['count' => 1],
                        '$set' => [
                            'page_title' => $visitData['page_title'] ?? 'Untitled',
                            'visited_at' => new \MongoDB\BSON\UTCDateTime(Carbon::now()),
                            'device_type' => $visitData['device_type'] ?? 'desktop',
                            'browser' => $visitData['browser'] ?? null,
                            'platform' => $visitData['platform'] ?? null,
                            'is_unique_visitor' => $visitData['is_unique_visitor'] ?? false,
                        ]
                    ],
                    [
                        'upsert' => true,
                        'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER
                    ]
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Visit recorded successfully',
                'data' => [
                    'visit_id' => (string)$visit['_id'],
                    'visitor_id' => $visitorId,
                    'is_unique_visitor' => $isUniqueVisitor,
                    'count' => $visit['count'] ?? 1
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording visit',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update visit data (time on page, bounce status)
     */
    public function updateVisit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visit_id' => 'required|string',
            'time_on_page' => 'nullable|integer|min:0',
            'is_bounce' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $visit = WebsiteVisit::find($request->input('visit_id'));
            
            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visit not found'
                ], 404);
            }

            $updateData = array_filter([
                'time_on_page' => $request->input('time_on_page'),
                'is_bounce' => $request->input('is_bounce'),
            ], function($value) {
                return $value !== null;
            });

            $visit->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Visit updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating visit',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Record user activity
     */
    public function recordActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'visitor_id' => 'nullable|string',
            'action' => 'required|string|in:click,scroll,hover,form_submit,download,view,search,share',
            'element' => 'nullable|string',
            'element_id' => 'nullable|string',
            'element_class' => 'nullable|string',
            'element_text' => 'nullable|string',
            'page_url' => 'required|url',
            'page_title' => 'nullable|string',
            'coordinates_x' => 'nullable|integer',
            'coordinates_y' => 'nullable|integer',
            'scroll_depth' => 'nullable|integer|min:0|max:100',
            'time_spent' => 'nullable|integer|min:0',
            'device_type' => 'nullable|string',
            'browser' => 'nullable|string',
            'platform' => 'nullable|string',
            'screen_width' => 'nullable|integer',
            'screen_height' => 'nullable|integer',
            'viewport_width' => 'nullable|integer',
            'viewport_height' => 'nullable|integer',
            'custom_data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $activityData = array_merge($validator->validated(), [
                'ip_address' => $this->getRealIpAddress($request),
                'user_agent' => $request->userAgent(),
                'occurred_at' => Carbon::now(),
            ]);

            $activity = UserActivity::create($activityData);

            return response()->json([
                'success' => true,
                'message' => 'Activity recorded successfully',
                'data' => [
                    'activity_id' => $activity->id
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording activity',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Batch record multiple activities
     */
    public function recordActivities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activities' => 'required|array|max:100',
            'activities.*.session_id' => 'required|string',
            'activities.*.action' => 'required|string',
            'activities.*.page_url' => 'required|url',
            'activities.*.occurred_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $ipAddress = $this->getRealIpAddress($request);
            $userAgent = $request->userAgent();
            $activities = [];

            foreach ($request->input('activities') as $activityData) {
                $activities[] = array_merge($activityData, [
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'occurred_at' => isset($activityData['occurred_at']) 
                                   ? Carbon::parse($activityData['occurred_at']) 
                                   : Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            UserActivity::insert($activities);

            return response()->json([
                'success' => true,
                'message' => count($activities) . ' activities recorded successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording activities',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get visitor statistics
     */
    public function getStats(Request $request)
    {
        try {
            $period = $request->input('period', '30days'); // today, week, month, 30days, 90days

            $stats = [
                'visits' => $this->getVisitStats($period),
                'activities' => UserActivity::getStats(),
                'popular_pages' => $this->getPopularPages($period),
                'traffic_sources' => $this->getTrafficSources($period),
                'device_breakdown' => $this->getDeviceBreakdown($period),
                'location_data' => $this->getLocationStats($period),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get simple visitor count
     */
    public function getVisitCount()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_visits' => WebsiteVisit::sum('count'),
                    'today_visits' => WebsiteVisit::whereDate('visited_at', Carbon::today())->sum('count'),
                    'unique_visitors_today' => WebsiteVisit::whereDate('visited_at', Carbon::today())
                                                         ->where('is_unique_visitor', true)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching visit count',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get real IP address
     */
    private function getRealIpAddress(Request $request)
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                return trim($ips[0]);
            }
        }

        return $request->ip();
    }

    /**
     * Generate visitor ID
     */
    private function generateVisitorId($ipAddress, $userAgent)
    {
        return hash('sha256', $ipAddress . $userAgent . config('app.key'));
    }

    /**
     * Get location data from IP (placeholder - integrate with real service)
     */
    private function getLocationData($ipAddress)
    {
        // In production, integrate with services like MaxMind GeoIP2, ipinfo.io, etc.
        // For now, return default values
        return [
            'country' => null,
            'country_code' => null,
            'region' => null,
            'city' => null,
            'latitude' => null,
            'longitude' => null,
        ];
    }

    /**
     * Get visit statistics for a period
     */
    private function getVisitStats($period)
    {
        $query = WebsiteVisit::query();

        switch ($period) {
            case 'today':
                $query->whereDate('visited_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('visited_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereMonth('visited_at', Carbon::now()->month)
                      ->whereYear('visited_at', Carbon::now()->year);
                break;
            case '30days':
                $query->where('visited_at', '>=', Carbon::now()->subDays(30));
                break;
            case '90days':
                $query->where('visited_at', '>=', Carbon::now()->subDays(90));
                break;
        }

        return [
            'total_visits' => $query->sum('count'),
            'unique_visitors' => $query->where('is_unique_visitor', true)->count(),
            'bounce_rate' => $this->calculateBounceRate($query),
            'avg_time_on_page' => $this->calculateAverageTimeOnPage($query),
        ];
    }

    /**
     * Get popular pages
     */
    private function getPopularPages($period)
    {
        $query = WebsiteVisit::query();
        
        // Apply period filter
        if ($period === 'today') {
            $query->whereDate('visited_at', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('visited_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        // Use MongoDB aggregation for grouping
        return $query->raw(function($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => ['url' => '$url', 'page_title' => '$page_title'],
                        'visits' => ['$sum' => ['$ifNull' => ['$count', 1]]]
                    ]
                ],
                ['$sort' => ['visits' => -1]],
                ['$limit' => 10]
            ]);
        });
    }

    /**
     * Get traffic sources
     */
    private function getTrafficSources($period)
    {
        $query = WebsiteVisit::query();
        
        if ($period === 'today') {
            $query->whereDate('visited_at', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('visited_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        return $query->raw(function($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => '$utm_source',
                        'visits' => ['$sum' => ['$ifNull' => ['$count', 1]]]
                    ]
                ],
                ['$sort' => ['visits' => -1]],
                ['$limit' => 10]
            ]);
        });
    }

    /**
     * Get device breakdown
     */
    private function getDeviceBreakdown($period)
    {
        $query = WebsiteVisit::query();
        
        if ($period === 'today') {
            $query->whereDate('visited_at', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('visited_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        return $query->raw(function($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => '$device_type',
                        'visits' => ['$sum' => ['$ifNull' => ['$count', 1]]]
                    ]
                ],
                ['$sort' => ['visits' => -1]]
            ]);
        });
    }

    /**
     * Get location statistics
     */
    private function getLocationStats($period)
    {
        $query = WebsiteVisit::query()->whereNotNull('country');
        
        if ($period === 'today') {
            $query->whereDate('visited_at', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('visited_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        return $query->raw(function($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => ['country' => '$country', 'country_code' => '$country_code'],
                        'visits' => ['$sum' => ['$ifNull' => ['$count', 1]]]
                    ]
                ],
                ['$sort' => ['visits' => -1]],
                ['$limit' => 10]
            ]);
        });
    }

    /**
     * Calculate bounce rate
     */
    private function calculateBounceRate($query)
    {
        $total = $query->sum('count');
        if ($total === 0) return 0;
        
        $bounced = $query->where('is_bounce', true)->count();
        return round(($bounced / $total) * 100, 2);
    }

    /**
     * Calculate average time on page
     */
    private function calculateAverageTimeOnPage($query)
    {
        return $query->whereNotNull('time_on_page')
                    ->where('time_on_page', '>', 0)
                    ->avg('time_on_page') ?: 0;
    }
}