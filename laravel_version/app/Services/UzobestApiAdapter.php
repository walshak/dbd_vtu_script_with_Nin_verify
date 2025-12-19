<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Adapter class to transform requests/responses between internal format and Uzobest API format
 */
class UzobestApiAdapter
{
    /**
     * Network name to Uzobest network ID mapping
     * These IDs should be fetched from /api/network/ endpoint and cached
     */
    private const NETWORK_MAP = [
        'MTN' => 1,
        'AIRTEL' => 4,
        'GLO' => 2,
        '9MOBILE' => 3,
    ];

    /**
     * Airtime type mapping
     */
    private const AIRTIME_TYPE_MAP = [
        'VTU' => 'VTU',
        'ShareSell' => 'Share and Sell',
        'Share and Sell' => 'Share and Sell',
        'awuf4U' => 'awuf4U',
    ];

    /**
     * Transform data purchase request to Uzobest format
     *
     * @param string $network Network name (MTN, AIRTEL, etc.)
     * @param string $phone Phone number
     * @param string $planId Internal plan ID (needs mapping to Uzobest plan ID)
     * @param bool $portedNumber Whether number is ported
     * @return array Uzobest API request format
     */
    public function transformDataPurchaseRequest(string $network, string $phone, string $planId, bool $portedNumber = false): array
    {
        return [
            'network' => $this->getNetworkId($network),
            'mobile_number' => $this->formatPhoneNumber($phone),
            'plan' => (int) $planId, // Uzobest plan ID
            'Ported_number' => $portedNumber,
        ];
    }

    /**
     * Transform airtime purchase request to Uzobest format
     *
     * @param string $network Network name
     * @param string $phone Phone number
     * @param float $amount Amount
     * @param string $airtimeType VTU, ShareSell, awuf4U
     * @param bool $portedNumber Whether number is ported
     * @return array Uzobest API request format
     */
    public function transformAirtimePurchaseRequest(string $network, string $phone, float $amount, string $airtimeType = 'VTU', bool $portedNumber = false): array
    {
        return [
            'network' => $this->getNetworkId($network),
            'amount' => $amount,
            'mobile_number' => $this->formatPhoneNumber($phone),
            'Ported_number' => $portedNumber,
            'airtime_type' => $this->getAirtimeType($airtimeType),
        ];
    }

    /**
     * Transform cable purchase request to Uzobest format
     *
     * IMPORTANT: Uzobest expects numeric plan IDs, not string identifiers
     * If planId is a string like "dstv-padi", it should be converted to the
     * actual numeric Uzobest plan ID. Common patterns:
     * - DSTV plans: typically IDs 1-10
     * - GOTV plans: typically IDs 1-10
     * - STARTIMES plans: typically IDs 1-10
     *
     * @param string $cableProvider Cable provider (dstv, gotv, startimes)
     * @param string $iucNumber IUC/Smart card number
     * @param string|int $planId Cable plan ID (should be numeric Uzobest ID)
     * @return array Uzobest API request format
     */
    public function transformCablePurchaseRequest(string $cableProvider, string $iucNumber, $planId): array
    {
        // If planId is a string identifier, try to extract numeric portion
        // Otherwise use it as-is and cast to int
        if (is_string($planId) && !is_numeric($planId)) {
            // Log warning that non-numeric plan ID is being used
            \Log::warning('Cable plan ID is not numeric', [
                'provider' => $cableProvider,
                'plan_id' => $planId,
                'note' => 'Uzobest expects numeric plan IDs. Please update uzobest_plan_id in cable_plans table.'
            ]);
        }

        return [
            'cablename' => (int) $this->getCableProviderId($cableProvider),
            'cableplan' => is_numeric($planId) ? (int) $planId : $planId,
            'smart_card_number' => $iucNumber,
        ];
    }

    /**
     * Transform electricity purchase request to Uzobest format
     *
     * @param string $discoProvider Disco provider name
     * @param string $meterNumber Meter number
     * @param string $meterType PREPAID or POSTPAID
     * @param float $amount Amount
     * @return array Uzobest API request format
     */
    public function transformElectricityPurchaseRequest(string $discoProvider, string $meterNumber, string $meterType, float $amount): array
    {
        return [
            'disco_name' => strtolower($discoProvider), // Uzobest expects lowercase string (e.g., "ekedc")
            'amount' => $amount,
            'meter_number' => $meterNumber,
            'MeterType' => $this->getMeterTypeId($meterType),
        ];
    }

    /**
     * Parse Uzobest API response
     *
     * @param array $response Raw API response
     * @return array Standardized response
     */
    public function parseResponse(array $response): array
    {
        // Uzobest typically returns success responses with transaction details
        // Check for common success indicators
        if (isset($response['Status']) && strtolower($response['Status']) === 'successful') {
            return [
                'success' => true,
                'message' => $response['msg'] ?? 'Transaction successful',
                'transaction_id' => $response['id'] ?? $response['ident'] ?? null,
                'data' => $response,
            ];
        }

        // Check for error responses
        if (isset($response['detail']) || isset($response['error'])) {
            return [
                'success' => false,
                'message' => $response['detail'] ?? $response['error'] ?? 'Transaction failed',
                'error_code' => 'API_ERROR',
                'data' => $response,
            ];
        }

        // Default to success if no error indicators
        return [
            'success' => true,
            'message' => 'Transaction completed',
            'data' => $response,
        ];
    }

    /**
     * Get Uzobest network ID from network name
     */
    private function getNetworkId(string $network): int
    {
        $networkUpper = strtoupper($network);

        if (!isset(self::NETWORK_MAP[$networkUpper])) {
            Log::warning("Unknown network: {$network}, defaulting to MTN");
            return self::NETWORK_MAP['MTN'];
        }

        return self::NETWORK_MAP[$networkUpper];
    }

    /**
     * Get Uzobest airtime type
     * Ensures valid type is always returned (defaults to VTU)
     */
    private function getAirtimeType(string $type): string
    {
        // Handle empty or null type
        if (empty($type)) {
            return 'VTU';
        }

        // Map the type, default to VTU if not found
        return self::AIRTIME_TYPE_MAP[$type] ?? 'VTU';
    }

    /**
     * Format phone number for Uzobest API
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove country code and normalize
        $phone = preg_replace('/^\+?234/', '0', $phone);
        $phone = preg_replace('/^0+/', '0', $phone);

        return $phone;
    }

    /**
     * Get cable provider ID (public for external access)
     */
    public function getCableProviderId(string $provider): int
    {
        $map = [
            'DSTV' => 1,
            'GOTV' => 2,
            'STARTIMES' => 3,
        ];

        return $map[strtoupper($provider)] ?? 1;
    }

    /**
     * Get disco provider name for meter validation
     * Uzobest expects full disco names: "Ikeja Electric", "Jos Electric", etc.
     */
    public function getDiscoProviderId(string $provider): string
    {
        // Map provider codes to Uzobest disco names (official mapping)
        $mapping = [
            'IKEDC' => 'Ikeja Electric',
            'EKEDC' => 'Eko Electric',
            'AEDC' => 'Abuja Electric',
            'KEDCO' => 'Kano Electric',
            'EEDC' => 'Enugu Electric',
            'PHED' => 'Port Harcourt Electric',
            'IBEDC' => 'Ibadan Electric',
            'KAEDCO' => 'Kaduna Electric',
            'JED' => 'Jos Electric',
            'BEDC' => 'Benin Electric',
            'YEDC' => 'Yola Electric',
            'ABA' => 'Aba Electric',
        ];

        $providerUpper = strtoupper($provider);
        return $mapping[$providerUpper] ?? $provider;
    }

    /**
     * Get meter type string for Uzobest API
     * Uzobest expects: "PREPAID" or "POSTPAID" (strings, not numeric)
     */
    public function getMeterTypeString(string $type): string
    {
        return strtoupper($type) === 'PREPAID' ? 'PREPAID' : 'POSTPAID';
    }
}
