<?php

namespace App\Services;

use App\Models\RechargePin;
use App\Models\Transaction;
use App\Models\User;
use App\Models\NetworkId;
use Illuminate\Support\Facades\Log;
use Exception;

class RechargePinService
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
     * Get available recharge pin networks and their prices
     */
    public function getAvailableNetworks()
    {
        try {
            $networks = NetworkId::getActive();
            $rechargePins = [];

            foreach ($networks as $network) {
                $rechargePin = RechargePin::getByNetwork($network->sId);
                if ($rechargePin) {
                    $rechargePins[$network->sName] = [
                        'network_id' => $network->sId,
                        'network_name' => $network->sName,
                        'status' => $network->sStatus,
                        'denominations' => RechargePin::getAvailableDenominations(),
                        'discounts' => [
                            'user' => $rechargePin->aUserDiscount,
                            'agent' => $rechargePin->aAgentDiscount,
                            'vendor' => $rechargePin->aVendorDiscount
                        ]
                    ];
                }
            }

            return $rechargePins;
        } catch (Exception $e) {
            Log::error('Error fetching recharge pin networks: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Purchase recharge pin(s) using real external API
     */
    public function purchaseRechargePin($userId, $networkId, $denomination, $quantity = 1, $nameOnCard = null)
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

            $rechargePinPrice = RechargePin::getByNetwork($networkId);
            if (!$rechargePinPrice) {
                return $this->errorResponse('Recharge pin pricing not available for this network');
            }

            // Validate denomination
            if (!RechargePin::validateAmount($denomination)) {
                return $this->errorResponse('Invalid denomination. Available: ' . implode(', ', RechargePin::getAvailableDenominations()));
            }

            // Validate quantity
            if ($quantity < 1 || $quantity > 20) {
                return $this->errorResponse('Invalid quantity. Must be between 1 and 20');
            }

            // Calculate total cost
            $unitPrice = $rechargePinPrice->calculateAmountToPay($denomination, $user->sType);
            $totalAmount = $unitPrice * $quantity;

            // Validate user balance
            if ($user->sWalletBalance < $totalAmount) {
                return $this->errorResponse('Insufficient wallet balance');
            }

            // Generate transaction reference
            $reference = Transaction::generateReference('RPIN');

            // Create transaction record
            $transaction = Transaction::recordVtuTransaction(
                $userId,
                Transaction::TYPE_RECHARGE_PIN,
                $totalAmount,
                $user->sPhone,
                $reference,
                $user->sWalletBalance,
                [
                    'network' => $network->sName,
                    'denomination' => $denomination,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'name_on_card' => $nameOnCard
                ]
            );

            // Debit user wallet
            $user->sWalletBalance -= $totalAmount;
            $user->save();

            // Call external API to purchase recharge pins using ExternalApiService
            $purchaseResult = $this->externalApiService->purchaseRechargePin(
                $network->sName,
                $denomination,
                $quantity,
                $reference,
                $nameOnCard ?: $user->sFname . ' ' . $user->sLname
            );

            if ($purchaseResult['success']) {
                $transaction->updateStatus(
                    Transaction::STATUS_SUCCESS,
                    $purchaseResult['server_reference'] ?? null,
                    $purchaseResult
                );

                // Calculate and save profit
                $originalAmount = $denomination * $quantity;
                $profit = $originalAmount - $totalAmount;
                $transaction->commission = $profit;
                $transaction->save();

                // Log successful purchase
                Log::info('Recharge pin purchase successful', [
                    'user_id' => $userId,
                    'reference' => $reference,
                    'network' => $network->sName,
                    'denomination' => $denomination,
                    'quantity' => $quantity,
                    'amount' => $totalAmount,
                    'pins_count' => count($purchaseResult['pins'] ?? [])
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Recharge pin(s) purchased successfully',
                    'data' => [
                        'reference' => $reference,
                        'network' => $network->sName,
                        'denomination' => $denomination,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_amount' => $totalAmount,
                        'pins' => $purchaseResult['pins'] ?? [],
                        'instructions' => 'Scratch to reveal PIN and Serial numbers. Load with *888*PIN# for MTN, etc.'
                    ]
                ];
            } else {
                // Refund user on failure
                $user->sWalletBalance += $totalAmount;
                $user->save();

                $transaction->updateStatus(Transaction::STATUS_FAILED, null, $purchaseResult);

                Log::error('Recharge pin purchase failed', [
                    'user_id' => $userId,
                    'reference' => $reference,
                    'network' => $network->sName,
                    'denomination' => $denomination,
                    'quantity' => $quantity,
                    'error' => $purchaseResult['message'] ?? 'Unknown error'
                ]);

                return $this->errorResponse($purchaseResult['message'] ?? 'Recharge pin purchase failed');
            }
        } catch (Exception $e) {
            Log::error('Recharge pin purchase error: ' . $e->getMessage());

            // Attempt to refund user if transaction was created
            if (isset($user) && isset($totalAmount)) {
                $user->sWalletBalance += $totalAmount;
                $user->save();
            }

            if (isset($transaction)) {
                $transaction->updateStatus(Transaction::STATUS_FAILED);
            }

            return $this->errorResponse('Unable to complete recharge pin purchase');
        }
    }

    /**
     * Verify recharge pin using external API with proper implementation
     */
    public function verifyRechargePin($pinCode, $serialNumber = null)
    {
        try {
            // Get API configuration
            $config = $this->configService->getServiceConfig('recharge_pin');

            if (!$config) {
                return $this->errorResponse('Recharge pin verification service configuration not found');
            }

            Log::info('Recharge pin verification attempt', [
                'pin_code' => substr($pinCode, 0, 4) . '****', // Log partial pin for security
                'serial_number' => $serialNumber ? substr($serialNumber, 0, 4) . '****' : null,
                'provider' => $config['provider'] ?? 'unknown'
            ]);

            // Prepare verification request
            $verifyEndpoint = str_replace('/recharge-pin', '/verify-recharge-pin', $config['provider']);
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
                        'message' => 'Recharge pin is valid',
                        'data' => [
                            'pin_code' => $pinCode,
                            'serial_number' => $serialNumber,
                            'network' => $responseData['network'] ?? 'Unknown',
                            'denomination' => $responseData['denomination'] ?? 'Unknown',
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
                        'message' => $responseData['message'] ?? $responseData['msg'] ?? 'Invalid recharge pin',
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
                    'message' => 'Recharge pin verification endpoint not available from provider. Pin may still be valid for loading.',
                    'data' => [
                        'pin_code' => $pinCode,
                        'serial_number' => $serialNumber,
                        'verification_attempted' => true,
                        'provider_response' => $result['message'] ?? 'No verification endpoint',
                        'loading_instructions' => 'Use *888*PIN# for MTN, *126*PIN# for Airtel/GLO, *222*PIN# for 9Mobile'
                    ]
                ];
            }

        } catch (Exception $e) {
            Log::error('Recharge pin verification error: ' . $e->getMessage());
            return $this->errorResponse('Unable to verify recharge pin at this time');
        }
    }

    /**
     * Get transaction history for recharge pins
     */
    public function getRechargePinHistory($userId, $limit = 20)
    {
        try {
            $transactions = Transaction::where('sUser', $userId)
                ->where('sType', Transaction::TYPE_RECHARGE_PIN)
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
            Log::error('Recharge pin history error: ' . $e->getMessage());
            return $this->errorResponse('Unable to fetch recharge pin history');
        }
    }

    /**
     * Print recharge pin receipt
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
            Log::error('Recharge pin receipt error: ' . $e->getMessage());
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
