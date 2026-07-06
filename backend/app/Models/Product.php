<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'a_plus_content',
        'features',
        'specifications',
        'faqs',
        'category_id',
        'images',
        'datasheet_document',
        'connection_diagram_document',
        'user_manual_document',
        'catalogue_document',
        'status',
        'featured',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'specifications' => 'array',
        'faqs' => 'array',
        'images' => 'array',
        'status' => 'boolean',
        'featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'datasheet_url',
        'connection_diagram_url',
        'user_manual_url',
        'catalogue_url',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function isActive()
    {
        return $this->status === true;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function addFeature($feature)
    {
        $features = $this->features ?? [];
        $features[] = $feature;
        $this->features = $features;
        return $this;
    }

    public function removeFeature($index)
    {
        $features = $this->features ?? [];
        if (isset($features[$index])) {
            unset($features[$index]);
            $this->features = array_values($features);
        }
        return $this;
    }

    public function addSpecification($title, $value)
    {
        $specifications = $this->specifications ?? [];
        $specifications[] = [
            'title' => $title,
            'value' => $value,
        ];
        $this->specifications = $specifications;
        return $this;
    }

    public function removeSpecification($index)
    {
        $specifications = $this->specifications ?? [];
        if (isset($specifications[$index])) {
            unset($specifications[$index]);
            $this->specifications = array_values($specifications);
        }
        return $this;
    }

    public function addImage($image)
    {
        $images = $this->images ?? [];
        $images[] = $image;
        $this->images = $images;
        return $this;
    }

    public function removeImage($index)
    {
        $images = $this->images ?? [];
        if (isset($images[$index])) {
            unset($images[$index]);
            $this->images = array_values($images);
        }
        return $this;
    }

    public function addFaq($question, $answer)
    {
        $faqs = $this->faqs ?? [];
        $faqs[] = [
            'question' => $question,
            'answer' => $answer,
        ];
        $this->faqs = $faqs;
        return $this;
    }

    public function removeFaq($index)
    {
        $faqs = $this->faqs ?? [];
        if (isset($faqs[$index])) {
            unset($faqs[$index]);
            $this->faqs = array_values($faqs);
        }
        return $this;
    }

    public function getDatasheetUrlAttribute()
    {
        return $this->datasheet_document ? Storage::url($this->datasheet_document) : null;
    }

    public function getConnectionDiagramUrlAttribute()
    {
        return $this->connection_diagram_document ? Storage::url($this->connection_diagram_document) : null;
    }

    public function getUserManualUrlAttribute()
    {
        return $this->user_manual_document ? Storage::url($this->user_manual_document) : null;
    }

    public function getCatalogueUrlAttribute()
    {
        return $this->catalogue_document ? Storage::url($this->catalogue_document) : null;
    }
}

