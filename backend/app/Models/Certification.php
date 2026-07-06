<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Certification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'certifications';

    protected $fillable = [
        'name',
        'authority_logo',
        'certificate_file',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Ensure computed attributes are included in JSON responses
    protected $appends = [
        'authority_logo_url',
        'certificate_url',
    ];

    public function getAuthorityLogoUrlAttribute()
    {
        return $this->authority_logo ? asset('storage/' . $this->authority_logo) : null;
    }

    public function getCertificateUrlAttribute()
    {
        return $this->certificate_file ? asset('storage/' . $this->certificate_file) : null;
    }
}
