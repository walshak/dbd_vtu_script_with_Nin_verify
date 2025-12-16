<?php

namespace App\Services;

use App\Models\ApiConfiguration;
use App\Models\ApiLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ConfigurationService
{
    private const CACHE_TTL = 3600; // 1 hour cache

    /**
     * Get configuration value similar to original PHP getConfigValue()
     */
    public function getConfigValue(string $key, $default = null)
    {
        $cacheKey = "config.{$key}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $default) {
            return ApiConfiguration::getValue($key, $default);
        });
    }

    /**
     * Get API details similar to original PHP getApiDetails()
     */
    public function getApiDetails(): Collection
    {
        return Cache::remember('api.details', self::CACHE_TTL, function () {
            return ApiConfiguration::where('is_active', true)->get();
        });
    }

    /**
     * Get API links for admin configuration dropdowns
     */
    public function getApiLinks(): Collection
    {
        return Cache::remember('api.links', self::CACHE_TTL, function () {
            return ApiLink::where('is_active', true)
                ->orderBy('type')
                ->orderBy('priority')
                ->get();
        });
    }

    /**
     * Get provider configuration for a specific service and network
     */
    public function getProviderConfig(string $serviceType, string $network, string $providerType = 'VTU'): array
    {
        $cacheKey = "provider.{$serviceType}.{$network}.{$providerType}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($serviceType, $network, $providerType) {
            // Get API key for this service/network/provider combination
            $apiKeyConfig = strtolower($network) . ucfirst($providerType) . 'Key';
            $apiKey = $this->getConfigValue($apiKeyConfig);

            // Get provider URL for this service/network/provider combination
            $providerConfig = strtolower($network) . ucfirst($providerType) . 'Provider';
            $providerUrl = $this->getConfigValue($providerConfig);

            // Get provider details from api_links table
            $provider = ApiLink::where('value', $providerUrl)
                ->where('type', $serviceType)
                ->where('is_active', true)
                ->first();

            return [
                'api_key' => $apiKey,
                'provider_url' => $providerUrl,
                'provider_name' => $provider?->name,
                'auth_type' => $provider?->auth_type ?? 'token',
                'auth_params' => $provider?->auth_params ?? [],
            ];
        });
    }

    /**
     * Get service-specific configuration (cable, electricity, etc.)
     */
    public function getServiceConfig(string $serviceType): array
    {
        $cacheKey = "service.{$serviceType}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($serviceType) {
            $config = [];

            switch ($serviceType) {
                case 'cable':
                    $config = [
                        'verification_api' => $this->getConfigValue('cableVerificationApi'),
                        'verification_provider' => $this->getConfigValue('cableVerificationProvider'),
                        'api_key' => $this->getConfigValue('cableApi'),
                        'provider' => $this->getConfigValue('cableProvider'),
                    ];
                    break;

                case 'electricity':
                    $config = [
                        'verification_api' => $this->getConfigValue('meterVerificationApi'),
                        'verification_provider' => $this->getConfigValue('meterVerificationProvider'),
                        'api_key' => $this->getConfigValue('meterApi'),
                        'provider' => $this->getConfigValue('meterProvider'),
                    ];
                    break;

                case 'exam':
                    $provider = ApiLink::getBestProvider('Exam');
                    if ($provider) {
                        $config = [
                            'api_key' => $this->getConfigValue('examApi'),
                            'provider' => $provider->value,
                            'auth_type' => $provider->auth_type ?? 'Basic',
                            'user_url' => $provider->auth_params['user_url'] ?? null,
                        ];
                    } else {
                        $config = [
                            'api_key' => $this->getConfigValue('examApi'),
                            'provider' => $this->getConfigValue('examProvider'),
                            'auth_type' => 'Basic',
                        ];
                    }
                    break;

                case 'recharge_pin':
                    $provider = ApiLink::getBestProvider('RechargePin');
                    if ($provider) {
                        $config = [
                            'api_key' => $this->getConfigValue('rechargePinApi'),
                            'provider' => $provider->value,
                            'auth_type' => $provider->auth_type ?? 'Basic',
                            'user_url' => $provider->auth_params['user_url'] ?? null,
                        ];
                    } else {
                        $config = [
                            'api_key' => $this->getConfigValue('rechargePinApi'),
                            'provider' => $this->getConfigValue('rechargePinProvider'),
                            'auth_type' => 'Basic',
                        ];
                    }
                    break;

                case 'data_pin':
                    $provider = ApiLink::getBestProvider('DataPin');
                    if ($provider) {
                        $config = [
                            'api_key' => $this->getConfigValue('dataPinApi'),
                            'provider' => $provider->value,
                            'auth_type' => $provider->auth_type ?? 'Basic',
                            'user_url' => $provider->auth_params['user_url'] ?? null,
                        ];
                    } else {
                        $config = [
                            'api_key' => $this->getConfigValue('dataPinApi'),
                            'provider' => $this->getConfigValue('dataPinProvider'),
                            'auth_type' => 'Basic',
                        ];
                    }
                    break;

                case 'alpha_topup':
                    $config = [
                        'api_key' => $this->getConfigValue('alphaApi'),
                        'provider' => $this->getConfigValue('alphaProvider'),
                    ];
                    break;
            }

            // Get provider details if provider URL is specified
            if (isset($config['provider'])) {
                $provider = ApiLink::where('value', $config['provider'])
                    ->where('is_active', true)
                    ->first();

                if ($provider) {
                    $config['provider_name'] = $provider->name;
                    $config['auth_type'] = $provider->auth_type;
                    $config['auth_params'] = $provider->auth_params;
                }
            }

            return $config;
        });
    }

    /**
     * Alias for getServiceConfig for backward compatibility
     */
    public function getServiceConfiguration(string $serviceType): array
    {
        return $this->getServiceConfig($serviceType);
    }

    /**
     * Set configuration value
     */
    public function setConfigValue(string $key, $value, array $attributes = []): ApiConfiguration
    {
        $config = ApiConfiguration::setValue($key, $value, $attributes);

        // Clear related cache
        $this->clearConfigCache($key);

        return $config;
    }

    /**
     * Update multiple configurations at once
     */
    public function updateConfigurations(array $configurations): void
    {
        foreach ($configurations as $key => $value) {
            if (is_array($value)) {
                $this->setConfigValue($key, $value['value'] ?? null, $value);
            } else {
                $this->setConfigValue($key, $value);
            }
        }
    }

    /**
     * Get configuration by key (similar to getConfigValue)
     */
    public function getConfiguration(string $key, $default = null)
    {
        return $this->getConfigValue($key, $default);
    }

    /**
     * Get all configurations for admin management
     */
    public function getAllConfigurations(): Collection
    {
        return Cache::remember('configurations.all', self::CACHE_TTL, function () {
            return ApiConfiguration::where('is_active', true)
                ->orderBy('service_type')
                ->orderBy('config_key')
                ->get();
        });
    }

    /**
     * Clear configuration cache
     */
    public function clearConfigCache(string $key = null): void
    {
        if ($key) {
            Cache::forget("config.{$key}");
        }

        // Clear all related cache
        Cache::forget('api.details');
        Cache::forget('api.links');
        Cache::forget('configurations.all');

        // Clear provider caches
        foreach (['airtime', 'data', 'cable', 'electricity'] as $service) {
            foreach (['MTN', 'AIRTEL', 'GLO', '9MOBILE'] as $network) {
                foreach (['VTU', 'ShareSell', 'SME', 'Corporate', 'Gifting'] as $providerType) {
                    Cache::forget("provider.{$service}.{$network}.{$providerType}");
                }
            }
        }

        // Clear service caches
        foreach (['cable', 'electricity', 'exam', 'recharge_pin', 'data_pin', 'alpha_topup'] as $service) {
            Cache::forget("service.{$service}");
        }
    }

    /**
     * Initialize default configurations from original PHP app
     */
    public function initializeDefaults(): void
    {
        $defaults = $this->getDefaultConfigurations();

        foreach ($defaults as $config) {
            ApiConfiguration::firstOrCreate(
                ['config_key' => $config['config_key']],
                $config
            );
        }

        $this->clearConfigCache();
    }

    /**
     * Get default configurations based on original PHP admin settings
     */
    private function getDefaultConfigurations(): array
    {
        return [
            // Cable TV configurations
            [
                'config_key' => 'cableVerificationApi',
                'config_value' => '',
                'service_type' => 'cable',
                'description' => 'Cable TV IUC Verification API Key'
            ],
            [
                'config_key' => 'cableVerificationProvider',
                'config_value' => '',
                'service_type' => 'cable',
                'description' => 'Cable TV IUC Verification Provider URL'
            ],
            [
                'config_key' => 'cableApi',
                'config_value' => '',
                'service_type' => 'cable',
                'description' => 'Cable TV API Key'
            ],
            [
                'config_key' => 'cableProvider',
                'config_value' => '',
                'service_type' => 'cable',
                'description' => 'Cable TV Provider URL'
            ],

            // Electricity configurations
            [
                'config_key' => 'meterVerificationApi',
                'config_value' => '',
                'service_type' => 'electricity',
                'description' => 'Electricity Meter Verification API Key'
            ],
            [
                'config_key' => 'meterVerificationProvider',
                'config_value' => '',
                'service_type' => 'electricity',
                'description' => 'Electricity Meter Verification Provider URL'
            ],
            [
                'config_key' => 'meterApi',
                'config_value' => '',
                'service_type' => 'electricity',
                'description' => 'Electricity API Key'
            ],
            [
                'config_key' => 'meterProvider',
                'config_value' => '',
                'service_type' => 'electricity',
                'description' => 'Electricity Provider URL'
            ],

            // Other service configurations
            [
                'config_key' => 'examApi',
                'config_value' => '',
                'service_type' => 'exam',
                'description' => 'Exam Checker API Key'
            ],
            [
                'config_key' => 'examProvider',
                'config_value' => '',
                'service_type' => 'exam',
                'description' => 'Exam Checker Provider URL'
            ],
            [
                'config_key' => 'rechargePinApi',
                'config_value' => '',
                'service_type' => 'recharge_pin',
                'description' => 'Recharge Card API Key'
            ],
            [
                'config_key' => 'rechargePinProvider',
                'config_value' => '',
                'service_type' => 'recharge_pin',
                'description' => 'Recharge Card Provider URL'
            ],
            [
                'config_key' => 'dataPinApi',
                'config_value' => '',
                'service_type' => 'data_pin',
                'description' => 'Data Pin API Key'
            ],
            [
                'config_key' => 'dataPinProvider',
                'config_value' => '',
                'service_type' => 'data_pin',
                'description' => 'Data Pin Provider URL'
            ],
            [
                'config_key' => 'alphaApi',
                'config_value' => '',
                'service_type' => 'alpha_topup',
                'description' => 'Alpha Topup API Key'
            ],
            [
                'config_key' => 'alphaProvider',
                'config_value' => '',
                'service_type' => 'alpha_topup',
                'description' => 'Alpha Topup Provider URL'
            ],
        ];
    }
}
