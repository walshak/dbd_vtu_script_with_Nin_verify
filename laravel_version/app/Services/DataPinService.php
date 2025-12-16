<?php

namespace App\Services;

use App\Models\DataPin;
use App\Models\Transaction;
use App\Models\User;
use App\Models\NetworkId;
use Illuminate\Support\Facades\Log;
use Exception;

class DataPinService
{
    private ExternalApiService $externalApiService;
    private WalletService $walletService;
    private ConfigurationService $configService;

    public function __construct(
        ExternalApiService $externalApiService,
        WalletService $walletService,
        ConfigurationService $configService
    ) {
        $this->externalApiService = $externalApiService;
        $this->walletService = $walletService;
        $this->configService = $configService;
    }

    /**
     * Get available data pin plans and their prices
     */
    public function getAvailablePlans()
    {
        try {
            $networks = NetworkId::getActive();
            $dataPins = [];

            foreach ($networks as $network) {
                $dataPin = DataPin::getByNetwork($network->sId);
                if ($dataPin) {
                    $dataPins[$network->sName] = [
                        'network_id' => $network->sId,
                        'network_name' => $network->sName,
                        'status' => $network->sStatus,
                        'plans' => DataPin::getAvailablePlans($network->sId),
                        'discounts' => [
                            'user' => $dataPin->aUserDiscount ?? 0,
                            'agent' => $dataPin->aAgentDiscount ?? 0,
                            'vendor' => $dataPin->aVendorDiscount ?? 0
                        ]
                    ];
                }
            }

            return $dataPins;
        } catch (Exception $e) {
            Log::error('Error fetching data pin plans: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Purchase data pin(s) using real external API
     */
    public function purchaseDataPin($userId, $networkId, $planId, $quantity = 1, $cardName = null)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            $network = NetworkId::find($networkId);
            if (!$network || !$network->isActive()) {
                return $this->errorResponse('Network not available');
            }

            $dataPinPrice = DataPin::getByNetworkAndPlan($networkId, $planId);
            if (!$dataPinPrice) {
                return $this->errorResponse('Data pin pricing not available for this plan');
            }

            // Validate quantity
            if ($quantity < 1 || $quantity > 10) {
                return $this->errorResponse('Invalid quantity. Must be between 1 and 10');
            }

            // Calculate total cost
            $unitPrice = $dataPinPrice->calculateAmountToPay($user->sType);
            $totalAmount = $unitPrice * $quantity;

            // Validate user balance
            if ($user->sWalletBalance < $totalAmount) {
                return $this->errorResponse('Insufficient wallet balance');
            }

            // Generate transaction reference
            $reference = Transaction::generateReference('DPIN');

            // Create transaction record
            $transaction = Transaction::recordVtuTransaction(
                $userId,
                Transaction::TYPE_DATA_PIN,
                $totalAmount,
                $user->sPhone,
                $reference,
                $user->sWalletBalance,
                [
                    'network' => $network->sName,
                    'plan_id' => $planId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'card_name' => $cardName
                ]
            );

            // Debit user wallet
            $user->sWalletBalance -= $totalAmount;
            $user->save();

            // Call external API to purchase data pins using ExternalApiService
            $purchaseResult = $this->externalApiService->purchaseDataPin(
                $network->sName,
                $planId,
                $quantity,
                $reference,
                $cardName ?: $user->sFname . ' ' . $user->sLname
            );

            if ($purchaseResult['success']) {
                $transaction->updateStatus(
                    Transaction::STATUS_SUCCESS,
                    $purchaseResult['server_reference'] ?? null,
                    $purchaseResult
                );

                // Calculate and save profit
                $profit = $dataPinPrice->calculateProfit($user->sType);
                $transaction->commission = $profit * $quantity;
                $transaction->save();

                // Log successful purchase
                Log::info('Data pin purchase successful', [
                    'user_id' => $userId,
                    'reference' => $reference,
                    'network' => $network->sName,
                    'plan_id' => $planId,
                    'quantity' => $quantity,
                    'amount' => $totalAmount,
                    'pins_count' => count($purchaseResult['pins'] ?? [])
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Data pin(s) purchased successfully',
                    'data' => [
                        'reference' => $reference,
                        'network' => $network->sName,
                        'plan_id' => $planId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_amount' => $totalAmount,
                        'pins' => $purchaseResult['pins'] ?? [],
                        'instructions' => 'Use these data pins to load data bundles on compatible devices'
                    ]
                ];
            } else {
                // Refund user on failure
                $user->sWalletBalance += $totalAmount;
                $user->save();

                $transaction->updateStatus(Transaction::STATUS_FAILED, null, $purchaseResult);

                Log::error('Data pin purchase failed', [
                    'user_id' => $userId,
                    'reference' => $reference,
                    'network' => $network->sName,
                    'plan_id' => $planId,
                    'quantity' => $quantity,
                    'error' => $purchaseResult['message'] ?? 'Unknown error'
                ]);

                return $this->errorResponse($purchaseResult['message'] ?? 'Data pin purchase failed');
            }

        } catch (Exception $e) {
            Log::error('Data pin purchase error: ' . $e->getMessage());

            // Attempt to refund user if transaction was created
            if (isset($user) && isset($totalAmount)) {
                $user->sWalletBalance += $totalAmount;
                $user->save();
            }

            if (isset($transaction)) {
                $transaction->updateStatus(Transaction::STATUS_FAILED);
            }

            return $this->errorResponse('Unable to complete data pin purchase');
        }
    }

    /**
     * Verify data pin using external API with proper implementation
     */
    public function verifyDataPin($pinCode, $serialNumber = null)
    {
        try {
            // Get API configuration
            $config = $this->configService->getServiceConfig('data_pin');

            if (!$config) {
                return $this->errorResponse('Data pin verification service configuration not found');
            }

            Log::info('Data pin verification attempt', [
                'pin_code' => substr($pinCode, 0, 4) . '****', // Log partial pin for security
                'serial_number' => $serialNumber ? substr($serialNumber, 0, 4) . '****' : null,
                'provider' => $config['provider'] ?? 'unknown'
            ]);

            // Prepare verification request
            $verifyEndpoint = str_replace('/data-pin', '/verify-data-pin', $config['provider']);
            $requestData = [
                'pin_code' => $pinCode
            ];

            if ($serialNumber) {
                $requestData['serial_number'] = $serialNumber;
            }

            // Setup authentication
            $authConfig = [
                'auth_type' => $config['auth_type'] ?? 'Basic',
                'api_key' => $config['api_key'],
                'user_url' => $config['user_url'] ?? null
            ];

            // Make verification request using ExternalApiService
            $result = $this->externalApiService->makeRequest($verifyEndpoint, $requestData, 'POST', [], $authConfig);

            if ($result['success']) {
                $responseData = $result['data'] ?? $result['response'] ?? [];
                $status = $responseData['status'] ?? $responseData['Status'] ?? 'failed';

                if (in_array(strtolower($status), ['success', 'successful', 'valid'])) {
                    return [
                        'status' => 'success',
                        'message' => 'Data pin is valid',
                        'data' => [
                            'pin_code' => $pinCode,
                            'serial_number' => $serialNumber,
                            'network' => $responseData['network'] ?? 'Unknown',
                            'plan_type' => $responseData['plan_type'] ?? 'Unknown',
                            'data_value' => $responseData['data_value'] ?? 'Unknown',
                            'is_valid' => true,
                            'is_used' => $responseData['is_used'] ?? false,
                            'purchase_date' => $responseData['purchase_date'] ?? null,
                            'expiry_date' => $responseData['expiry_date'] ?? null,
                            'verified_at' => now()->toISOString()
                        ]
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => $responseData['message'] ?? $responseData['msg'] ?? 'Invalid data pin',
                        'data' => [
                            'pin_code' => $pinCode,
                            'serial_number' => $serialNumber,
                            'is_valid' => false
                        ]
                    ];
                }
            } else {
                // If verification endpoint is not available, return informative message
                return [
                    'status' => 'info',
                    'message' => 'Data pin verification endpoint not available from provider. Pin may still be valid for usage.',
                    'data' => [
                        'pin_code' => $pinCode,
                        'serial_number' => $serialNumber,
                        'verification_attempted' => true,
                        'provider_response' => $result['message'] ?? 'No verification endpoint',
                        'usage_instructions' => 'Load the pin using the network-specific USSD code or mobile app'
                    ]
                ];
            }

        } catch (Exception $e) {
            Log::error('Data pin verification error: ' . $e->getMessage());
            return $this->errorResponse('Unable to verify data pin at this time');
        }
    }

    /**
     * Get transaction history for data pins
     */
    public function getDataPinHistory($userId, $limit = 20)
    {
        try {
            $transactions = Transaction::where('sUser', $userId)
                ->where('sType', Transaction::TYPE_DATA_PIN)
                ->orderBy('sDate', 'desc')
                ->limit($limit)
                ->get();

            return [
                'status' => 'success',
                'data' => $transactions->map(function ($transaction) {
                    return [
                        'reference' => $transaction->sReference,
                        'network' => $transaction->sDesc ?? 'Unknown',
                        'amount' => $transaction->sAmount,
                        'status' => $transaction->sStatus,
                        'date' => $transaction->sDate,
                        'details' => json_decode($transaction->api_response, true)
                    ];
                })
            ];

        } catch (Exception $e) {
            Log::error('Data pin history error: ' . $e->getMessage());
            return $this->errorResponse('Unable to fetch data pin history');
        }
    }

    /**
     * Print data pin receipt
     */
    public function printReceipt($transactionReference)
    {
        try {
            $transaction = Transaction::where('sReference', $transactionReference)->first();

            if (!$transaction) {
                return $this->errorResponse('Transaction not found');
            }

            $apiResponse = json_decode($transaction->api_response, true);
            $pins = $apiResponse['pins'] ?? [];

            if (empty($pins)) {
                return $this->errorResponse('No pins found for this transaction');
            }

            return [
                'status' => 'success',
                'data' => [
                    'reference' => $transaction->sReference,
                    'date' => $transaction->sDate,
                    'network' => $transaction->sDesc,
                    'amount' => $transaction->sAmount,
                    'pins' => $pins,
                    'print_time' => now()->format('Y-m-d H:i:s')
                ]
            ];

        } catch (Exception $e) {
            Log::error('Data pin receipt error: ' . $e->getMessage());
            return $this->errorResponse('Unable to generate receipt');
        }
    }

    /**
     * Return error response
     */
    private function errorResponse($message)
    {
        return [
            'status' => 'error',
            'message' => $message
        ];
    }
}
