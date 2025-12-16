<?php

namespace App\Services;

use App\Models\Airtime;
use App\Models\NetworkId;
use App\Models\ApiConfig;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AirtimeService
{
    protected $walletService;
    protected $externalApiService;

    public function __construct(WalletService $walletService, ExternalApiService $externalApiService)
    {
        $this->walletService = $walletService;
        $this->externalApiService = $externalApiService;
    }

    /**
     * Purchase airtime for a user (compatible with old PHP app)
     */
    public function purchaseAirtime($userId, $network, $phone, $amount, $airtimeType = 'VTU', $portedNumber = false)
    {
        try {
            // Validate user
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            // Validate network
            $networkDetails = NetworkId::getByName($network);
            if (!$networkDetails) {
                return $this->errorResponse('Invalid network selected');
            }

            // Get airtime configuration
            $airtimeConfig = Airtime::getByNetworkAndType($networkDetails->nId, $airtimeType);
            if (!$airtimeConfig) {
                return $this->errorResponse('Airtime service not available for this network');
            }

            // Calculate final amount after discount
            $finalAmount = $airtimeConfig->calculateFinalAmount($amount, $user->sType);
            $profit = $amount - $finalAmount; // Calculate profit

            // Check for duplicate transaction
            if (Transaction::checkDuplicate($userId, Transaction::SERVICE_AIRTIME, $finalAmount, 60)) {
                return $this->errorResponse('Duplicate transaction detected. Please wait before retrying.');
            }

            // Check user balance
            if (!$this->walletService->hasSufficientBalance($userId, $finalAmount)) {
                return $this->errorResponse('Insufficient wallet balance');
            }

            // Create description compatible with old PHP app
            $description = "Purchase of {$network} ₦{$amount} airtime for phone number {$phone}";

            // Debit wallet first
            $walletResult = $this->walletService->debitWallet(
                $userId,
                $finalAmount,
                Transaction::SERVICE_AIRTIME,
                $description,
                $profit
            );

            if ($walletResult['status'] !== 'success') {
                return $this->errorResponse($walletResult['message']);
            }

            $transaction = Transaction::find($walletResult['data']['transaction_id']);

            // Process airtime purchase via API
            $apiResponse = $this->processAirtimeAPI($networkDetails, $airtimeType, $phone, $amount, $transaction->transref, $portedNumber);

            if ($apiResponse['status'] === 'success') {
                // Transaction already marked as successful by WalletService
                return $this->successResponse('Airtime purchase successful', [
                    'transaction_id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'amount' => $amount,
                    'final_amount' => $finalAmount,
                    'phone' => $phone,
                    'network' => strtoupper($network),
                    'balance' => $walletResult['data']['new_balance'],
                    'profit' => $profit
                ]);
            } else {
                // API failed, reverse the transaction
                $this->walletService->reverseTransaction(
                    $transaction->tId,
                    'API failure: ' . ($apiResponse['message'] ?? 'Unknown error')
                );

                return $this->errorResponse($apiResponse['message'] ?? 'Airtime purchase failed');
            }
        } catch (Exception $e) {
            Log::error('Airtime Purchase Error: ' . $e->getMessage());
            return $this->errorResponse('Airtime purchase failed. Please try again.');
        }
    }

    /**
     * Process airtime purchase via API
     */
    protected function processAirtimeAPI($networkDetails, $airtimeType, $phone, $amount, $reference, $portedNumber = false)
    {
        try {
            // Use the real external API service
            $result = $this->externalApiService->purchaseAirtime(
                $networkDetails->network,
                $phone,
                $amount,
                $airtimeType,
                $portedNumber // Pass ported number flag to API
            );

            if ($result['success']) {
                return [
                    'status' => 'success',
                    'server_ref' => $result['transaction_id'] ?? $reference,
                    'response' => json_encode($result['api_response'] ?? [])
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Airtime purchase failed',
                    'error_code' => $result['error_code'] ?? 'API_ERROR'
                ];
            }
        } catch (Exception $e) {
            Log::error('Airtime API Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'API service temporarily unavailable'
            ];
        }
    }

    /**
     * Get discount rates for user
     */
    public function getDiscountRates($userId, $network)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $networkDetails = NetworkId::getByName($network);
        if (!$networkDetails) {
            return null;
        }

        $airtimeConfig = Airtime::where('nId', $networkDetails->nId)->first();
        if (!$airtimeConfig) {
            return null;
        }

        return [
            'vtu_discount' => $airtimeConfig->getDiscount($user->sType, 'VTU'),
            'share_and_sell_discount' => $airtimeConfig->getDiscount($user->sType, 'Share and Sell')
        ];
    }

    /**
     * Get airtime pricing information
     */
    public function getAirtimePricing($userId, $network)
    {
        $discountRates = $this->getDiscountRates($userId, $network);

        if (!$discountRates) {
            return [
                'status' => 'error',
                'message' => 'Pricing not available for this network'
            ];
        }

        return [
            'status' => 'success',
            'network' => strtoupper($network),
            'vtu_discount' => $discountRates['vtu_discount'],
            'share_and_sell_discount' => $discountRates['share_and_sell_discount'],
            'minimum_amount' => 50,
            'maximum_amount' => 10000
        ];
    }

    /**
     * Get airtime transaction history (compatible with old PHP app)
     */
    public function getAirtimeHistory($userId, $limit = 50)
    {
        return Transaction::where('sId', $userId)
            ->where('servicename', Transaction::SERVICE_AIRTIME)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'amount' => $transaction->amount,
                    'phone' => $this->extractPhoneFromDescription($transaction->servicedesc),
                    'network' => $this->extractNetworkFromDescription($transaction->servicedesc),
                    'status' => $transaction->status_text,
                    'date' => $transaction->formatted_date,
                    'profit' => $transaction->profit
                ];
            });
    }

    /**
     * Extract phone number from transaction description
     */
    private function extractPhoneFromDescription($description)
    {
        preg_match('/\b\d{11}\b/', $description, $matches);
        return $matches[0] ?? 'N/A';
    }

    /**
     * Extract network from transaction description
     */
    private function extractNetworkFromDescription($description)
    {
        if (stripos($description, 'MTN') !== false) return 'MTN';
        if (stripos($description, 'GLO') !== false) return 'GLO';
        if (stripos($description, 'AIRTEL') !== false) return 'AIRTEL';
        if (stripos($description, '9MOBILE') !== false) return '9MOBILE';
        return 'Unknown';
    }

    /**
     * Success response helper
     */
    protected function successResponse($message, $data = [])
    {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Error response helper
     */
    protected function errorResponse($message, $data = [])
    {
        return [
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ];
    }
}
