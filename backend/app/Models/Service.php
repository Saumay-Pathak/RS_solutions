<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'services';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'image',
        'status',
        'hide_from_homepage',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'status' => 'boolean',
        'hide_from_homepage' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => true,
        'hide_from_homepage' => false,
        'sort_order' => 0
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        return asset('assets/img/services/default.jpg');
    }

    public function getExcerptAttribute($length = 150)
    {
        return \Str::limit(strip_tags($this->short_description ?: $this->description), $length);
    }

    // Helpers
    public function excerpt($length = 150)
    {
        return \Str::limit(strip_tags($this->short_description ?: $this->description), $length);
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
}
