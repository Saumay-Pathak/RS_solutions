<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'permissions',
        'profile_image',
        'phone',
        'address',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
        'status' => 'boolean',
        'last_login' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission($permission)
    {
        if ($this->role && $this->role->permissions) {
            return in_array($permission, $this->role->permissions);
        }
        return false;
    }

    public function hasPageAccess($page)
    {
        if ($this->role && $this->role->page_access) {
            return in_array($page, $this->role->page_access);
        }
        return false;
    }

    public function isActive()
    {
        return $this->status === true;
    }

    /**
     * Set the password attribute with hashing
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
