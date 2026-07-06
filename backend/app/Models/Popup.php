<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

class Popup extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'popups';

    protected $fillable = [
        'title',
        'type',
        'content',
        'image',
        'video_url',
        'button_text',
        'button_url',
        'position',
        'size',
        'show_after', // seconds
        'show_on_pages',
        'show_frequency', // once, daily, always
        'target_users', // all, new, returning
        'is_active',
        'start_date',
        'end_date',
        'priority',
        'styles'
    ];

    protected $casts = [
        'show_on_pages' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'show_after' => 'integer',
        'priority' => 'integer',
        'styles' => 'array'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    /**
     * Scope for active popups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popups that should be shown now
     */
    public function scopeShouldShow($query)
    {
        $now = now();
        
        return $query->active()
                    ->where(function ($q) use ($now) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Scope for popups by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get popups for specific page
     */
    public function scopeForPage($query, $page)
    {
        return $query->where(function ($q) use ($page) {
            $q->whereNull('show_on_pages')
              ->orWhereIn('show_on_pages', ['all', $page])
              ->orWhere('show_on_pages', 'like', '%' . $page . '%');
        });
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Get popup types
     */
    public static function getTypes()
    {
        return [
            'modal' => 'Modal Popup',
            'banner' => 'Banner',
            'slide_in' => 'Slide In',
            'fullscreen' => 'Fullscreen',
            'video' => 'Video Popup',
            'newsletter' => 'Newsletter Signup',
            'promotion' => 'Promotion',
            'announcement' => 'Announcement'
        ];
    }

    /**
     * Get positions
     */
    public static function getPositions()
    {
        return [
            'center' => 'Center',
            'top' => 'Top',
            'bottom' => 'Bottom',
            'left' => 'Left',
            'right' => 'Right',
            'top-left' => 'Top Left',
            'top-right' => 'Top Right',
            'bottom-left' => 'Bottom Left',
            'bottom-right' => 'Bottom Right'
        ];
    }

    /**
     * Get sizes
     */
    public static function getSizes()
    {
        return [
            'small' => 'Small (400px)',
            'medium' => 'Medium (600px)',
            'large' => 'Large (800px)',
            'extra-large' => 'Extra Large (1000px)',
            'full-width' => 'Full Width',
            'auto' => 'Auto'
        ];
    }

    /**
     * Get show frequency options
     */
    public static function getFrequencies()
    {
        return [
            'always' => 'Always Show',
            'once' => 'Once Per Session',
            'daily' => 'Once Per Day',
            'weekly' => 'Once Per Week'
        ];
    }

    /**
     * Get target user options
     */
    public static function getTargetUsers()
    {
        return [
            'all' => 'All Users',
            'new' => 'New Users Only',
            'returning' => 'Returning Users Only',
            'logged_in' => 'Logged In Users',
            'guests' => 'Guest Users'
        ];
    }

    /**
     * Check if popup should be shown to user
     */
    public function shouldShowToUser($user = null, $request = null)
    {
        // Check if popups are globally enabled
        if (!SiteSetting::getValue('popups_enabled', true)) {
            return false;
        }

        // Check if popup is active and within date range
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }
        
        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        // Check target users
        switch ($this->target_users) {
            case 'new':
                // Logic for new users (could check session, cookies, etc.)
                break;
            case 'returning':
                // Logic for returning users
                break;
            case 'logged_in':
                if (!$user) {
                    return false;
                }
                break;
            case 'guests':
                if ($user) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Get CSS styles as string
     */
    public function getCssStylesAttribute()
    {
        if (!$this->styles) {
            return '';
        }

        $css = [];
        foreach ($this->styles as $property => $value) {
            if ($value) {
                $css[] = $property . ': ' . $value;
            }
        }

        return implode('; ', $css);
    }
}