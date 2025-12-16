<?php

namespace App\Services;

use App\Models\ElectricityProvider;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ApiConfig;
use App\Services\WalletService;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Log;
use Exception;

class ElectricityService
{
    protected $walletService;
    protected $externalApiService;

    public function __construct(WalletService $walletService, ExternalApiService $externalApiService)
    {
        $this->walletService = $walletService;
        $this->externalApiService = $externalApiService;
    }

    /**
     * Purchase electricity token (compatible with old PHP app)
     */
    public function purchaseElectricity($userId, $providerName, $meterNumber, $amount, $meterType = 'prepaid')
    {
        try {
            // Validate user
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            // Validate electricity provider
            $provider = ElectricityProvider::getByPlan($providerName);
            if (!$provider) {
                return $this->errorResponse('Electricity provider not found');
            }

            if (!$provider->isActive()) {
                return $this->errorResponse('Electricity provider is currently unavailable');
            }

            // Basic meter number validation
            if (!ElectricityProvider::validateMeterNumber($meterNumber)) {
                return $this->errorResponse('Invalid meter number format');
            }

            // Calculate final amount after service charge
            $finalAmount = $provider->calculateFinalAmount($amount, $user->sType);
            $profit = $provider->calculateProfit($amount, $finalAmount); // Calculate profit

            // Validate meter number first
            $validationResult = $this->validateMeterNumber($providerName, $meterNumber, $meterType);
            if ($validationResult['status'] !== 'success') {
                return $this->errorResponse($validationResult['message']);
            }

            $customerName = $validationResult['data']['customer_name'];

            // Check for duplicate transaction
            if (Transaction::checkDuplicate($userId, Transaction::SERVICE_ELECTRICITY, $finalAmount, 60)) {
                return $this->errorResponse('Duplicate transaction detected. Please wait before retrying.');
            }

            // Check user balance
            if (!$this->walletService->hasSufficientBalance($userId, $finalAmount)) {
                return $this->errorResponse('Insufficient wallet balance');
            }

            // Create description compatible with old PHP app
            $description = "Purchase of ₦{$amount} {$providerName} electricity token for meter {$meterNumber} ({$customerName})";

            // Debit wallet first
            $walletResult = $this->walletService->debitWallet(
                $userId,
                $finalAmount,
                Transaction::SERVICE_ELECTRICITY,
                $description,
                $profit
            );

            if ($walletResult['status'] !== 'success') {
                return $this->errorResponse($walletResult['message']);
            }

            $transaction = Transaction::find($walletResult['data']['transaction_id']);

            // Process electricity purchase via API
            $apiResponse = $this->processElectricityAPI($provider, $meterNumber, $amount, $transaction->transref, $meterType, $customerName);

            if ($apiResponse['status'] === 'success') {
                // Transaction already marked as successful by WalletService
                return $this->successResponse('Electricity token purchase successful', [
                    'transaction_id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'provider' => strtoupper($providerName),
                    'amount' => $amount,
                    'final_amount' => $finalAmount,
                    'meter_number' => $meterNumber,
                    'customer_name' => $customerName,
                    'token' => $apiResponse['token'] ?? 'Check SMS',
                    'units' => $apiResponse['units'] ?? 'N/A',
                    'balance' => $walletResult['data']['new_balance'],
                    'profit' => $profit
                ]);
            } else {
                // API failed, reverse the transaction
                $this->walletService->reverseTransaction(
                    $transaction->tId,
                    'API failure: ' . ($apiResponse['message'] ?? 'Unknown error')
                );

                return $this->errorResponse($apiResponse['message'] ?? 'Electricity token purchase failed');
            }
        } catch (Exception $e) {
            Log::error('Electricity Purchase Error: ' . $e->getMessage());
            return $this->errorResponse('Electricity token purchase failed. Please try again.');
        }
    }

    /**
     * Validate meter number using external API service
     */
    public function validateMeterNumber($providerName, $meterNumber, $meterType = 'prepaid')
    {
        try {
            $provider = ElectricityProvider::getByPlan($providerName);

            if (!$provider) {
                return $this->errorResponse('Electricity provider not found');
            }

            if (!$provider->isActive()) {
                return $this->errorResponse('Electricity provider is currently unavailable');
            }

            // Basic meter number validation
            if (!ElectricityProvider::validateMeterNumber($meterNumber)) {
                return $this->errorResponse('Invalid meter number format');
            }

            // Use external API service for meter verification
            $result = $this->externalApiService->verifyMeter($meterNumber, $meterType, $providerName);

            if ($result['success']) {
                return [
                    'status' => 'success',
                    'data' => [
                        'meter_number' => $meterNumber,
                        'customer_name' => $result['customer_name'] ?? 'Unknown',
                        'meter_type' => $meterType,
                        'provider' => strtoupper($providerName)
                    ]
                ];
            } else {
                return $this->errorResponse($result['message'] ?? 'Meter validation failed');
            }
        } catch (Exception $e) {
            Log::error('Meter Validation Error: ' . $e->getMessage());
            return $this->errorResponse('Meter validation service temporarily unavailable');
        }
    }

    /**
     * Process electricity purchase via external API service
     */
    protected function processElectricityAPI($provider, $meterNumber, $amount, $reference, $meterType, $customerName)
    {
        try {
            // Use external API service for electricity purchase
            $result = $this->externalApiService->purchaseElectricity(
                $provider->provider_code,
                $meterNumber,
                $meterType,
                $amount,
                $reference
            );

            if ($result['success']) {
                return [
                    'status' => 'success',
                    'token' => $result['token'] ?? 'Check SMS',
                    'units' => $result['units'] ?? 'N/A',
                    'server_ref' => $result['server_reference'] ?? $reference,
                    'message' => $result['message'] ?? 'Electricity purchase successful'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Electricity purchase failed'
                ];
            }
        } catch (Exception $e) {
            Log::error('Electricity API Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'API service temporarily unavailable'
            ];
        }
    }

    /**
     * Get available electricity providers
     */
    public function getAvailableProviders()
    {
        try {
            $providers = ElectricityProvider::getActiveProviders();

            $formattedProviders = $providers->map(function ($provider) {
                return [
                    'code' => $provider->provider_code,
                    'name' => $provider->provider_name,
                    'minimum_amount' => $provider->minimum_amount ?? 1000,
                    'maximum_amount' => $provider->maximum_amount ?? 50000,
                    'service_charge' => $provider->service_charge ?? 0,
                    'available' => $provider->isActive()
                ];
            });

            return $this->successResponse('Electricity providers retrieved successfully', [
                'providers' => $formattedProviders
            ]);
        } catch (Exception $e) {
            Log::error('Get Electricity Providers Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve electricity providers');
        }
    }

    /**
     * Get electricity transaction history (compatible with old PHP app)
     */
    public function getElectricityHistory($userId, $limit = 50)
    {
        return Transaction::where('sId', $userId)
            ->where('servicename', Transaction::SERVICE_ELECTRICITY)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'amount' => $transaction->amount,
                    'meter_number' => $this->extractMeterFromDescription($transaction->servicedesc),
                    'provider' => $this->extractProviderFromDescription($transaction->servicedesc),
                    'customer_name' => $this->extractCustomerFromDescription($transaction->servicedesc),
                    'status' => $transaction->status_text,
                    'date' => $transaction->formatted_date,
                    'profit' => $transaction->profit
                ];
            });
    }

    /**
     * Extract meter number from transaction description
     */
    private function extractMeterFromDescription($description)
    {
        preg_match('/meter\s+(\d+)/', $description, $matches);
        return $matches[1] ?? 'N/A';
    }

    /**
     * Extract provider from transaction description
     */
    private function extractProviderFromDescription($description)
    {
        if (stripos($description, 'AEDC') !== false) return 'AEDC';
        if (stripos($description, 'EKEDC') !== false) return 'EKEDC';
        if (stripos($description, 'IKEDC') !== false) return 'IKEDC';
        if (stripos($description, 'KEDCO') !== false) return 'KEDCO';
        return 'Unknown';
    }

    /**
     * Extract customer name from transaction description
     */
    private function extractCustomerFromDescription($description)
    {
        preg_match('/\((.+?)\)$/', $description, $matches);
        return $matches[1] ?? 'N/A';
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
