<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CableId extends Model
{
    use HasFactory;

    protected $table = 'cable_ids';
    protected $primaryKey = 'cId';

    protected $fillable = [
        'cableid',
        'provider',
        'providerStatus'
    ];

    /**
     * Get cable plans for this provider
     */
    public function cablePlans()
    {
        return $this->hasMany(CablePlan::class, 'cableprovider', 'cId');
    }

    /**
     * Scope for active providers
     */
    public function scopeActive($query)
    {
        return $query->where('providerStatus', 'On');
    }

    /**
     * Check if provider is active
     */
    public function isActive()
    {
        return $this->providerStatus === 'On';
    }

    /**
     * Toggle provider status
     */
    public function toggleStatus()
    {
        $this->providerStatus = $this->providerStatus === 'On' ? 'Off' : 'On';
        return $this->save();
    }

    /**
     * Get active providers
     */
    public static function getActiveProviders()
    {
        return self::where('providerStatus', 'On')->get();
    }

    /**
     * Get provider by cable ID
     */
    public static function getProviderByCableId($cableId)
    {
        return self::where('cableid', $cableId)->first();
    }

    /**
     * Get provider logo path
     */
    public function getLogoPathAttribute()
    {
        $logos = [
            'dstv' => '/images/providers/dstv.png',
            'gotv' => '/images/providers/gotv.png',
            'startimes' => '/images/providers/startimes.png',
        ];

        $providerKey = strtolower(trim($this->provider));
        return $logos[$providerKey] ?? '/images/providers/default-cable.png';
    }

    /**
     * Get formatted provider name
     */
    public function getFormattedNameAttribute()
    {
        return strtoupper($this->provider);
    }
}
