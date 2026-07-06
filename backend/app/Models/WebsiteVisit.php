<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class WebsiteVisit extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'website_visits';

    protected $fillable = [
        'session_id',
        'visitor_id',
        'ip_address',
        'user_agent',
        'url',
        'page_title',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'country',
        'country_code',
        'region',
        'city',
        'latitude',
        'longitude',
        'device_type',
        'browser',
        'browser_version',
        'platform',
        'screen_width',
        'screen_height',
        'time_on_page',
        'is_bounce',
        'is_unique_visitor',
        'visited_at',
        'type',
        'count',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'screen_width' => 'integer',
        'screen_height' => 'integer',
        'time_on_page' => 'integer',
        'is_bounce' => 'boolean',
        'is_unique_visitor' => 'boolean',
        'visited_at' => 'datetime',
    ];

    protected $dates = [
        'visited_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get visits for today
     */
    public static function today()
    {
        return static::whereDate('visited_at', Carbon::today());
    }

    /**
     * Get visits for this week
     */
    public static function thisWeek()
    {
        return static::whereBetween('visited_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Get visits for this month
     */
    public static function thisMonth()
    {
        return static::whereMonth('visited_at', Carbon::now()->month)
                    ->whereYear('visited_at', Carbon::now()->year);
    }

    /**
     * Get visits by page
     */
    public static function popularPages($limit = 10)
    {
        return static::raw(function($collection) use ($limit) {
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
    }

    /**
     * Get bounce rate
     */
    public static function bounceRate()
    {
        $total = static::sum('count');
        if ($total === 0) return 0;
        
        $bounced = static::where('is_bounce', true)->count();
        return round(($bounced / $total) * 100, 2);
    }

    /**
     * Get visits trend for charts
     */
    public static function visitsTrend($days = 30)
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        
        return static::raw(function($collection) use ($startDate) {
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
    }
}
