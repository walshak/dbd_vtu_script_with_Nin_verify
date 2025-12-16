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
     * Endpoint: GET /api/validatemeter?meternumber={meter}&disconame={disco_id}&mtype={meter_type}
     *
     * @param string $meterNumber Meter number
     * @param int $discoProviderId Uzobest disco provider ID
     * @param int $meterType Meter type ID (1=PREPAID, 2=POSTPAID)
     * @return array Validation result with customer details
     */
    public function validateMeter(string $meterNumber, int $discoProviderId, int $meterType): array
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
                'disconame' => $discoProviderId,
                'mtype' => $meterType
            ]);

            if (!$response->successful()) {
                Log::error('Uzobest meter validation error', [
                    'meter' => $meterNumber,
                    'disco_id' => $discoProviderId,
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
            // Each network has plans categorized by type (SME, Gifting, Corporate)
            // Example structure:
            // {
            //   "MTN": {
            //     "SME": [{"id": 1, "plan": "500MB", "price": 100, "validity": "30 days"}],
            //     "Gifting": [...],
            //     "Corporate": [...]
            //   },
            //   "AIRTEL": {...}
            // }

            foreach ($rawData as $networkName => $planTypes) {
                if (!is_array($planTypes)) {
                    continue;
                }

                $organizedPlans[$networkName] = [
                    'SME' => $planTypes['SME'] ?? $planTypes['sme'] ?? [],
                    'Gifting' => $planTypes['Gifting'] ?? $planTypes['gifting'] ?? [],
                    'Corporate' => $planTypes['Corporate'] ?? $planTypes['corporate'] ?? []
                ];
            }

            Log::info('Uzobest data plans parsed', [
                'networks' => array_keys($organizedPlans)
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
     *
     * @return array Cable provider name to ID mapping
     */
    public function getCableProviderMapping(): array
    {
        return [
            'DSTV' => 1,
            'GOTV' => 2,
            'STARTIMES' => 3,
        ];
    }

    /**
     * Get Uzobest electricity disco provider IDs mapping
     * These IDs need to be determined from Uzobest documentation/testing
     *
     * @return array Disco provider name to ID mapping
     */
    public function getDiscoProviderMapping(): array
    {
        // TODO: Complete this mapping from Uzobest documentation
        return [
            'EKEDC' => 1,
            'IKEDC' => 2,
            'AEDC' => 3,
            'KEDCO' => 4,
            'PHED' => 5,
            'JED' => 6,
            'IBEDC' => 7,
            'KAEDCO' => 8,
            'EEDC' => 9,
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
