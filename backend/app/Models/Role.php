<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'page_access',
        'status',
    ];

    protected $casts = [
        'permissions' => 'array',
        'page_access' => 'array',
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function hasPageAccess($page)
    {
        return in_array($page, $this->page_access ?? []);
    }

    public function isActive()
    {
        return $this->status === true;
    }
}