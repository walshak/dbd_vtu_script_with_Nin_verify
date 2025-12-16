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
     * @param string $cableProvider Cable provider name
     * @param string $iucNumber IUC/Smartcard number
     * @param string $planId Cable plan ID
     * @return array Uzobest API request format
     */
    public function transformCablePurchaseRequest(string $cableProvider, string $iucNumber, string $planId): array
    {
        return [
            'cablename' => (int) $this->getCableProviderId($cableProvider),
            'cableplan' => (int) $planId,
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
            'disco_name' => (int) $this->getDiscoProviderId($discoProvider),
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
     * Get disco provider ID (public for external access)
     */
    public function getDiscoProviderId(string $provider): int
    {
        // These IDs need to be determined from Uzobest documentation
        $map = [
            'IKEDC' => 1,
            'EKEDC' => 2,
            'AEDC' => 3,
            'PHED' => 4,
            'JED' => 5,
            'IBEDC' => 6,
            'KAEDCO' => 7,
            'KEDCO' => 8,
        ];

        return $map[strtoupper($provider)] ?? 1;
    }

    /**
     * Get meter type ID
     */
    private function getMeterTypeId(string $type): int
    {
        return strtoupper($type) === 'PREPAID' ? 1 : 2;
    }
}
