<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceSyncStatus extends Model
{
    protected $table = 'service_sync_status';

    protected $fillable = [
        'service_type',
        'last_sync_at',
        'sync_status',
        'total_synced',
        'total_created',
        'total_updated',
        'total_errors',
        'last_error',
        'api_source'
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];

    /**
     * Record a successful sync
     */
    public static function recordSync(
        string $serviceType,
        int $totalSynced,
        int $created,
        int $updated,
        int $errors = 0,
        ?string $errorMessage = null
    ): self {
        return self::updateOrCreate(
            ['service_type' => $serviceType],
            [
                'last_sync_at' => now(),
                'sync_status' => $errors > 0 ? 'partial' : 'success',
                'total_synced' => $totalSynced,
                'total_created' => $created,
                'total_updated' => $updated,
                'total_errors' => $errors,
                'last_error' => $errorMessage,
                'api_source' => 'uzobest'
            ]
        );
    }

    /**
     * Record a failed sync
     */
    public static function recordFailure(string $serviceType, string $errorMessage): self
    {
        return self::updateOrCreate(
            ['service_type' => $serviceType],
            [
                'last_sync_at' => now(),
                'sync_status' => 'failed',
                'last_error' => $errorMessage,
                'api_source' => 'uzobest'
            ]
        );
    }

    /**
     * Get sync status for a service
     */
    public static function getStatus(string $serviceType): ?self
    {
        return self::where('service_type', $serviceType)->first();
    }

    /**
     * Check if service needs sync (never synced or older than 24 hours)
     */
    public function needsSync(): bool
    {
        if ($this->sync_status === 'never' || !$this->last_sync_at) {
            return true;
        }

        return $this->last_sync_at->diffInHours(now()) >= 24;
    }

    /**
     * Get human-readable time since last sync
     */
    public function getLastSyncHumanAttribute(): string
    {
        if (!$this->last_sync_at) {
            return 'Never synced';
        }

        return $this->last_sync_at->diffForHumans();
    }

    /**
     * Get sync status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->sync_status) {
            'success' => 'success',
            'partial' => 'warning',
            'failed' => 'danger',
            'never' => 'secondary',
            default => 'secondary'
        };
    }
}
