<?php

namespace App\Services;

use App\Models\CablePlan;
use App\Models\ApiConfig;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Log;
use Exception;

class CableTVService
{
    protected $walletService;
    protected $externalApiService;

    public function __construct(WalletService $walletService, ExternalApiService $externalApiService)
    {
        $this->walletService = $walletService;
        $this->externalApiService = $externalApiService;
    }

    /**
     * Purchase cable TV subscription (compatible with old PHP app)
     */
    public function purchaseCableSubscription($userId, $decoder, $iucNumber, $planId)
    {
        try {
            // Validate user
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            // Validate cable plan
            $cablePlan = CablePlan::getCablePlan($decoder, $planId);
            if (!$cablePlan) {
                return $this->errorResponse('Invalid cable plan selected');
            }

            // Calculate final amount after discount
            $finalAmount = $cablePlan->calculateFinalAmount($user->sType);
            $profit = $cablePlan->calculateProfit($finalAmount); // Calculate profit

            // Validate IUC number first
            $validationResult = $this->validateIUC($decoder, $iucNumber);
            if ($validationResult['status'] !== 'success') {
                return $this->errorResponse($validationResult['message']);
            }

            $customerName = $validationResult['customer_name'];

            // Check for duplicate transaction
            if (Transaction::checkDuplicate($userId, Transaction::SERVICE_CABLE_TV, $finalAmount, 60)) {
                return $this->errorResponse('Duplicate transaction detected. Please wait before retrying.');
            }

            // Check user balance
            if (!$this->walletService->hasSufficientBalance($userId, $finalAmount)) {
                return $this->errorResponse('Insufficient wallet balance');
            }

            // Create description compatible with old PHP app
            $description = "Purchase of {$decoder} {$cablePlan->sPlan} subscription for IUC {$iucNumber} ({$customerName})";

            // Debit wallet first
            $walletResult = $this->walletService->debitWallet(
                $userId,
                $finalAmount,
                Transaction::SERVICE_CABLE_TV,
                $description,
                $profit
            );

            if ($walletResult['status'] !== 'success') {
                return $this->errorResponse($walletResult['message']);
            }

            $transaction = Transaction::find($walletResult['data']['transaction_id']);

            // Get Uzobest cable ID for the provider
            $cableProvider = \App\Models\CableId::where('provider', strtolower($decoder))->first();
            $uzobestCableId = $cableProvider ? $cableProvider->cableid : null;

            // Process cable TV subscription via API
            $apiResponse = $this->processCableAPI($uzobestCableId, $iucNumber, $cablePlan, $transaction->transref, $customerName);

            if ($apiResponse['status'] === 'success') {
                // Transaction already marked as successful by WalletService
                return $this->successResponse('Cable TV subscription successful', [
                    'transaction_id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'decoder' => strtoupper($decoder),
                    'plan' => $cablePlan->sPlan,
                    'amount' => $finalAmount,
                    'iuc_number' => $iucNumber,
                    'customer_name' => $customerName,
                    'balance' => $walletResult['data']['new_balance'],
                    'profit' => $profit
                ]);
            } else {
                // API failed, reverse the transaction
                $this->walletService->reverseTransaction(
                    $transaction->tId,
                    'API failure: ' . ($apiResponse['message'] ?? 'Unknown error')
                );

                return $this->errorResponse($apiResponse['message'] ?? 'Cable TV subscription failed');
            }

        } catch (Exception $e) {
            Log::error('Cable TV Purchase Error: ' . $e->getMessage());
            return $this->errorResponse('Cable TV subscription failed. Please try again.');
        }
    }

    /**
     * Validate IUC/Smart Card number using external API service
     */
    public function validateIUC($decoder, $iucNumber)
    {
        try {
            // Get Uzobest cable ID for the provider
            $cableProvider = \App\Models\CableId::where('provider', strtolower($decoder))->first();
            $uzobestCableId = $cableProvider ? $cableProvider->cableid : $decoder;

            $result = $this->externalApiService->verifyCableIUC($iucNumber, $uzobestCableId);

            if ($result['success']) {
                return [
                    'status' => 'success',
                    'customer_name' => $result['customer_name'] ?? 'Unknown Customer'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Invalid IUC/Smart Card number'
                ];
            }

        } catch (Exception $e) {
            Log::error('IUC Validation Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Validation service temporarily unavailable'
            ];
        }
    }

    /**
     * Process cable TV subscription via external API service
     */
    protected function processCableAPI($uzobestCableId, $iucNumber, $cablePlan, $reference, $customerName)
    {
        try {
            // Use uzobest_plan_id from the plan if available, fallback to planid
            $planId = $cablePlan->uzobest_plan_id ?? $cablePlan->planid;

            $result = $this->externalApiService->purchaseCable($uzobestCableId, $iucNumber, $planId, $reference);

            if ($result['success']) {
                return [
                    'status' => 'success',
                    'server_ref' => $result['server_reference'] ?? $reference,
                    'message' => $result['message'] ?? 'Cable TV subscription successful'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Cable TV subscription failed'
                ];
            }

        } catch (Exception $e) {
            Log::error('Cable TV API Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'API service temporarily unavailable'
            ];
        }
    }

    /**
     * Get cable TV plans for a decoder
     */
    public function getCablePlans($userId, $decoder)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            $plans = CablePlan::getByDecoder($decoder);

            $formattedPlans = $plans->map(function ($plan) use ($user) {
                return [
                    'id' => $plan->cpId, // For backward compatibility with JavaScript
                    'plan_id' => $plan->cpId,
                    'uzobest_plan_id' => $plan->uzobest_plan_id,
                    'plan' => $plan->name,
                    'name' => $plan->name, // Add both plan and name for compatibility
                    'amount' => $plan->getPriceForUserType($user->sType ?? 'user'),
                    'price' => $plan->getPriceForUserType($user->sType ?? 'user'),
                    'original_amount' => $plan->price,
                    'validity' => $plan->day ? $plan->day . ' days' : 'N/A',
                    'duration' => $plan->day ? $plan->day . ' days' : '30 days',
                    'type' => $plan->type ?? 'Standard',
                    'channels' => 'Multiple'
                ];
            });

            return $this->successResponse('Cable TV plans retrieved successfully', [
                'decoder' => strtoupper($decoder),
                'plans' => $formattedPlans
            ]);

        } catch (Exception $e) {
            Log::error('Get Cable Plans Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve cable TV plans');
        }
    }

    /**
     * Get cable TV transaction history (compatible with old PHP app)
     */
    public function getCableHistory($userId, $limit = 50)
    {
        return Transaction::where('sId', $userId)
            ->where('servicename', Transaction::SERVICE_CABLE_TV)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'amount' => $transaction->amount,
                    'iuc_number' => $this->extractIUCFromDescription($transaction->servicedesc),
                    'plan' => $this->extractPlanFromDescription($transaction->servicedesc),
                    'decoder' => $this->extractDecoderFromDescription($transaction->servicedesc),
                    'customer_name' => $this->extractCustomerFromDescription($transaction->servicedesc),
                    'status' => $transaction->status_text,
                    'date' => $transaction->formatted_date,
                    'profit' => $transaction->profit
                ];
            });
    }

    /**
     * Get available decoders
     */
    public function getAvailableDecoders()
    {
        return ['dstv', 'gotv', 'startimes'];
    }

    /**
     * Get available decoders with details
     */
    public function getAvailableDecodersWithDetails()
    {
        return [
            'status' => 'success',
            'data' => [
                ['code' => 'dstv', 'name' => 'DSTV'],
                ['code' => 'gotv', 'name' => 'GOTV'],
                ['code' => 'startimes', 'name' => 'Startimes']
            ]
        ];
    }

    /**
     * Extract IUC number from transaction description
     */
    private function extractIUCFromDescription($description)
    {
        preg_match('/IUC\s+(\d+)/', $description, $matches);
        return $matches[1] ?? 'N/A';
    }

    /**
     * Extract plan from transaction description
     */
    private function extractPlanFromDescription($description)
    {
        // Extract plan between decoder type and "subscription"
        preg_match('/(?:DSTV|GOTV|STARTIMES)\s+(.+?)\s+subscription/i', $description, $matches);
        return $matches[1] ?? 'N/A';
    }

    /**
     * Extract decoder from transaction description
     */
    private function extractDecoderFromDescription($description)
    {
        if (stripos($description, 'DSTV') !== false) return 'DSTV';
        if (stripos($description, 'GOTV') !== false) return 'GOTV';
        if (stripos($description, 'STARTIMES') !== false) return 'STARTIMES';
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
