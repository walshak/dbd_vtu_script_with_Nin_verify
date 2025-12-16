<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiConfig extends Model
{
    use HasFactory;

    protected $table = 'api_configs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'value'
    ];

    /**
     * Get configuration value by name
     */
    public static function getValue($name, $default = null)
    {
        $config = static::where('name', $name)->first();
        return $config ? $config->value : $default;
    }

    /**
     * Set configuration value
     */
    public static function setValue($name, $value)
    {
        return static::updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        );
    }

    /**
     * Get API provider URL for service
     */
    public static function getProviderUrl($network, $service, $type = 'Provider')
    {
        $configName = strtolower($network) . ucfirst(strtolower($service)) . $type;
        return static::getValue($configName);
    }

    /**
     * Get API key for service
     */
    public static function getApiKey($network, $service, $type = 'Api')
    {
        $configName = strtolower($network) . ucfirst(strtolower($service)) . $type;
        return static::getValue($configName);
    }

    /**
     * Get all API configurations as array
     */
    public static function getAllConfigs()
    {
        return static::pluck('value', 'name')->toArray();
    }

    /**
     * Get payment gateway configurations
     */
    public static function getPaymentConfig($gateway = 'paystack')
    {
        $configs = [];
        $prefix = strtolower($gateway);

        $configs['public_key'] = static::getValue($prefix . '_public_key');
        $configs['secret_key'] = static::getValue($prefix . '_secret_key');
        $configs['webhook_url'] = static::getValue($prefix . '_webhook_url');
        $configs['base_url'] = static::getValue($prefix . '_base_url');

        return $configs;
    }

    /**
     * Get SMS configuration
     */
    public static function getSmsConfig()
    {
        return [
            'username' => static::getValue('sms_username'),
            'password' => static::getValue('sms_password'),
            'sender_id' => static::getValue('sms_sender_id'),
            'api_url' => static::getValue('sms_api_url')
        ];
    }

    /**
     * Get email configuration
     */
    public static function getEmailConfig()
    {
        return [
            'smtp_host' => static::getValue('smtp_host'),
            'smtp_port' => static::getValue('smtp_port'),
            'smtp_username' => static::getValue('smtp_username'),
            'smtp_password' => static::getValue('smtp_password'),
            'from_email' => static::getValue('from_email'),
            'from_name' => static::getValue('from_name')
        ];
    }
}
