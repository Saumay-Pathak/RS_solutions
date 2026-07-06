<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Software extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'software';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'one_line_description',
        'main_category',
        'sub_category',
        'file',
        'external_url',
        'version',
        'size',
        'requirements',
        'platforms',
        'tags',
        'developer',
        'license',
        'price',
        'is_free',
        'download_count',
        'status',
        'featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'released_at'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'status' => 'boolean',
        'featured' => 'boolean',
        'download_count' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'released_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => true,
        'featured' => false,
        'is_free' => true,
        'download_count' => 0,
        'sort_order' => 0,
        'requirements' => [],
        'platforms' => [],
        'tags' => []
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

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->where('main_category', $category);
        }
        return $query;
    }

    public function scopeBySubCategory($query, $subCategory)
    {
        if ($subCategory) {
            return $query->where('sub_category', $subCategory);
        }
        return $query;
    }

    // Accessors
    public function getDownloadUrlAttribute()
    {
        if ($this->external_url) {
            return $this->external_url;
        }
        
        if ($this->file && Storage::exists($this->file)) {
            return Storage::url($this->file);
        }
        
        return null;
    }

    public function getFileTypeAttribute()
    {
        if ($this->external_url) {
            return 'external';
        }
        
        if ($this->file) {
            return 'file';
        }
        
        return 'none';
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->size) {
            return 'Unknown';
        }
        
        $bytes = floatval($this->size);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getExcerptAttribute($length = 150)
    {
        return \Str::limit(strip_tags($this->one_line_description ?: $this->description), $length);
    }

    public function excerpt($length = 150)
    {
        return \Str::limit(strip_tags($this->one_line_description ?: $this->description), $length);
    }

    public function getUrlAttribute()
    {
        return route('software.show', $this->slug ?: $this->id);
    }

    public function getPlatformsListAttribute()
    {
        return is_array($this->platforms) ? $this->platforms : [];
    }

    public function getTagsListAttribute()
    {
        return is_array($this->tags) ? $this->tags : [];
    }

    public function getRequirementsListAttribute()
    {
        return is_array($this->requirements) ? $this->requirements : [];
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

    public function setRequirementsAttribute($value)
    {
        $this->attributes['requirements'] = is_array($value) ? array_filter($value) : [];
    }

    public function setPlatformsAttribute($value)
    {
        $this->attributes['platforms'] = is_array($value) ? array_filter($value) : [];
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = is_array($value) ? array_filter($value) : [];
    }

    // Helper methods
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function hasDownload()
    {
        return !empty($this->file) || !empty($this->external_url);
    }

    public function getFullCategoryAttribute()
    {
        $category = $this->main_category;
        if ($this->sub_category) {
            $category .= ' > ' . $this->sub_category;
        }
        return $category;
    }
}
