<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HeaderFooter extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'header_footer_settings';

    protected $fillable = [
        // SEO Settings
        'site_title',
        'site_tagline',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'og_url',
        'twitter_card',
        'twitter_site',
        'twitter_creator',
        'canonical_url',
        'robots_meta',
        'schema_markup',
        
        // Logo & Branding
        'logo',
        'footer_logo',
        'favicon',
        'apple_touch_icon',
        
        // Footer Information
        'footer_description',
        'footer_email',
        'footer_phone',
        'footer_address',
        'footer_copyright',
        
        // Legal/Policy Pages
        'privacy_policy',
        'terms_of_service',
        'cookie_policy',
        'disclaimer',

        // Social Media Links
        'social_facebook',
        'social_twitter',
        'social_linkedin',
        'social_instagram',
        'social_youtube',
        'social_github',
        
        // Analytics & Tracking
        'google_analytics_id',
        'google_tag_manager_id',
        'facebook_pixel_id',
        'google_search_console',
        
        // Header/Footer Scripts
        'header_scripts',
        'footer_scripts',
        'custom_css',
        'custom_js',

        // Counters
        'counters',
        
        // Navigation Settings
        'show_search_in_header',
        'show_language_switcher',
        'show_dark_mode_toggle',
        'header_style',
        'footer_style',
        
        // Contact Settings
        'contact_form_email',
        'notification_email',
        
        // Additional Settings
        'timezone',
        'date_format',
        'time_format',
        'updated_by',

        // App Links
        'smart_app_link',
        'attendance_app_link'
    ];

    protected $casts = [
        'show_search_in_header' => 'boolean',
        'show_language_switcher' => 'boolean',
        'show_dark_mode_toggle' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'counters' => 'array'
    ];

    protected $attributes = [
        'show_search_in_header' => true,
        'show_language_switcher' => false,
        'show_dark_mode_toggle' => true,
        'og_type' => 'website',
        'twitter_card' => 'summary_large_image',
        'robots_meta' => 'index, follow',
        'timezone' => 'UTC',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
        'header_style' => 'default',
        'footer_style' => 'default',
        // Default counters
        'counters' => [
            'clients' => [
                'label' => 'Current Clients',
                'value' => '10+',
            ],
            'experience' => [
                'label' => 'Years Of Experience',
                'value' => '35+',
            ],
            'awards' => [
                'label' => 'Awards Winning',
                'value' => '10+',
            ],
            'solutions' => [
                'label' => 'Our Solutions',
                'value' => '0+',
            ],
        ],
    ];

    /**
     * Get the singleton HeaderFooter instance
     */
    public static function getInstance()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'site_title' => 'RealTime Biometrics',
                'site_tagline' => 'Advanced Biometric Solutions',
                'meta_description' => 'Leading provider of biometric solutions, access control systems, and security technology.',
                'meta_keywords' => 'biometrics, access control, fingerprint scanner, face recognition, security systems',
                'og_title' => 'RealTime Biometrics - Advanced Biometric Solutions',
                'og_description' => 'Leading provider of biometric solutions, access control systems, and security technology.',
                'twitter_site' => '@realtimebiometrics',
                'footer_description' => 'RealTime Biometrics is a leading provider of cutting-edge biometric solutions, specializing in access control systems, fingerprint scanners, and advanced security technology.',
                'footer_email' => 'info@realtimebiometrics.com',
                'footer_phone' => '+91-8743 8743 34',
                'footer_address' => 'C-83, Ganesh Nagar Pandav Nagar complex, Delhi, India 110092',
                'footer_copyright' => '© 2026 RS Solutions. All rights reserved.',
                'social_facebook' => 'https://facebook.com/realtimebiometrics',
                'social_linkedin' => 'https://linkedin.com/company/realtimebiometrics',
                'social_twitter' => 'https://twitter.com/realtimebiometrics',
                'social_instagram' => 'https://instagram.com/realtimebiometrics',
                'contact_form_email' => 'info@realtimebiometrics.com',
                'notification_email' => 'admin@realtimebiometrics.com',
            ]);
        }

        return $settings;
    }

    /**
     * Get cached settings
     */
    public static function getCached()
    {
        return Cache::remember('header_footer_settings', 3600, function () {
            return self::getInstance();
        });
    }

    /**
     * Get specific setting value
     */
    public static function getValue($key, $default = null)
    {
        $settings = self::getCached();
        return $settings->$key ?? $default;
    }

    /**
     * Update setting value
     */
    public static function setValue($key, $value)
    {
        $settings = self::getInstance();
        $settings->$key = $value;
        $settings->updated_by = auth()->user()->_id ?? null;
        $settings->save();
        
        // Clear cache
        Cache::forget('header_footer_settings');
        
        return $settings;
    }

    /**
     * Update multiple settings
     */
    public static function updateMultiple(array $values)
    {
        $settings = self::getInstance();
        
        foreach ($values as $key => $value) {
            if (in_array($key, $settings->getFillable())) {
                $settings->$key = $value;
            }
        }
        
        $settings->updated_by = auth()->user()->_id ?? null;
        $settings->save();
        
        // Clear cache
        Cache::forget('header_footer_settings');
        
        return $settings;
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('assets/img/logo.png');
    }

    /**
     * Get footer logo URL
     */
    public function getFooterLogoUrlAttribute()
    {
        return $this->footer_logo ? asset('storage/' . $this->footer_logo) : $this->logo_url;
    }

    /**
     * Get favicon URL
     */
    public function getFaviconUrlAttribute()
    {
        return $this->favicon ? asset('storage/' . $this->favicon) : asset('favicon.ico');
    }

    /**
     * Get OG image URL
     */
    public function getOgImageUrlAttribute()
    {
        return $this->og_image ? asset('storage/' . $this->og_image) : $this->logo_url;
    }

    /**
     * Get Apple touch icon URL
     */
    public function getAppleTouchIconUrlAttribute()
    {
        return $this->apple_touch_icon ? asset('storage/' . $this->apple_touch_icon) : $this->favicon_url;
    }

    /**
     * Clear cache when updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget('header_footer_settings');
        });

        static::deleted(function ($model) {
            Cache::forget('header_footer_settings');
        });
    }
}