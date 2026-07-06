<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IntegrationModule extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'integration_modules';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'download_file',
        'key_features',
        'api_features',
        'api_documentations',
        'production_base_url',
        'staging_base_url',
        'demo_credentials',
        'apis',
        'services_api',
        'services_other',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => true,
        'sort_order' => 0,
        'key_features' => [],
        'api_features' => [],
        'api_documentations' => [],
        'demo_credentials' => [],
        'apis' => [],
        'services_api' => [],
        'services_other' => [],
    ];

    // Ensure accessor attributes are included in JSON responses
    protected $appends = [
        'cover_image_url',
        'download_url',
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
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image && Storage::disk('public')->exists($this->cover_image)) {
            return Storage::disk('public')->url($this->cover_image);
        }
        return asset('assets/img/software/default-cover.jpg');
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->download_file && Storage::disk('public')->exists($this->download_file)) {
            return Storage::disk('public')->url($this->download_file);
        }
        return null;
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

    // Ensure robust handling for array-like attributes (Mongo returns arrays natively, SQL may store JSON strings)
    public function getKeyFeaturesAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }

    public function getApiFeaturesAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }

    public function getApiDocumentationsAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }

    public function getDemoCredentialsAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }

    public function getApisAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }

    public function getServicesApiAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }

    public function getServicesOtherAttribute($value)
    {
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }
}
