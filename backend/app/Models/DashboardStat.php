<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DashboardStat extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'dashboard_stats';

    protected $fillable = [
        'key',
        'value',
        'last_updated_at'
    ];

    protected $casts = [
        'value' => 'array',
        'last_updated_at' => 'datetime'
    ];
}
