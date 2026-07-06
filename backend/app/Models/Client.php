<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Client extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'clients';

    protected $fillable = [
        'name',
        'logo',
        'featured',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Ensure computed attributes are included in JSON responses
    protected $appends = [
        'logo_url',
    ];

    // Accessor for full logo URL
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
