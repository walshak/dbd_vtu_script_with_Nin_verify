<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_type',
        'fcm_token',
        'app_version',
        'os_version',
        'device_info',
        'preferences',
        'security_features',
        'security_score',
        'biometric_enabled',
        'biometric_type',
        'biometric_token',
        'biometric_setup_at',
        'status',
        'first_seen_at',
        'last_active_at',
        'last_security_check',
        'ip_address',
        'location'
    ];

    protected $casts = [
        'device_info' => 'array',
        'preferences' => 'array',
        'security_features' => 'array',
        'security_score' => 'integer',
        'biometric_enabled' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_active_at' => 'datetime',
        'biometric_setup_at' => 'datetime',
        'last_security_check' => 'datetime'
    ];

    protected $hidden = [
        'biometric_token'
    ];

    /**
     * Get the user that owns the device
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if device is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->last_active_at && 
               $this->last_active_at->greaterThan(now()->subDays(30));
    }

    /**
     * Check if device has biometric authentication
     */
    public function hasBiometric(): bool
    {
        return $this->biometric_enabled && !empty($this->biometric_token);
    }

    /**
     * Get device security level
     */
    public function getSecurityLevel(): string
    {
        $score = $this->security_score ?? 0;
        
        if ($score >= 80) return 'high';
        if ($score >= 60) return 'medium';
        if ($score >= 40) return 'low';
        return 'very_low';
    }

    /**
     * Check if device needs security check
     */
    public function needsSecurityCheck(): bool
    {
        return !$this->last_security_check || 
               $this->last_security_check->lessThan(now()->subDays(7));
    }

    /**
     * Update device activity
     */
    public function updateActivity(string $ipAddress = null): void
    {
        $this->update([
            'last_active_at' => now(),
            'ip_address' => $ipAddress ?? $this->ip_address
        ]);
    }

    /**
     * Revoke device access
     */
    public function revoke(): void
    {
        $this->update([
            'status' => 'revoked',
            'fcm_token' => null,
            'biometric_enabled' => false,
            'biometric_token' => null
        ]);

        // Revoke all tokens for this device if user is set
        if ($this->user_id) {
            $this->user->tokens()
                 ->where('name', 'mobile-app')
                 ->delete();
        }
    }

    /**
     * Get device platform
     */
    public function getPlatform(): string
    {
        return match($this->device_type) {
            'android' => 'Android',
            'ios' => 'iOS',
            'web' => 'Web',
            default => 'Unknown'
        };
    }

    /**
     * Scope for active devices
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('last_active_at', '>', now()->subDays(30));
    }

    /**
     * Scope for devices by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('device_type', $type);
    }

    /**
     * Scope for devices with biometric
     */
    public function scopeWithBiometric($query)
    {
        return $query->where('biometric_enabled', true)
                    ->whereNotNull('biometric_token');
    }
}