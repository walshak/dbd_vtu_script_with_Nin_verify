<?php

namespace App\Services;

use App\Models\DataPlan;
use App\Models\CableTVPlan;
use App\Models\ExamType;
use App\Models\RechargePin;
use App\Models\NetworkId;
use App\Models\AlphaTopup;
use App\Services\ConfigurationService;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Exception;

class PricingService
{
    protected $configurationService;
    protected $externalApiService;

    // Cache durations in minutes
    const CACHE_DURATION = 30; // 30 minutes for real-time pricing
    const FALLBACK_CACHE_DURATION = 1440; // 24 hours for fallback data

    public function __construct(
        ConfigurationService $configurationService,
        ExternalApiService $externalApiService
    ) {
        $this->configurationService = $configurationService;
        $this->externalApiService = $externalApiService;
    }

    /**
     * Get real-time airtime pricing from external API
     */
    public function getAirtimePricing(string $userType = 'user'): array
    {
        $cacheKey = "realtime_airtime_pricing_{$userType}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userType) {
            try {
                $config = $this->configurationService->getServiceConfiguration('airtime');
                if (!$config) {
                    return $this->getFallbackAirtimePricing($userType);
                }

                // Fetch real-time pricing from external API
                $realTimePricing = $this->fetchExternalAirtimePricing($config);

                if ($realTimePricing) {
                    return $this->processAirtimePricing($realTimePricing, $userType);
                }

                return $this->getFallbackAirtimePricing($userType);

            } catch (Exception $e) {
                Log::error('Error fetching real-time airtime pricing', [
                    'error' => $e->getMessage(),
                    'user_type' => $userType
                ]);

                return $this->getFallbackAirtimePricing($userType);
            }
        });
    }

    /**
     * Get real-time data pricing from external API
     */
    public function getDataPricing(string $userType = 'user'): array
    {
        $cacheKey = "realtime_data_pricing_{$userType}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userType) {
            try {
                $config = $this->configurationService->getServiceConfiguration('data');
                if (!$config) {
                    return $this->getFallbackDataPricing($userType);
                }

                // Fetch real-time pricing from external API
                $realTimePricing = $this->fetchExternalDataPricing($config);

                if ($realTimePricing) {
                    return $this->processDataPricing($realTimePricing, $userType);
                }

                return $this->getFallbackDataPricing($userType);

            } catch (Exception $e) {
                Log::error('Error fetching real-time data pricing', [
                    'error' => $e->getMessage(),
                    'user_type' => $userType
                ]);

                return $this->getFallbackDataPricing($userType);
            }
        });
    }

    /**
     * Get real-time cable TV pricing from external API
     */
    public function getCableTVPricing(string $userType = 'user'): array
    {
        $cacheKey = "realtime_cabletv_pricing_{$userType}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userType) {
            try {
                $config = $this->configurationService->getServiceConfiguration('cable');
                if (!$config) {
                    return $this->getFallbackCableTVPricing($userType);
                }

                // Fetch real-time pricing from external API
                $realTimePricing = $this->fetchExternalCableTVPricing($config);

                if ($realTimePricing) {
                    return $this->processCableTVPricing($realTimePricing, $userType);
                }

                return $this->getFallbackCableTVPricing($userType);

            } catch (Exception $e) {
                Log::error('Error fetching real-time cable TV pricing', [
                    'error' => $e->getMessage(),
                    'user_type' => $userType
                ]);

                return $this->getFallbackCableTVPricing($userType);
            }
        });
    }

    /**
     * Get real-time electricity pricing from external API
     */
    public function getElectricityPricing(string $userType = 'user'): array
    {
        $cacheKey = "realtime_electricity_pricing_{$userType}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userType) {
            try {
                $config = $this->configurationService->getServiceConfiguration('electricity');
                if (!$config) {
                    return $this->getFallbackElectricityPricing($userType);
                }

                // Fetch real-time pricing from external API
                $realTimePricing = $this->fetchExternalElectricityPricing($config);

                if ($realTimePricing) {
                    return $this->processElectricityPricing($realTimePricing, $userType);
                }

                return $this->getFallbackElectricityPricing($userType);

            } catch (Exception $e) {
                Log::error('Error fetching real-time electricity pricing', [
                    'error' => $e->getMessage(),
                    'user_type' => $userType
                ]);

                return $this->getFallbackElectricityPricing($userType);
            }
        });
    }

    /**
     * Get real-time exam pin pricing
     */
    public function getExamPinPricing(string $userType = 'user'): array
    {
        $cacheKey = "realtime_exampin_pricing_{$userType}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userType) {
            try {
                $config = $this->configurationService->getServiceConfiguration('exam');
                if (!$config) {
                    return $this->getFallbackExamPinPricing($userType);
                }

                // Fetch real-time pricing from external API
                $realTimePricing = $this->fetchExternalExamPinPricing($config);

                if ($realTimePricing) {
                    return $this->processExamPinPricing($realTimePricing, $userType);
                }

                return $this->getFallbackExamPinPricing($userType);

            } catch (Exception $e) {
                Log::error('Error fetching real-time exam pin pricing', [
                    'error' => $e->getMessage(),
                    'user_type' => $userType
                ]);

                return $this->getFallbackExamPinPricing($userType);
            }
        });
    }

    /**
     * Get real-time recharge pin pricing
     */
    public function getRechargePinPricing(string $userType = 'user'): array
    {
        $cacheKey = "realtime_rechargepin_pricing_{$userType}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userType) {
            try {
                $config = $this->configurationService->getServiceConfiguration('recharge_pin');
                if (!$config) {
                    return $this->getFallbackRechargePinPricing($userType);
                }

                // Fetch real-time pricing from external API
                $realTimePricing = $this->fetchExternalRechargePinPricing($config);

                if ($realTimePricing) {
                    return $this->processRechargePinPricing($realTimePricing, $userType);
                }

                return $this->getFallbackRechargePinPricing($userType);

            } catch (Exception $e) {
                Log::error('Error fetching real-time recharge pin pricing', [
                    'error' => $e->getMessage(),
                    'user_type' => $userType
                ]);

                return $this->getFallbackRechargePinPricing($userType);
            }
        });
    }

    /**
     * Get real-time alpha topup pricing
     */
    public function getAlphaTopupPricing(string $userType = 'user'): array
    {
        $cacheKey = "realtime_alphatopup_pricing_{$userType}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userType) {
            try {
                $config = $this->configurationService->getServiceConfiguration('alpha_topup');
                if (!$config) {
                    return $this->getFallbackAlphaTopupPricing($userType);
                }

                // Fetch real-time pricing from external API
                $realTimePricing = $this->fetchExternalAlphaTopupPricing($config);

                if ($realTimePricing) {
                    return $this->processAlphaTopupPricing($realTimePricing, $userType);
                }

                return $this->getFallbackAlphaTopupPricing($userType);

            } catch (Exception $e) {
                Log::error('Error fetching real-time alpha topup pricing', [
                    'error' => $e->getMessage(),
                    'user_type' => $userType
                ]);

                return $this->getFallbackAlphaTopupPricing($userType);
            }
        });
    }

    /**
     * Fetch external airtime pricing from API
     */
    private function fetchExternalAirtimePricing(array $config): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->get($config['pricing_url'] ?? $config['provider'] . '/pricing/airtime');

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('External airtime pricing API returned error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;

        } catch (Exception $e) {
            Log::error('Error calling external airtime pricing API', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Fetch external data pricing from API
     */
    private function fetchExternalDataPricing(array $config): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->get($config['pricing_url'] ?? $config['provider'] . '/pricing/data');

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (Exception $e) {
            Log::error('Error calling external data pricing API', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Fetch external cable TV pricing from API
     */
    private function fetchExternalCableTVPricing(array $config): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->get($config['pricing_url'] ?? $config['provider'] . '/pricing/cabletv');

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (Exception $e) {
            Log::error('Error calling external cable TV pricing API', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Fetch external electricity pricing from API
     */
    private function fetchExternalElectricityPricing(array $config): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->get($config['pricing_url'] ?? $config['provider'] . '/pricing/electricity');

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (Exception $e) {
            Log::error('Error calling external electricity pricing API', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Fetch external exam pin pricing from API
     */
    private function fetchExternalExamPinPricing(array $config): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->get($config['pricing_url'] ?? $config['provider'] . '/pricing/exampin');

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (Exception $e) {
            Log::error('Error calling external exam pin pricing API', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Fetch external recharge pin pricing from API
     */
    private function fetchExternalRechargePinPricing(array $config): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->get($config['pricing_url'] ?? $config['provider'] . '/pricing/rechargepin');

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (Exception $e) {
            Log::error('Error calling external recharge pin pricing API', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Fetch external alpha topup pricing from API
     */
    private function fetchExternalAlphaTopupPricing(array $config): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->get($config['pricing_url'] ?? $config['provider'] . '/pricing/alphatopup');

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (Exception $e) {
            Log::error('Error calling external alpha topup pricing API', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Process external airtime pricing data
     */
    private function processAirtimePricing(array $externalData, string $userType): array
    {
        $processed = [];
        $networks = $externalData['networks'] ?? $externalData['data'] ?? [];

        foreach ($networks as $network) {
            $networkName = $network['network'] ?? $network['name'] ?? 'Unknown';
            $baseRate = $network['rate'] ?? $network['price_per_naira'] ?? 1.0;
            $userRate = $this->applyUserDiscount($baseRate, $userType);

            $processed[] = [
                'network' => $networkName,
                'network_code' => strtolower($networkName),
                'min_amount' => $network['min_amount'] ?? 50,
                'max_amount' => $network['max_amount'] ?? 50000,
                'rate' => $userRate,
                'discount_percentage' => $this->getUserDiscountPercentage($userType),
                'external_rate' => $baseRate,
                'last_updated' => now()->toISOString(),
                'source' => 'external_api'
            ];
        }

        return $processed;
    }

    /**
     * Process external data pricing data
     */
    private function processDataPricing(array $externalData, string $userType): array
    {
        $processed = [];
        $plans = $externalData['plans'] ?? $externalData['data'] ?? [];

        foreach ($plans as $plan) {
            $basePrice = $plan['price'] ?? $plan['amount'] ?? 0;
            $userPrice = $this->applyUserDiscount($basePrice, $userType);

            $processed[] = [
                'plan_id' => $plan['plan_id'] ?? $plan['id'] ?? null,
                'network' => $plan['network'] ?? 'Unknown',
                'network_code' => strtolower($plan['network'] ?? 'unknown'),
                'plan_name' => $plan['plan_name'] ?? $plan['name'] ?? 'Unknown Plan',
                'data_size' => $plan['data_size'] ?? $plan['size'] ?? 'Unknown',
                'validity' => $plan['validity'] ?? $plan['duration'] ?? 'N/A',
                'external_price' => number_format($basePrice, 2),
                'user_price' => number_format($userPrice, 2),
                'discount_percentage' => $this->getUserDiscountPercentage($userType),
                'savings' => number_format($basePrice - $userPrice, 2),
                'last_updated' => now()->toISOString(),
                'source' => 'external_api'
            ];
        }

        return $processed;
    }

    /**
     * Process external cable TV pricing data
     */
    private function processCableTVPricing(array $externalData, string $userType): array
    {
        $processed = [];
        $plans = $externalData['plans'] ?? $externalData['data'] ?? [];

        foreach ($plans as $plan) {
            $basePrice = $plan['price'] ?? $plan['amount'] ?? 0;
            $userPrice = $this->applyUserDiscount($basePrice, $userType);

            $processed[] = [
                'plan_id' => $plan['plan_id'] ?? $plan['id'] ?? null,
                'provider' => $plan['provider'] ?? 'Unknown',
                'plan_name' => $plan['plan_name'] ?? $plan['name'] ?? 'Unknown Plan',
                'plan_code' => $plan['plan_code'] ?? $plan['code'] ?? '',
                'external_price' => number_format($basePrice, 2),
                'user_price' => number_format($userPrice, 2),
                'discount_percentage' => $this->getUserDiscountPercentage($userType),
                'validity' => $plan['validity'] ?? $plan['duration'] ?? 'Monthly',
                'last_updated' => now()->toISOString(),
                'source' => 'external_api'
            ];
        }

        return $processed;
    }

    /**
     * Process external electricity pricing data
     */
    private function processElectricityPricing(array $externalData, string $userType): array
    {
        $baseData = $externalData['data'] ?? $externalData;
        $serviceCharge = $baseData['service_charge'] ?? 0;
        $baseRate = $baseData['rate'] ?? $baseData['price_per_naira'] ?? 1.0;
        $userRate = $this->applyUserDiscount($baseRate, $userType);

        return [
            'service_charge' => $serviceCharge,
            'min_amount' => $baseData['min_amount'] ?? 1000,
            'max_amount' => $baseData['max_amount'] ?? 50000,
            'external_rate' => $baseRate,
            'user_rate' => $userRate,
            'discount_percentage' => $this->getUserDiscountPercentage($userType),
            'last_updated' => now()->toISOString(),
            'source' => 'external_api'
        ];
    }

    /**
     * Process external exam pin pricing data
     */
    private function processExamPinPricing(array $externalData, string $userType): array
    {
        $processed = [];
        $exams = $externalData['exams'] ?? $externalData['data'] ?? [];

        foreach ($exams as $exam) {
            $basePrice = $exam['price'] ?? $exam['amount'] ?? 0;
            $userPrice = $this->applyUserDiscount($basePrice, $userType);

            $processed[] = [
                'exam_id' => $exam['exam_id'] ?? $exam['id'] ?? null,
                'exam_name' => $exam['exam_name'] ?? $exam['name'] ?? 'Unknown Exam',
                'exam_code' => $exam['exam_code'] ?? strtolower(str_replace(' ', '_', $exam['name'] ?? 'unknown')),
                'external_price' => number_format($basePrice, 2),
                'user_price' => number_format($userPrice, 2),
                'discount_percentage' => $this->getUserDiscountPercentage($userType),
                'savings' => number_format($basePrice - $userPrice, 2),
                'last_updated' => now()->toISOString(),
                'source' => 'external_api'
            ];
        }

        return $processed;
    }

    /**
     * Process external recharge pin pricing data
     */
    private function processRechargePinPricing(array $externalData, string $userType): array
    {
        $processed = [];
        $pins = $externalData['pins'] ?? $externalData['data'] ?? [];

        foreach ($pins as $pin) {
            $basePrice = $pin['price'] ?? $pin['selling_price'] ?? 0;
            $userPrice = $this->applyUserDiscount($basePrice, $userType);

            $processed[] = [
                'pin_id' => $pin['pin_id'] ?? $pin['id'] ?? null,
                'network' => $pin['network'] ?? 'Unknown',
                'network_code' => strtolower($pin['network'] ?? 'unknown'),
                'denomination' => $pin['denomination'] ?? $pin['amount'] ?? 0,
                'external_price' => number_format($basePrice, 2),
                'user_price' => number_format($userPrice, 2),
                'discount_percentage' => $this->getUserDiscountPercentage($userType),
                'savings' => number_format($basePrice - $userPrice, 2),
                'last_updated' => now()->toISOString(),
                'source' => 'external_api'
            ];
        }

        return $processed;
    }

    /**
     * Process external alpha topup pricing data
     */
    private function processAlphaTopupPricing(array $externalData, string $userType): array
    {
        $processed = [];
        $plans = $externalData['plans'] ?? $externalData['data'] ?? [];

        foreach ($plans as $plan) {
            $basePrice = $plan['price'] ?? $plan['selling_price'] ?? 0;
            $userPrice = $this->applyUserDiscount($basePrice, $userType);

            $processed[] = [
                'plan_id' => $plan['plan_id'] ?? $plan['id'] ?? null,
                'amount' => $plan['amount'] ?? $basePrice,
                'external_price' => number_format($basePrice, 2),
                'user_price' => number_format($userPrice, 2),
                'discount_percentage' => $this->getUserDiscountPercentage($userType),
                'savings' => number_format($basePrice - $userPrice, 2),
                'last_updated' => now()->toISOString(),
                'source' => 'external_api'
            ];
        }

        return $processed;
    }

    /**
     * Apply user discount to base price
     */
    private function applyUserDiscount(float $basePrice, string $userType): float
    {
        $discountPercentage = $this->getUserDiscountPercentage($userType);
        return $basePrice * (1 - ($discountPercentage / 100));
    }

    /**
     * Get user discount percentage
     */
    private function getUserDiscountPercentage(string $userType): float
    {
        $discountMap = [
            'user' => 0,
            'agent' => 2,
            'super_agent' => 5,
            'api_user' => 3,
            'vendor' => 7
        ];

        return $discountMap[$userType] ?? 0;
    }

    /**
     * Get fallback airtime pricing from database
     */
    private function getFallbackAirtimePricing(string $userType): array
    {
        return Cache::remember("fallback_airtime_pricing_{$userType}", self::FALLBACK_CACHE_DURATION, function () use ($userType) {
            $networks = NetworkId::getAllActive();
            $pricing = [];

            foreach ($networks as $network) {
                $discountPercentage = $this->getUserDiscountPercentage($userType);

                $pricing[] = [
                    'network' => $network->network,
                    'network_code' => strtolower($network->network),
                    'min_amount' => 50,
                    'max_amount' => 50000,
                    'rate' => 1 - ($discountPercentage / 100),
                    'discount_percentage' => $discountPercentage,
                    'last_updated' => now()->toISOString(),
                    'source' => 'database_fallback'
                ];
            }

            return $pricing;
        });
    }

    /**
     * Get fallback data pricing from database
     */
    private function getFallbackDataPricing(string $userType): array
    {
        return Cache::remember("fallback_data_pricing_{$userType}", self::FALLBACK_CACHE_DURATION, function () use ($userType) {
            $dataPlans = DataPlan::where('status', 'active')->orderBy('network_id')->orderBy('data_size')->get();
            $pricing = [];

            foreach ($dataPlans as $plan) {
                $userPrice = $plan->getUserPrice($userType);
                $discountPercentage = (($plan->selling_price - $userPrice) / $plan->selling_price) * 100;

                $pricing[] = [
                    'plan_id' => $plan->id,
                    'network' => $plan->network->network ?? 'Unknown',
                    'network_code' => strtolower($plan->network->network ?? 'unknown'),
                    'plan_name' => $plan->plan_name,
                    'data_size' => $plan->data_size,
                    'validity' => $plan->validity,
                    'external_price' => number_format($plan->selling_price, 2),
                    'user_price' => number_format($userPrice, 2),
                    'discount_percentage' => number_format($discountPercentage, 2),
                    'savings' => number_format($plan->selling_price - $userPrice, 2),
                    'last_updated' => now()->toISOString(),
                    'source' => 'database_fallback'
                ];
            }

            return $pricing;
        });
    }

    /**
     * Get fallback cable TV pricing from database
     */
    private function getFallbackCableTVPricing(string $userType): array
    {
        return Cache::remember("fallback_cabletv_pricing_{$userType}", self::FALLBACK_CACHE_DURATION, function () use ($userType) {
            $cablePlans = CableTVPlan::where('status', 'active')->orderBy('provider')->orderBy('amount')->get();
            $pricing = [];

            foreach ($cablePlans as $plan) {
                $userPrice = $plan->getUserPrice($userType);
                $discountPercentage = (($plan->amount - $userPrice) / $plan->amount) * 100;

                $pricing[] = [
                    'plan_id' => $plan->id,
                    'provider' => $plan->provider,
                    'plan_name' => $plan->plan_name,
                    'plan_code' => $plan->plan_code,
                    'external_price' => number_format($plan->amount, 2),
                    'user_price' => number_format($userPrice, 2),
                    'discount_percentage' => number_format($discountPercentage, 2),
                    'validity' => $plan->validity ?? 'Monthly',
                    'last_updated' => now()->toISOString(),
                    'source' => 'database_fallback'
                ];
            }

            return $pricing;
        });
    }

    /**
     * Get fallback electricity pricing
     */
    private function getFallbackElectricityPricing(string $userType): array
    {
        $discountPercentage = $this->getUserDiscountPercentage($userType);

        return [
            'service_charge' => 0,
            'min_amount' => 1000,
            'max_amount' => 50000,
            'external_rate' => 1.0,
            'user_rate' => 1 - ($discountPercentage / 100),
            'discount_percentage' => $discountPercentage,
            'last_updated' => now()->toISOString(),
            'source' => 'database_fallback'
        ];
    }

    /**
     * Get fallback exam pin pricing from database
     */
    private function getFallbackExamPinPricing(string $userType): array
    {
        return Cache::remember("fallback_exampin_pricing_{$userType}", self::FALLBACK_CACHE_DURATION, function () use ($userType) {
            $examTypes = ExamType::where('status', 'active')->get();
            $pricing = [];

            foreach ($examTypes as $exam) {
                $userPrice = $exam->getUserPrice($userType);
                $discountPercentage = (($exam->amount - $userPrice) / $exam->amount) * 100;

                $pricing[] = [
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->exam,
                    'exam_code' => strtolower(str_replace(' ', '_', $exam->exam)),
                    'external_price' => number_format($exam->amount, 2),
                    'user_price' => number_format($userPrice, 2),
                    'discount_percentage' => number_format($discountPercentage, 2),
                    'savings' => number_format($exam->amount - $userPrice, 2),
                    'last_updated' => now()->toISOString(),
                    'source' => 'database_fallback'
                ];
            }

            return $pricing;
        });
    }

    /**
     * Get fallback recharge pin pricing from database
     */
    private function getFallbackRechargePinPricing(string $userType): array
    {
        return Cache::remember("fallback_rechargepin_pricing_{$userType}", self::FALLBACK_CACHE_DURATION, function () use ($userType) {
            $rechargePins = RechargePin::where('status', 'active')
                ->orderBy('network_id')
                ->orderBy('denomination')
                ->get();
            $pricing = [];

            foreach ($rechargePins as $pin) {
                $userPrice = $pin->getUserPrice($userType);
                $discountPercentage = (($pin->selling_price - $userPrice) / $pin->selling_price) * 100;

                $pricing[] = [
                    'pin_id' => $pin->id,
                    'network' => $pin->network->network ?? 'Unknown',
                    'network_code' => strtolower($pin->network->network ?? 'unknown'),
                    'denomination' => $pin->denomination,
                    'external_price' => number_format($pin->selling_price, 2),
                    'user_price' => number_format($userPrice, 2),
                    'discount_percentage' => number_format($discountPercentage, 2),
                    'savings' => number_format($pin->selling_price - $userPrice, 2),
                    'last_updated' => now()->toISOString(),
                    'source' => 'database_fallback'
                ];
            }

            return $pricing;
        });
    }

    /**
     * Get fallback alpha topup pricing from database
     */
    private function getFallbackAlphaTopupPricing(string $userType): array
    {
        return Cache::remember("fallback_alphatopup_pricing_{$userType}", self::FALLBACK_CACHE_DURATION, function () use ($userType) {
            $alphaPlans = AlphaTopup::getActivePlans();
            $pricing = [];

            foreach ($alphaPlans as $plan) {
                $userPrice = $plan->getUserPrice($userType);
                $discountPercentage = $plan->getDiscountPercentage($userType);

                $pricing[] = [
                    'plan_id' => $plan->alphaId,
                    'amount' => $plan->sellingPrice,
                    'external_price' => number_format($plan->sellingPrice, 2),
                    'user_price' => number_format($userPrice, 2),
                    'discount_percentage' => number_format($discountPercentage, 2),
                    'savings' => number_format($plan->sellingPrice - $userPrice, 2),
                    'last_updated' => now()->toISOString(),
                    'source' => 'database_fallback'
                ];
            }

            return $pricing;
        });
    }

    /**
     * Clear all pricing caches
     */
    public function clearPricingCache(): bool
    {
        try {
            $patterns = [
                'realtime_*_pricing_*',
                'fallback_*_pricing_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }

            Log::info('Pricing cache cleared successfully');
            return true;

        } catch (Exception $e) {
            Log::error('Error clearing pricing cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cache status for pricing data
     */
    public function getPricingCacheStatus(): array
    {
        $userTypes = ['user', 'agent', 'super_agent', 'api_user', 'vendor'];
        $services = ['airtime', 'data', 'cabletv', 'electricity', 'exampin', 'rechargepin', 'alphatopup'];

        $status = [
            'realtime_cache' => [],
            'fallback_cache' => [],
            'last_checked' => now()->toISOString()
        ];

        foreach ($services as $service) {
            foreach ($userTypes as $userType) {
                $realtimeKey = "realtime_{$service}_pricing_{$userType}";
                $fallbackKey = "fallback_{$service}_pricing_{$userType}";

                $status['realtime_cache'][$service][$userType] = Cache::has($realtimeKey);
                $status['fallback_cache'][$service][$userType] = Cache::has($fallbackKey);
            }
        }

        return $status;
    }
}
