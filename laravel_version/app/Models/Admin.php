<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'sysusers';
    protected $primaryKey = 'sysId';
    public $timestamps = false; // No created_at/updated_at columns

    protected $fillable = [
        'sysName',
        'sysRole',
        'sysUsername',
        'sysToken',
        'sysStatus',
    ];

    protected $hidden = [
        'sysToken',
    ];

    /**
     * Override Laravel's default password field
     */
    public function getAuthPassword()
    {
        return $this->sysToken;
    }

    /**
     * Override Laravel's default username field
     */
    public function getAuthIdentifierName()
    {
        return 'sysUsername';
    }

    /**
     * Get the username for authentication.
     */
    public function getAuthIdentifier()
    {
        return $this->sysUsername;
    }

    /**
     * Role constants - matching existing system
     */
    const ROLE_SUPER_ADMIN = 1;
    const ROLE_ADMIN = 2;
    const ROLE_SUPPORT = 3;

    /**
     * Status constants - matching existing system
     */
    const STATUS_ACTIVE = 0;
    const STATUS_BLOCKED = 1;

    /**
     * Get role name
     */
    public function getRoleNameAttribute()
    {
        return match($this->sysRole) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_SUPPORT => 'Support',
            default => 'Unknown'
        };
    }

    /**
     * Get status name
     */
    public function getStatusNameAttribute()
    {
        return match($this->sysStatus) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_BLOCKED => 'Blocked',
            default => 'Unknown'
        };
    }

    /**
     * Check if admin is active
     */
    public function isActive()
    {
        return $this->sysStatus === self::STATUS_ACTIVE;
    }

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin()
    {
        return $this->sysRole === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Scope for active admins
     */
    public function scopeActive($query)
    {
        return $query->where('sysStatus', self::STATUS_ACTIVE);
    }
}