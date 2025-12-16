<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $table = 'configurations';
    protected $primaryKey = 'cId';
    public $timestamps = false;

    protected $fillable = [
        'config_key',
        'config_value',
    ];

    /**
     * Scope for getting configuration by key
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('config_key', $key);
    }

    /**
     * Get configuration value by key
     */
    public static function getValue($key, $default = null)
    {
        $config = static::where('config_key', $key)->first();
        return $config ? $config->config_value : $default;
    }

    /**
     * Set configuration value
     */
    public static function setValue($key, $value)
    {
        return static::updateOrCreate(
            ['config_key' => $key],
            ['config_value' => $value]
        );
    }

    /**
     * Get multiple configuration values
     */
    public static function getValues(array $keys, $default = null)
    {
        $configs = static::whereIn('config_key', $keys)->get()->keyBy('config_key');
        
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = isset($configs[$key]) ? $configs[$key]->config_value : $default;
        }
        
        return $result;
    }

    /**
     * Set multiple configuration values
     */
    public static function setValues(array $configs)
    {
        foreach ($configs as $key => $value) {
            static::setValue($key, $value);
        }
    }

    /**
     * Delete configuration by key
     */
    public static function deleteKey($key)
    {
        return static::where('config_key', $key)->delete();
    }

    /**
     * Check if configuration key exists
     */
    public static function keyExists($key)
    {
        return static::where('config_key', $key)->exists();
    }

    /**
     * Get all configurations as key-value pairs
     */
    public static function getAllConfigs()
    {
        return static::all()->pluck('config_value', 'config_key')->toArray();
    }

    /**
     * Get configurations matching a pattern
     */
    public static function getConfigsByPattern($pattern)
    {
        return static::where('config_key', 'LIKE', $pattern)->get()->keyBy('config_key');
    }

    /**
     * Bulk update configurations
     */
    public static function bulkUpdate(array $configs)
    {
        foreach ($configs as $key => $value) {
            static::updateOrCreate(
                ['config_key' => $key],
                ['config_value' => $value]
            );
        }
    }
}