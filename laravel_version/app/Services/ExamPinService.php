<?php

namespace App\Services;

use App\Models\ExamPin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class ExamPinService
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
     * Get available exam types and their prices
     */
    public function getExamTypes()
    {
        try {
            return ExamPin::getActiveExamTypes();
        } catch (Exception $e) {
            Log::error('Error fetching exam types: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Purchase exam pin(s) using real external API
     */
    public function purchaseExamPin($userId, $examType, $phone = null, $quantity = 1)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            $examPin = ExamPin::getByExamType($examType);
            if (!$examPin || !$examPin->isActive()) {
                return $this->errorResponse('Exam type not available');
            }

            // Validate quantity
            if ($quantity < 1 || $quantity > 50) {
                return $this->errorResponse('Invalid quantity. Must be between 1 and 50');
            }

            // Use provided phone or user's default phone
            $recipientPhone = $phone ?: $user->sPhone;

            // Calculate total cost
            $unitPrice = $examPin->getUserPrice($user->sType);
            $totalAmount = $unitPrice * $quantity;

            // Validate user balance
            if ($user->sWalletBalance < $totalAmount) {
                return $this->errorResponse('Insufficient wallet balance');
            }

            // Generate transaction reference
            $reference = Transaction::generateReference('EXAM');

            // Create transaction record
            $transaction = Transaction::recordVtuTransaction(
                $userId,
                Transaction::TYPE_EXAM_PIN,
                $totalAmount,
                $recipientPhone,
                $reference,
                $user->sWalletBalance,
                [
                    'exam_type' => $examType,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'phone' => $recipientPhone
                ]
            );

            // Debit user wallet
            $user->sWalletBalance -= $totalAmount;
            $user->save();

            // Call external API to purchase exam pins using ExternalApiService
            $purchaseResult = $this->externalApiService->purchaseExamPin(
                $examType,
                $quantity,
                $reference,
                $recipientPhone
            );

            if ($purchaseResult['success']) {
                $transaction->updateStatus(
                    Transaction::STATUS_SUCCESS,
                    $purchaseResult['server_reference'] ?? null,
                    $purchaseResult
                );

                // Calculate and save profit
                $profit = $examPin->calculateProfit($user->sType);
                $transaction->commission = $profit * $quantity;
                $transaction->save();

                // Log successful purchase
                Log::info('Exam pin purchase successful', [
                    'user_id' => $userId,
                    'reference' => $reference,
                    'exam_type' => $examType,
                    'quantity' => $quantity,
                    'amount' => $totalAmount,
                    'pins_count' => count($purchaseResult['pins'] ?? [])
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Exam pin(s) purchased successfully',
                    'data' => [
                        'reference' => $reference,
                        'provider' => $examType,
                        'quantity' => $quantity,
                        'amount' => $totalAmount,
                        'phone' => $recipientPhone,
                        'balance' => $user->sWalletBalance,
                        'pins' => $purchaseResult['pins'] ?? [],
                        'instructions' => 'Use the generated pins for your exam registration'
                    ]
                ];
            } else {
                // Refund user on failure
                $user->sWalletBalance += $totalAmount;
                $user->save();

                $transaction->updateStatus(Transaction::STATUS_FAILED, null, $purchaseResult);

                Log::error('Exam pin purchase failed', [
                    'user_id' => $userId,
                    'reference' => $reference,
                    'exam_type' => $examType,
                    'quantity' => $quantity,
                    'error' => $purchaseResult['message'] ?? 'Unknown error'
                ]);

                return $this->errorResponse($purchaseResult['message'] ?? 'Exam pin purchase failed');
            }

        } catch (Exception $e) {
            Log::error('Exam pin purchase error: ' . $e->getMessage());

            // Attempt to refund user if transaction was created
            if (isset($user) && isset($totalAmount)) {
                $user->sWalletBalance += $totalAmount;
                $user->save();
            }

            if (isset($transaction)) {
                $transaction->updateStatus(Transaction::STATUS_FAILED);
            }

            return $this->errorResponse('Unable to complete exam pin purchase');
        }
    }

    /**
     * Verify exam pin using external API with proper implementation
     */
    public function verifyExamPin($pinCode, $examType = null)
    {
        try {
            // Get API configuration
            $config = $this->configService->getServiceConfig('exam');

            if (!$config) {
                return $this->errorResponse('Exam pin verification service configuration not found');
            }

            Log::info('Exam pin verification attempt', [
                'pin_code' => substr($pinCode, 0, 4) . '****', // Log partial pin for security
                'exam_type' => $examType,
                'provider' => $config['provider'] ?? 'unknown'
            ]);

            // Prepare verification request
            $verifyEndpoint = str_replace('/exam', '/verify-exam', $config['provider']);
            $requestData = [
                'pin_code' => $pinCode
            ];

            if ($examType) {
                $requestData['exam_type'] = $examType;
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
                        'message' => 'Exam pin is valid',
                        'data' => [
                            'pin_code' => $pinCode,
                            'exam_type' => $responseData['exam_type'] ?? $examType ?? 'Unknown',
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
                        'message' => $responseData['message'] ?? $responseData['msg'] ?? 'Invalid exam pin',
                        'data' => [
                            'pin_code' => $pinCode,
                            'is_valid' => false
                        ]
                    ];
                }
            } else {
                // If verification endpoint is not available, return informative message
                return [
                    'status' => 'info',
                    'message' => 'Exam pin verification endpoint not available from provider. Pin may still be valid.',
                    'data' => [
                        'pin_code' => $pinCode,
                        'verification_attempted' => true,
                        'provider_response' => $result['message'] ?? 'No verification endpoint'
                    ]
                ];
            }

        } catch (Exception $e) {
            Log::error('Exam pin verification error: ' . $e->getMessage());
            return $this->errorResponse('Unable to verify exam pin at this time');
        }
    }

    /**
     * Get transaction history for exam pins
     */
    public function getExamPinHistory($userId, $limit = 20)
    {
        try {
            $transactions = Transaction::where('sUser', $userId)
                ->where('sType', Transaction::TYPE_EXAM_PIN)
                ->orderBy('sDate', 'desc')
                ->limit($limit)
                ->get();

            return [
                'status' => 'success',
                'data' => $transactions->map(function ($transaction) {
                    return [
                        'reference' => $transaction->sReference,
                        'exam_type' => $transaction->sDesc ?? 'Unknown',
                        'amount' => $transaction->sAmount,
                        'status' => $transaction->sStatus,
                        'date' => $transaction->sDate,
                        'details' => json_decode($transaction->api_response, true)
                    ];
                })
            ];

        } catch (Exception $e) {
            Log::error('Exam pin history error: ' . $e->getMessage());
            return $this->errorResponse('Unable to fetch exam pin history');
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
