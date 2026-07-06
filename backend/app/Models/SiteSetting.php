<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'label',
        'description',
        'group',
        'order'
    ];

    protected $casts = [
        'value' => 'array',
        'order' => 'integer'
    ];

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value, $type = 'boolean', $label = null, $description = null, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'label' => $label ?: ucwords(str_replace('_', ' ', $key)),
                'description' => $description,
                'group' => $group
            ]
        );
    }

    /**
     * Get settings grouped by category
     */
    public static function getGroupedSettings()
    {
        return static::orderBy('group')->orderBy('order')->get()->groupBy('group');
    }

    /**
     * Get all settings as key-value pairs
     */
    public static function getAllSettings()
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Initialize default settings
     */
    public static function initializeDefaults()
    {
        $defaults = [
            // Site Features
            [
                'key' => 'user_registration',
                'value' => true,
                'type' => 'boolean',
                'label' => 'User Registration',
                'description' => 'Allow new users to register',
                'group' => 'features',
                'order' => 1
            ],
            [
                'key' => 'comments_enabled',
                'value' => true,
                'type' => 'boolean',
                'label' => 'Comments System',
                'description' => 'Enable comments on blog posts',
                'group' => 'features',
                'order' => 2
            ],
            [
                'key' => 'social_sharing',
                'value' => true,
                'type' => 'boolean',
                'label' => 'Social Sharing',
                'description' => 'Show social media sharing buttons',
                'group' => 'features',
                'order' => 3
            ],

            // Analytics
            [
                'key' => 'user_activity_tracking',
                'value' => true,
                'type' => 'boolean',
                'label' => 'User Activity Tracking',
                'description' => 'Track user activities and page visits',
                'group' => 'analytics',
                'order' => 1
            ],
            [
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'text',
                'label' => 'Google Analytics ID',
                'description' => 'Your Google Analytics tracking ID (GA4)',
                'group' => 'analytics',
                'order' => 2
            ],

            // Popups & Modals
            [
                'key' => 'popups_enabled',
                'value' => true,
                'type' => 'boolean',
                'label' => 'Enable Popups',
                'description' => 'Allow popups to be displayed on the website',
                'group' => 'popups',
                'order' => 1
            ],

            // Maintenance
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'type' => 'boolean',
                'label' => 'Maintenance Mode',
                'description' => 'Put site in maintenance mode',
                'group' => 'system',
                'order' => 1
            ],
            [
                'key' => 'custom_activity_tracker',
                'value' => false,
                'type' => 'boolean',
                'label' => 'Custom Activity Tracker',
                'description' => 'Enable custom activity tracking system',
                'group' => 'system',
                'order' => 2
            ],
            [
                'key' => 'cache_enabled',
                'value' => true,
                'type' => 'boolean',
                'label' => 'Cache System',
                'description' => 'Enable page and data caching',
                'group' => 'system',
                'order' => 3
            ]
        ];

        foreach ($defaults as $setting) {
            static::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-initialize defaults if table is empty
        static::created(function ($model) {
            if (static::count() === 1) {
                static::initializeDefaults();
            }
        });
    }
}