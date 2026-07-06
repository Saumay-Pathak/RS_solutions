<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class SalesRequirementQuery extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sales_requirement_queries';

    protected $fillable = [
        // Contact
        'name',
        'email',
        'phone_country_code',
        'phone',

        // Location
        'city',
        'state',
        'country',
        'zip',

        // Requirement details
        'product',
        'requirement_type', // e.g., Face + Fingerprint Device, 4G WiFi Router, etc.
        'source', // e.g., General, Social Media Ad, Others
        'message',

        // Meta & tracking
        'ip_address',
        'user_agent',
        'page_url',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',

        // Admin workflow
        'status', // new, read, contacted, closed
        'priority', // low, medium, high
        'assigned_to',
        'notes',
        'contacted_at',
        'closed_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'closed_at' => 'datetime',
        'notes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'new',
        'priority' => 'medium',
    ];

    /**
     * Basic scopes
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Statistics for admin dashboard
     */
    public static function getStats(): array
    {
        return [
            'total' => static::count(),
            'new' => static::where('status', 'new')->count(),
            'contacted' => static::where('status', 'contacted')->count(),
            'closed' => static::where('status', 'closed')->count(),
            'today' => static::whereDate('created_at', Carbon::today())->count(),
            'this_month' => static::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
    }
}