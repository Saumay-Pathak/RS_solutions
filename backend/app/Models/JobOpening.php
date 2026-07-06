<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class JobOpening extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'job_openings';

    protected $fillable = [
        'title',
        'location',
        'employment_type',
        'description',
        'is_active',
        'display_from',
        'display_to',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_from' => 'datetime',
        'display_to' => 'datetime',
        'order' => 'integer',
    ];

    public static function getNextOrder(): int
    {
        $max = self::max('order');
        return $max ? $max + 1 : 1;
    }
}