<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'about_us';

    protected $fillable = [
        // Who We Are Section
        'who_we_are_title',
        'who_we_are_subtitle',
        'who_we_are_content',
        'who_we_are_image',
        'who_we_are_video_url',
        'who_we_are_video_file',
        'who_we_are_features',
        
        // Mission & Vision Section
        'mission_vision_title',
        'mission_title',
        'mission_content',
        'mission_image',
        'vision_title',
        'vision_content',
        'vision_image',
        
        // Additional customizable sections
        'custom_sections',
        
        // SEO Fields
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'schema_markup',
        
        // Settings
        'is_published',
        'sort_order',
        'updated_by'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get Who We Are image URL
     */
    public function getWhoWeAreImageUrlAttribute()
    {
        return $this->who_we_are_image ? asset('storage/' . $this->who_we_are_image) : null;
    }

    /**
     * Get Mission image URL
     */
    public function getMissionImageUrlAttribute()
    {
        return $this->mission_image ? asset('storage/' . $this->mission_image) : null;
    }

    /**
     * Get Vision image URL
     */
    public function getVisionImageUrlAttribute()
    {
        return $this->vision_image ? asset('storage/' . $this->vision_image) : null;
    }

    /**
     * Get Who We Are video URL
     */
    public function getWhoWeAreVideoUrlFullAttribute()
    {
        return $this->who_we_are_video_file ? asset('storage/' . $this->who_we_are_video_file) : null;
    }

    /**
     * Get OG image URL
     */
    public function getOgImageUrlAttribute()
    {
        return $this->og_image ? asset('storage/' . $this->og_image) : null;
    }

    /**
     * Get the active About Us record (singleton pattern)
     */
    public static function getInstance()
    {
        $aboutUs = static::first();
        
        if (!$aboutUs) {
            $aboutUs = static::create([
                // Who We Are defaults
                'who_we_are_title' => 'Who We Are',
                'who_we_are_subtitle' => 'Discover Our Story',
                'who_we_are_content' => '<p>We are a dynamic and innovative company dedicated to delivering exceptional solutions that make a difference in our customers\' lives.</p><p>Our journey began with a simple vision: to create meaningful impact through technology and innovation.</p>',
                'who_we_are_features' => [
                    ['icon' => 'fas fa-lightbulb', 'title' => 'Innovation', 'description' => 'We embrace cutting-edge technology and creative solutions.'],
                    ['icon' => 'fas fa-users', 'title' => 'Team Excellence', 'description' => 'Our diverse team brings expertise and passion to every project.'],
                    ['icon' => 'fas fa-award', 'title' => 'Quality Focus', 'description' => 'We maintain the highest standards in everything we deliver.']
                ],
                
                // Mission & Vision defaults
                'mission_vision_title' => 'Our Mission & Vision',
                'mission_title' => 'Our Mission',
                'mission_content' => '<p>To empower businesses and individuals through innovative technology solutions that drive growth, efficiency, and success.</p><p>We are committed to delivering exceptional value while maintaining the highest standards of integrity and customer service.</p>',
                'vision_title' => 'Our Vision',
                'vision_content' => '<p>To be the leading force in technological innovation, creating a future where technology seamlessly integrates with human potential.</p><p>We envision a world where our solutions contribute to sustainable growth and positive change.</p>',
                
                // SEO defaults
                'meta_title' => 'About Us - Learn About Our Company',
                'meta_description' => 'Discover who we are, our mission, vision, and what drives us to deliver exceptional solutions. Learn about our journey and commitment to excellence.',
                'meta_keywords' => 'about us, company profile, mission, vision, team, innovation, technology solutions',
                'og_title' => 'About Us - Our Story & Mission',
                'og_description' => 'Learn about our company, our mission to innovate, and our vision for the future. Discover what makes us unique.',
                
                'is_published' => true,
                'sort_order' => 1
            ]);
        }

        return $aboutUs;
    }

    /**
     * Get published About Us
     */
    public static function getPublished()
    {
        return static::where('is_published', true)->first() ?: static::getInstance();
    }

    /**
     * Mutator for Who We Are features
     */
    public function setWhoWeAreFeaturesAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true) ?: [];
        }
        $this->attributes['who_we_are_features'] = $value;
    }

    /**
     * Mutator for custom sections
     */
    public function setCustomSectionsAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true) ?: [];
        }
        $this->attributes['custom_sections'] = $value;
    }

    /**
     * Get feature count
     */
    public function getFeatureCountAttribute()
    {
        return count($this->who_we_are_features ?? []);
    }

    /**
     * Get custom section count
     */
    public function getCustomSectionCountAttribute()
    {
        return count($this->custom_sections ?? []);
    }

    /**
     * Check if has video content
     */
    public function hasVideoAttribute()
    {
        return !empty($this->who_we_are_video_url) || !empty($this->who_we_are_video_file);
    }

    /**
     * Get video source (URL or file)
     */
    public function getVideoSourceAttribute()
    {
        if ($this->who_we_are_video_file) {
            return $this->who_we_are_video_url_full;
        }
        return $this->who_we_are_video_url;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-generate meta title if empty
            if (empty($model->meta_title)) {
                $model->meta_title = $model->who_we_are_title . ' - About Us';
            }
            
            // Auto-generate meta description if empty
            if (empty($model->meta_description)) {
                $content = strip_tags($model->who_we_are_content);
                $model->meta_description = Str::limit($content, 155);
            }
        });
    }
}