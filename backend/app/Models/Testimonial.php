<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'testimonials';

    protected $fillable = [
        'name',
        'position',
        'company', 
        'content',
        'image',
        'rating',
        'featured',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'rating' => 'integer',
        'featured' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Ensure accessor attributes are included in JSON responses
    protected $appends = [
        'image_url',
    ];

    protected $attributes = [
        'rating' => 5,
        'featured' => false,
        'status' => true,
        'sort_order' => 0
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

    // Accessors
    public function getImageUrlAttribute()
    {
        // Use the public disk for files stored via Storage::disk('public')
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }
        return asset('assets/img/avatars/default.png');
    }

    public function getStarsAttribute()
    {
        return str_repeat('⭐', $this->rating);
    }

    public function getExcerptAttribute($length = 100)
    {
        return \Str::limit(strip_tags($this->content), $length);
    }
}
