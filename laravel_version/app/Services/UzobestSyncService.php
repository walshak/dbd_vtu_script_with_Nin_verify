<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service for syncing plans and configurations from Uzobest API
 * Used by admin controllers to fetch and update service plans
 */
class UzobestSyncService
{
    private $apiUrl;
    private $apiKey;
    private $adapter;

    public function __construct()
    {
        $this->apiUrl = config('services.uzobest.url', 'https://uzobestgsm.com/api');
        $this->apiKey = config('services.uzobest.key');
        $this->adapter = new UzobestApiAdapter();
    }

    /**
     * Fetch all networks and their data plans from Uzobest
     * Endpoint: GET /api/network/
     *
     * @return array Returns array of networks with their plans
     */
    public function fetchDataPlans(): array
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Uzobest API key not configured',
                    'error_code' => 'CONFIG_ERROR'
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->timeout(30)->get($this->apiUrl . '/network/');

            if (!$response->successful()) {
                Log::error('Uzobest fetch data plans error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to fetch data plans from Uzobest',
                    'error_code' => 'API_ERROR',
                    'status_code' => $response->status()
                ];
            }

            $data = $response->json();

            Log::info('Uzobest data plans fetched successfully', [
                'networks_count' => count($data ?? [])
            ]);

            return [
                'success' => true,
                'data' => $data,
                'message' => 'Data plans fetched successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Uzobest fetch data plans exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception fetching data plans: ' . $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Fetch user account details from Uzobest
     * Endpoint: GET /api/user/
     * Returns account balance and other details
     *
     * @return array User account information
     */
    public function fetchUserDetails(): array
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Uzobest API key not configured'
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->timeout(15)->get($this->apiUrl . '/user/');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch user details',
                    'status_code' => $response->status()
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'data' => $data,
                'balance' => $data['Account_Balance'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Uzobest fetch user details exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception fetching user details: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate IUC/Smartcard number for cable subscription
     * Endpoint: GET /api/validateiuc?smart_card_number={iuc}&cablename={provider_id}
     *
     * @param string $iucNumber IUC/Smartcard number
     * @param int $cableProviderId Uzobest cable provider ID (1=DSTV, 2=GOTV, 3=STARTIMES)
     * @return array Validation result with customer details
     */
    public function validateIUC(string $iucNumber, int $cableProviderId): array
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Uzobest API key not configured'
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->timeout(20)->get($this->apiUrl . '/validateiuc', [
                'smart_card_number' => $iucNumber,
                'cablename' => $cableProviderId
            ]);

            if (!$response->successful()) {
                Log::error('Uzobest IUC validation error', [
                    'iuc' => $iucNumber,
                    'provider_id' => $cableProviderId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to validate IUC number',
                    'status_code' => $response->status()
                ];
            }

            $data = $response->json();

            // Check if validation was successful
            $isValid = isset($data['name']) || isset($data['Customer_Name']);

            return [
                'success' => $isValid,
                'message' => $isValid ? 'IUC validated successfully' : 'Invalid IUC number',
                'data' => $data,
                'customer_name' => $data['Customer_Name'] ?? $data['name'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Uzobest IUC validation exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception validating IUC: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate meter number for electricity payment
     * Endpoint: GET /api/validatemeter?meternumber={meter}&disconame={disco_name}&mtype={meter_type}
     *
     * @param string $meterNumber Meter number
     * @param string $discoProviderName Uzobest disco provider name (e.g., "Jos Electric")
     * @param string $meterType Meter type string ("PREPAID" or "POSTPAID")
     * @return array Validation result with customer details
     */
    public function validateMeter(string $meterNumber, string $discoProviderName, string $meterType): array
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Uzobest API key not configured'
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
            ])->timeout(20)->get($this->apiUrl . '/validatemeter', [
                'meternumber' => $meterNumber,
                'disconame' => $discoProviderName,
                'mtype' => $meterType
            ]);

            if (!$response->successful()) {
                Log::error('Uzobest meter validation error', [
                    'meter' => $meterNumber,
                    'disco_name' => $discoProviderName,
                    'meter_type' => $meterType,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to validate meter number',
                    'status_code' => $response->status()
                ];
            }

            $data = $response->json();

            // Log the full Uzobest API response for debugging
            Log::info('Uzobest validateMeter API Response', [
                'meter' => $meterNumber,
                'disco_name' => $discoProviderName,
                'meter_type' => $meterType,
                'status_code' => $response->status(),
                'response_data' => $data,
                'raw_body' => $response->body()
            ]);

            // Check if validation was successful
            $isValid = isset($data['name']) || isset($data['Customer_Name']);

            return [
                'success' => $isValid,
                'message' => $isValid ? 'Meter validated successfully' : 'Invalid meter number',
                'data' => $data,
                'customer_name' => $data['Customer_Name'] ?? $data['name'] ?? null,
                'address' => $data['Address'] ?? $data['address'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Uzobest meter validation exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception validating meter: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse Uzobest data plans response and organize by network
     * Transforms the raw API response into a structured format
     *
     * @param array $rawData Raw data from /api/network/ endpoint
     * @return array Organized plans by network
     */
    public function parseDataPlans(array $rawData): array
    {
        $organizedPlans = [];

        try {
            // Uzobest returns data in format:
            // {
            //   "MTN_PLAN": [{"id": 1, "plan": "500MB", "plan_type": "SME", "plan_amount": 100, ...}],
            //   "GLO_PLAN": [...],
            //   "AIRTEL_PLAN": [...],
            //   "9MOBILE_PLAN": [...]
            // }

            foreach ($rawData as $networkKey => $plans) {
                if (!is_array($plans) || empty($plans)) {
                    continue;
                }

                // Group plans by type (SME, Gifting, Corporate, etc.)
                $groupedPlans = [];
                foreach ($plans as $plan) {
                    $planType = $plan['plan_type'] ?? 'SME'; // Default to SME if not specified

                    if (!isset($groupedPlans[$planType])) {
                        $groupedPlans[$planType] = [];
                    }

                    $groupedPlans[$planType][] = $plan;
                }

                $organizedPlans[$networkKey] = $groupedPlans;
            }

            Log::info('Uzobest data plans parsed', [
                'networks' => array_keys($organizedPlans),
                'total_plans' => array_sum(array_map(function($network) {
                    return array_sum(array_map('count', $network));
                }, $organizedPlans))
            ]);

            return $organizedPlans;
        } catch (\Exception $e) {
            Log::error('Error parsing Uzobest data plans', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get cached data plans or fetch fresh ones
     * Cache for 1 hour to reduce API calls
     *
     * @param bool $forceRefresh Force fetch from API even if cached
     * @return array Data plans
     */
    public function getCachedDataPlans(bool $forceRefresh = false): array
    {
        $cacheKey = 'uzobest_data_plans';
        $cacheDuration = 3600; // 1 hour

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $cacheDuration, function () {
            $result = $this->fetchDataPlans();

            if ($result['success']) {
                return $this->parseDataPlans($result['data']);
            }

            return [];
        });
    }

    /**
     * Get Uzobest API network IDs mapping
     * These are the IDs used by Uzobest for network identification
     *
     * @return array Network name to ID mapping
     */
    public function getNetworkMapping(): array
    {
        return [
            'MTN' => 1,
            'AIRTEL' => 4,
            'GLO' => 2,
            '9MOBILE' => 3,
        ];
    }

    /**
     * Get Uzobest cable provider IDs mapping
     * CRITICAL: These IDs are from official Uzobest documentation
     *
     * @return array Cable provider name to ID mapping
     */
    public function getCableProviderMapping(): array
    {
        return [
            'GOTV' => 1,      // ✓ Official Uzobest ID
            'DSTV' => 2,      // ✓ Official Uzobest ID
            'STARTIME' => 3,  // ✓ Official Uzobest ID (note: STARTIME not STARTIMES)
        ];
    }

    /**
     * Get OFFICIAL Uzobest cable plan ID mappings
     * Source: Uzobest API documentation (verified Dec 16, 2025)
     *
     * CRITICAL: These are the ACTUAL Uzobest plan IDs, not sequential numbers
     * Format: 'provider-planname' => official_uzobest_plan_id
     *
     * @return array Plan identifier to Uzobest numeric ID mapping
     */
    public function getStandardCablePlanMapping(): array
    {
        return [
            // DSTV Plans (cablename: 2)
            'dstv-padi' => 20,              // ₦4,400
            'dstv-yanga' => 6,              // ₦6,000
            'dstv-confam' => 19,            // ₦11,000
            'dstv-compact' => 7,            // ₦19,000
            'dstv-compact-plus' => 8,       // ₦30,000
            'dstv-premium' => 9,            // ₦44,500

            // GOTV Plans (cablename: 1)
            'gotv-smallie' => 34,           // ₦1,900 Monthly
            'gotv-jinja' => 16,             // ₦3,900
            'gotv-jolli' => 17,             // ₦5,800
            'gotv-max' => 2,                // ₦8,500
            'gotv-supa' => 47,              // ₦11,400

            // STARTIME Plans (cablename: 3)
            'startimes-nova' => 14,         // ₦2,100 Monthly
            'startime-nova' => 14,          // Alternative spelling
            'startimes-basic' => 12,        // ₦4,000
            'startime-basic' => 12,         // Alternative spelling
            'startimes-smart' => 13,        // ₦5,100
            'startime-smart' => 13,         // Alternative spelling
            'startimes-classic' => 11,      // ₦6,000
            'startime-classic' => 11,       // Alternative spelling
            'startimes-super' => 15,        // ₦9,800
            'startime-super' => 15,         // Alternative spelling
        ];
    }

    /**
     * Get Uzobest electricity disco provider IDs mapping
     * Official mapping from Uzobest API documentation
     *
     * @return array Disco provider name to ID mapping
     */
    public function getDiscoProviderMapping(): array
    {
        return [
            'IKEDC' => 1,    // Ikeja Electric
            'EKEDC' => 2,    // Eko Electric
            'AEDC' => 3,     // Abuja Electric
            'KEDCO' => 4,    // Kano Electric
            'EEDC' => 5,     // Enugu Electric
            'PHED' => 6,     // Port Harcourt Electric
            'IBEDC' => 7,    // Ibadan Electric
            'KAEDCO' => 8,   // Kaduna Electric
            'JED' => 9,      // Jos Electric
            'BEDC' => 10,    // Benin Electric
            'YEDC' => 11,    // Yola Electric
            'ABA' => 12,     // Aba Electric
        ];
    }

    /**
     * Get meter type ID mapping
     *
     * @return array Meter type to ID mapping
     */
    public function getMeterTypeMapping(): array
    {
        return [
            'PREPAID' => 1,
            'POSTPAID' => 2,
        ];
    }

    /**
     * Get Uzobest airtime type mapping
     *
     * @return array Airtime type mapping
     */
    public function getAirtimeTypeMapping(): array
    {
        return [
            'VTU' => 'VTU',
            'Share and Sell' => 'Share and Sell',
            'awuf4U' => 'awuf4U',
        ];
    }
}
