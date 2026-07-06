<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Solution extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'solutions';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'image',
        'features',
        'benefits',
        'technologies',
        'category',
        'price_range',
        'delivery_time',
        'status',
        'featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'status' => 'boolean',
        'featured' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => true,
        'featured' => false,
        'sort_order' => 0,
        'features' => [],
        'benefits' => [],
        'technologies' => []
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }
        return asset('assets/img/solutions/default.jpg');
    }

    public function getExcerptAttribute($length = 150)
    {
        return \Str::limit(strip_tags($this->short_description ?: $this->description), $length);
    }

    public function excerpt($length = 150)
    {
        return \Str::limit(strip_tags($this->short_description ?: $this->description), $length);
    }

    public function getReadTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->description));
        $minutes = max(1, ceil($wordCount / 200));
        return $minutes . ' min read';
    }

    public function getUrlAttribute()
    {
        return route('solutions.show', $this->slug ?: $this->id);
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = \Str::slug($value);
        }
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = \Str::slug($value);
    }

    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = is_array($value) ? array_filter($value) : [];
    }

    public function setBenefitsAttribute($value)
    {
        $this->attributes['benefits'] = is_array($value) ? array_filter($value) : [];
    }

    public function setTechnologiesAttribute($value)
    {
        $this->attributes['technologies'] = is_array($value) ? array_filter($value) : [];
    }

    // Helper methods
    public function getFeaturesListAttribute()
    {
        return is_array($this->features) ? $this->features : [];
    }

    public function getBenefitsListAttribute()
    {
        return is_array($this->benefits) ? $this->benefits : [];
    }

    public function getTechnologiesListAttribute()
    {
        return is_array($this->technologies) ? $this->technologies : [];
    }
}
