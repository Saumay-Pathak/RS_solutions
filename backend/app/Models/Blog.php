<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Blog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'author_id',
        'category',
        'tags',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'reading_time',
    ];

    protected $casts = [
        'tags' => 'array',
        'status' => 'boolean',
        'published_at' => 'datetime',
        'reading_time' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isPublished()
    {
        return $this->status === true && $this->published_at <= now();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('status', true)
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', false);
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
        }
        return $this;
    }

    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $this->tags = array_values(array_filter($tags, function($t) use ($tag) {
            return $t !== $tag;
        }));
        return $this;
    }

    public function calculateReadingTime()
    {
        $words = str_word_count(strip_tags($this->content));
        $this->reading_time = ceil($words / 200); // Assuming 200 words per minute
        return $this;
    }
}