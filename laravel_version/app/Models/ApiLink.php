<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLink extends Model
{
    use HasFactory;

    protected $table = 'apilinks';

    protected $primaryKey = 'aId';

    protected $fillable = [
        'name',
        'type',
        'value',
        'is_active',
        'priority',
        'auth_type',
        'auth_params',
        'success_rate',
        'response_time',
        'last_checked',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auth_params' => 'json',
        'priority' => 'integer',
        'success_rate' => 'decimal:2',
        'response_time' => 'integer',
        'last_checked' => 'datetime',
    ];

    /**
     * Get providers by service type
     */
    public static function getProvidersByType(string $type)
    {
        return self::where('type', $type)
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('success_rate', 'desc')
            ->get();
    }

    /**
     * Get the best performing provider for a service type
     */
    public static function getBestProvider(string $type)
    {
        return self::where('type', $type)
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('success_rate', 'desc')
            ->first();
    }

    /**
     * Scope to get active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get providers by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to order by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority')->orderBy('success_rate', 'desc');
    }
}
