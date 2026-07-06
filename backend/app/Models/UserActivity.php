<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class UserActivity extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_activities';

    protected $fillable = [
        'session_id',
        'visitor_id',
        'ip_address',
        'user_agent',
        'action', // click, scroll, hover, form_submit, download, etc.
        'element', // button, link, form, image, etc.
        'element_id',
        'element_class',
        'element_text',
        'page_url',
        'page_title',
        'coordinates_x',
        'coordinates_y',
        'scroll_depth',
        'time_spent', // milliseconds
        'referrer',
        'device_type',
        'browser',
        'platform',
        'screen_width',
        'screen_height',
        'viewport_width',
        'viewport_height',
        'custom_data',
        'occurred_at',
    ];

    protected $casts = [
        'coordinates_x' => 'integer',
        'coordinates_y' => 'integer',
        'scroll_depth' => 'integer',
        'time_spent' => 'integer',
        'screen_width' => 'integer',
        'screen_height' => 'integer',
        'viewport_width' => 'integer',
        'viewport_height' => 'integer',
        'custom_data' => 'array',
        'occurred_at' => 'datetime',
    ];

    protected $dates = [
        'occurred_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get activities for today
     */
    public static function today()
    {
        return static::whereDate('occurred_at', Carbon::today())
                    ->orderBy('occurred_at', 'desc');
    }

    /**
     * Get activities by action type
     */
    public static function byAction($action)
    {
        return static::where('action', $action)
                    ->orderBy('occurred_at', 'desc');
    }

    /**
     * Get activities by page
     */
    public static function byPage($url)
    {
        return static::where('page_url', 'like', "%{$url}%")
                    ->orderBy('occurred_at', 'desc');
    }

    /**
     * Get popular actions
     */
    public static function popularActions($limit = 10)
    {
        return static::raw(function($collection) use ($limit) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => '$action',
                        'count' => ['$sum' => 1]
                    ]
                ],
                ['$sort' => ['count' => -1]],
                ['$limit' => $limit]
            ]);
        });
    }

    /**
     * Get heatmap data for a page
     */
    public static function heatmapData($url, $action = 'click')
    {
        return static::where('page_url', $url)
                    ->where('action', $action)
                    ->whereNotNull('coordinates_x')
                    ->whereNotNull('coordinates_y')
                    ->select('coordinates_x', 'coordinates_y')
                    ->get()
                    ->groupBy(function($item) {
                        return $item->coordinates_x . ',' . $item->coordinates_y;
                    })
                    ->map(function($group, $coordinates) {
                        [$x, $y] = explode(',', $coordinates);
                        return [
                            'x' => (int)$x,
                            'y' => (int)$y,
                            'count' => $group->count()
                        ];
                    })
                    ->values();
    }

    /**
     * Get scroll depth analytics
     */
    public static function scrollAnalytics($url = null)
    {
        $query = static::where('action', 'scroll')
                      ->whereNotNull('scroll_depth');
                      
        if ($url) {
            $query->where('page_url', $url);
        }

        return $query->get()
                    ->groupBy(function($item) {
                        // Group by scroll depth ranges
                        $depth = $item->scroll_depth;
                        if ($depth < 25) return '0-25%';
                        if ($depth < 50) return '25-50%';
                        if ($depth < 75) return '50-75%';
                        if ($depth < 100) return '75-100%';
                        return '100%';
                    })
                    ->map(function($group, $range) {
                        return [
                            'range' => $range,
                            'count' => $group->count()
                        ];
                    })
                    ->values();
    }

    /**
     * Get activity statistics
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'today' => static::today()->count(),
            'clicks' => static::where('action', 'click')->count(),
            'scrolls' => static::where('action', 'scroll')->count(),
            'form_submits' => static::where('action', 'form_submit')->count(),
            'downloads' => static::where('action', 'download')->count(),
        ];
    }
}
