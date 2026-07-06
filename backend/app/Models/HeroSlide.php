<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class HeroSlide extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'hero_slides';

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'content_file',
        'image',
        'image_alt',
        'button_text',
        'button_link',
        'button_style',
        'secondary_button_text',
        'secondary_button_link',
        'secondary_button_style',
        'order',
        'is_active',
        'background_color',
        'text_color',
        'overlay_opacity',
        'content_position',
        'animation_type',
        'auto_play_delay',
        'display_from',
        'display_to',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'overlay_opacity' => 'integer',
        'auto_play_delay' => 'integer',
        'display_from' => 'datetime',
        'display_to' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get active slides ordered by order field
     */
    public static function getActiveSlides()
    {
        return self::where('is_active', true)
            ->where(function ($query) {
                $query->where('display_from', '<=', now())
                    ->orWhereNull('display_from');
            })
            ->where(function ($query) {
                $query->where('display_to', '>=', now())
                    ->orWhereNull('display_to');
            })
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * Get next order number
     */
    public static function getNextOrder()
    {
        $maxOrder = self::max('order');
        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return asset('assets/img/elements/1.jpg'); // Default placeholder
    }

    /**
     * Check if slide is currently displayable
     */
    public function isDisplayable()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->display_from && $now->lt($this->display_from)) {
            return false;
        }

        if ($this->display_to && $now->gt($this->display_to)) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active slides
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered slides
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get HTML content, preferring uploaded file if present
     */
    public function getContentHtmlAttribute()
    {
        if ($this->content_file && \Storage::disk('public')->exists($this->content_file)) {
            return \Storage::disk('public')->get($this->content_file);
        }
        return $this->content ?? '';
    }
}
