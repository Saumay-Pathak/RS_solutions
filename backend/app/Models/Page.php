<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Page extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'template',
        'sections',
        'status',
        'meta_title',
        'meta_description',
        'custom_css',
        'custom_js',
        'sort_order',
    ];

    protected $casts = [
        'sections' => 'array',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function isActive()
    {
        return $this->status === true;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function addSection($section)
    {
        $sections = $this->sections ?? [];
        $sections[] = $section;
        $this->sections = $sections;
        return $this;
    }

    public function removeSection($index)
    {
        $sections = $this->sections ?? [];
        if (isset($sections[$index])) {
            unset($sections[$index]);
            $this->sections = array_values($sections);
        }
        return $this;
    }

    public function updateSection($index, $section)
    {
        $sections = $this->sections ?? [];
        if (isset($sections[$index])) {
            $sections[$index] = $section;
            $this->sections = $sections;
        }
        return $this;
    }

    public static function getSystemPages()
    {
        return [
            'home' => 'Home Page',
            'about' => 'About Us',
            'solutions' => 'Solutions',
            'support' => 'Support',
            'software' => 'Software',
            'contact' => 'Contact Us',
        ];
    }
}