<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_key',
        'config_value',
        'service_type',
        'network',
        'provider_type',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get configuration value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $config = self::where('config_key', $key)
            ->where('is_active', true)
            ->first();

        return $config ? $config->config_value : $default;
    }

    /**
     * Get configuration for specific service, network, and provider type
     */
    public static function getServiceConfig(string $serviceType, string $network = null, string $providerType = null, string $key = null)
    {
        $query = self::where('service_type', $serviceType)
            ->where('is_active', true);

        if ($network) {
            $query->where(function($q) use ($network) {
                $q->where('network', $network)->orWhereNull('network');
            });
        }

        if ($providerType) {
            $query->where(function($q) use ($providerType) {
                $q->where('provider_type', $providerType)->orWhereNull('provider_type');
            });
        }

        if ($key) {
            $query->where('config_key', 'like', "%{$key}%");
        }

        return $query->get();
    }

    /**
     * Set or update configuration value
     */
    public static function setValue(string $key, $value, array $attributes = [])
    {
        return self::updateOrCreate(
            ['config_key' => $key],
            array_merge([
                'config_value' => $value,
                'is_active' => true
            ], $attributes)
        );
    }
}
