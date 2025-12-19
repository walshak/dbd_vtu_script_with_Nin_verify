<?php

namespace App\Services;

use App\Models\DataPlan;
use App\Models\NetworkId;
use App\Models\ApiConfig;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DataService
{
    protected $walletService;
    protected $externalApiService;

    public function __construct(WalletService $walletService, ExternalApiService $externalApiService)
    {
        $this->walletService = $walletService;
        $this->externalApiService = $externalApiService;
    }

    /**
     * Purchase data bundle for a user (compatible with old PHP app)
     */
    public function purchaseData($userId, $network, $phone, $planId, $dataGroup = 'SME', $portedNumber = false)
    {
        try {
            Log::info('Data purchase started', [
                'user_id' => $userId,
                'network' => $network,
                'phone' => $phone,
                'plan_id' => $planId,
                'data_group' => $dataGroup
            ]);

            // Validate user
            $user = User::find($userId);
            if (!$user) {
                Log::warning('User not found', ['user_id' => $userId]);
                return $this->errorResponse('User not found');
            }

            // Validate network
            $networkDetails = NetworkId::getByName($network);
            if (!$networkDetails) {
                Log::warning('Invalid network', ['network' => $network]);
                return $this->errorResponse('Invalid network selected');
            }

            // Get data plan
            $dataPlan = DataPlan::getByNetworkAndPlanId($networkDetails->nId, $planId);
            if (!$dataPlan) {
                Log::warning('Invalid data plan', [
                    'network_id' => $networkDetails->nId,
                    'plan_id' => $planId
                ]);
                return $this->errorResponse('Invalid data plan selected');
            }

            // Check if uzobest_plan_id is set
            if (empty($dataPlan->uzobest_plan_id)) {
                Log::error('Uzobest plan ID not configured', [
                    'plan_id' => $planId,
                    'plan_name' => $dataPlan->dPlan,
                    'message' => 'The uzobest_plan_id field is empty in the database. Please fetch and save Uzobest plans from the admin panel.'
                ]);
                return $this->errorResponse('Data plan not properly configured. Please contact administrator.');
            }

            // Calculate final amount after discount
            $finalAmount = $dataPlan->calculateFinalAmount($user->sType);
            $profit = $dataPlan->calculateProfit($finalAmount); // Calculate profit

            Log::info('Data plan pricing calculated', [
                'final_amount' => $finalAmount,
                'profit' => $profit,
                'user_type' => $user->sType
            ]);

            // Check for duplicate transaction
            if (Transaction::checkDuplicate($userId, Transaction::SERVICE_DATA, $finalAmount, 60)) {
                return $this->errorResponse('Duplicate transaction detected. Please wait before retrying.');
            }

            // Check user balance
            if (!$this->walletService->hasSufficientBalance($userId, $finalAmount)) {
                return $this->errorResponse('Insufficient wallet balance');
            }

            // Create description compatible with old PHP app
            $description = "Purchase of {$network} {$dataPlan->sPlan} data bundle for phone number {$phone}";

            // Debit wallet first
            $walletResult = $this->walletService->debitWallet(
                $userId,
                $finalAmount,
                Transaction::SERVICE_DATA,
                $description,
                $profit
            );

            if ($walletResult['status'] !== 'success') {
                return $this->errorResponse($walletResult['message']);
            }

            $transaction = Transaction::find($walletResult['data']['transaction_id']);

            // Process data purchase via API
            $apiResponse = $this->processDataAPI($networkDetails, $dataPlan, $phone, $transaction->transref, $dataGroup, $portedNumber);

            if ($apiResponse['status'] === 'success') {
                // Transaction already marked as successful by WalletService
                return $this->successResponse('Data purchase successful', [
                    'transaction_id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'plan' => $dataPlan->sPlan,
                    'amount' => $finalAmount,
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

                return $this->errorResponse($apiResponse['message'] ?? 'Data purchase failed');
            }
        } catch (Exception $e) {
            Log::error('Data Purchase Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId ?? null,
                'network' => $network ?? null,
                'phone' => $phone ?? null,
                'plan_id' => $planId ?? null,
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return $this->errorResponse('Data purchase failed. Please try again.');
        }
    }

    protected function processDataAPI($networkDetails, $dataPlan, $phone, $reference, $dataGroup, $portedNumber = false)
    {
        try {
            // Get the plan ID - use uzobest_plan_id if available, otherwise fall back to dPlanId
            $planId = $dataPlan->uzobest_plan_id ?? $dataPlan->dPlanId ?? '';

            Log::info('Processing data API call', [
                'network' => $networkDetails->network,
                'phone' => $phone,
                'plan_id' => $planId,
                'uzobest_plan_id' => $dataPlan->uzobest_plan_id,
                'dPlanId' => $dataPlan->dPlanId,
                'data_group' => $dataGroup
            ]);

            // Use the real external API service
            $result = $this->externalApiService->purchaseData(
                $networkDetails->network,
                $phone,
                $planId, // Plan ID from the database for Uzobest API
                $dataGroup,
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
                    'message' => $result['message'] ?? 'Data purchase failed',
                    'error_code' => $result['error_code'] ?? 'API_ERROR'
                ];
            }
        } catch (Exception $e) {
            Log::error('Data API Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'API service temporarily unavailable'
            ];
        }
    }

    /**
     * Get data plans from external API
     */
    public function getExternalDataPlans($network, $dataType = 'SME')
    {
        try {
            $result = $this->externalApiService->getDataPlans($network, $dataType);

            if (!$result['success']) {
                return $this->errorResponse($result['message'] ?? 'Failed to retrieve data plans');
            }

            return $this->successResponse('Data plans retrieved successfully', [
                'network' => strtoupper($network),
                'data_type' => $dataType,
                'plans' => $result['plans'] ?? []
            ]);
        } catch (Exception $e) {
            Log::error('External Data Plans Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve data plans from provider');
        }
    }

    /**
     * Get data plans for a network
     */
    public function getDataPlans($userId, $network)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            $networkDetails = NetworkId::getByName($network);
            if (!$networkDetails) {
                return $this->errorResponse('Invalid network selected');
            }

            $plans = DataPlan::getByNetwork($networkDetails->nId);

            $formattedPlans = $plans->map(function ($plan) use ($user) {
                return [
                    'plan_id' => $plan->dId,
                    'plan' => $plan->sPlan,
                    'amount' => $plan->getPriceForUserType($user->sType),
                    'original_amount' => $plan->sPrice,
                    'discount' => $plan->getDiscountForUserType($user->sType),
                    'validity' => $plan->validity ?? 'N/A'
                ];
            });

            return $this->successResponse('Data plans retrieved successfully', [
                'network' => strtoupper($network),
                'plans' => $formattedPlans
            ]);
        } catch (Exception $e) {
            Log::error('Get Data Plans Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve data plans');
        }
    }

    /**
     * Get data transaction history (compatible with old PHP app)
     */
    public function getDataHistory($userId, $limit = 50)
    {
        return Transaction::where('sId', $userId)
            ->where('servicename', Transaction::SERVICE_DATA)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'amount' => $transaction->amount,
                    'phone' => $this->extractPhoneFromDescription($transaction->servicedesc),
                    'plan' => $this->extractPlanFromDescription($transaction->servicedesc),
                    'network' => $this->extractNetworkFromDescription($transaction->servicedesc),
                    'status' => $transaction->status_text,
                    'date' => $transaction->formatted_date,
                    'profit' => $transaction->profit
                ];
            });
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

        $samplePlan = DataPlan::where('network_id', $networkDetails->nId)->first();
        if (!$samplePlan) {
            return null;
        }

        return [
            'sme_discount' => $samplePlan->getDiscountForUserType($user->sType, 'SME'),
            'dg_discount' => $samplePlan->getDiscountForUserType($user->sType, 'DG'),
            'cg_discount' => $samplePlan->getDiscountForUserType($user->sType, 'CG')
        ];
    }

    /**
     * Get data pricing information
     */
    public function getDataPricing($userId, $network, $planId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            $networkDetails = NetworkId::getByName($network);
            if (!$networkDetails) {
                return $this->errorResponse('Invalid network selected');
            }

            $dataPlan = DataPlan::getByNetworkAndPlanId($networkDetails->nId, $planId);
            if (!$dataPlan) {
                return $this->errorResponse('Invalid data plan selected');
            }

            $finalAmount = $dataPlan->calculateFinalAmount($user->sType);
            $discount = $dataPlan->sPrice - $finalAmount;
            $discountPercentage = ($discount / $dataPlan->sPrice) * 100;

            return $this->successResponse('Data pricing retrieved successfully', [
                'network' => strtoupper($network),
                'plan' => $dataPlan->sPlan,
                'original_price' => $dataPlan->sPrice,
                'final_price' => $finalAmount,
                'discount_amount' => $discount,
                'discount_percentage' => round($discountPercentage, 2),
                'validity' => $dataPlan->validity ?? 'N/A'
            ]);
        } catch (Exception $e) {
            Log::error('Get Data Pricing Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve data pricing');
        }
    }

    /**
     * Validate phone number for data purchase
     */
    public function validatePhoneNumber($phone, $network)
    {
        // Basic phone number validation
        if (!preg_match('/^[0-9]{11}$/', $phone)) {
            return $this->errorResponse('Invalid phone number format. Must be 11 digits.');
        }

        // Network-specific validation based on phone number prefixes
        $networkPrefixes = [
            'MTN' => ['0803', '0806', '0703', '0706', '0813', '0816', '0810', '0814', '0903', '0906'],
            'GLO' => ['0805', '0807', '0705', '0815', '0811', '0905'],
            'AIRTEL' => ['0802', '0808', '0701', '0708', '0812', '0901', '0902'],
            '9MOBILE' => ['0809', '0818', '0817', '0909', '0908']
        ];

        $phonePrefix = substr($phone, 0, 4);
        $networkUpper = strtoupper($network);

        if (isset($networkPrefixes[$networkUpper])) {
            if (!in_array($phonePrefix, $networkPrefixes[$networkUpper])) {
                return $this->errorResponse("Phone number does not match {$networkUpper} network.");
            }
        }

        return $this->successResponse('Phone number validated successfully');
    }

    /**
     * Get data plans for user with specific data group
     */
    public function getDataPlansForUser($userId, $network, $dataGroup = 'SME')
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                Log::warning('User not found for data plans', ['userId' => $userId]);
                return [];
            }

            $networkDetails = NetworkId::getByName($network);
            if (!$networkDetails) {
                Log::warning('Network not found for data plans', ['network' => $network]);
                return [];
            }

            Log::info('Fetching data plans', [
                'network' => $network,
                'networkId' => $networkDetails->nId,
                'dataGroup' => $dataGroup,
                'userType' => $user->sType
            ]);

            $plans = DataPlan::getByNetworkAndGroup($networkDetails->nId, $dataGroup);

            Log::info('Found data plans', ['count' => $plans->count()]);

            return $plans->map(function ($plan) use ($user) {
                $price = $plan->getPriceForUserType($user->sType);
                return [
                    'id' => $plan->dPlanId,
                    'plan_id' => $plan->dPlanId,
                    'name' => $plan->dPlan,
                    'plan' => $plan->dPlan,
                    'amount' => $plan->dAmount,
                    'price' => $price,
                    'validity' => $plan->dValidity,
                    'group' => $plan->dGroup
                ];
            })->toArray();
        } catch (Exception $e) {
            Log::error('Get Data Plans Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Check if data service is available for network and group
     */
    public function isServiceAvailable($network, $dataGroup)
    {
        try {
            $networkDetails = NetworkId::getByName($network);
            if (!$networkDetails) {
                return false;
            }

            // Check if network supports the service type
            switch (strtolower($dataGroup)) {
                case 'sme':
                    return $networkDetails->isServiceEnabled('sme');
                case 'gifting':
                case 'dg':
                    return $networkDetails->isServiceEnabled('gifting');
                case 'corporate':
                case 'cg':
                    return $networkDetails->isServiceEnabled('corporate');
                default:
                    return $networkDetails->isServiceEnabled('sme');
            }
        } catch (Exception $e) {
            Log::error('Service Availability Check Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available data groups/types
     */
    public function getDataGroups()
    {
        return $this->successResponse('Data groups retrieved successfully', [
            'groups' => [
                ['code' => 'SME', 'name' => 'SME Data', 'description' => 'Corporate Gifting'],
                ['code' => 'DG', 'name' => 'Direct Gifting', 'description' => 'Direct Gifting'],
                ['code' => 'CG', 'name' => 'Corporate Gifting', 'description' => 'Corporate Gifting']
            ]
        ]);
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
     * Extract data plan from transaction description
     */
    private function extractPlanFromDescription($description)
    {
        // Extract data plan pattern like "500MB", "1GB", etc.
        preg_match('/\b\d+(?:\.\d+)?(?:MB|GB)\b/i', $description, $matches);
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
