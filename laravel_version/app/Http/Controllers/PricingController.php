<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\DataPlan;
use App\Models\CableTVPlan;
use App\Models\ExamType;
use App\Models\RechargePin;
use App\Models\NetworkId;
use App\Models\User;
use App\Services\PricingService;

class PricingController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }
    /**
     * Get all pricing for all services
     */
    public function getAllPricing(Request $request)
    {
        try {
            $userId = auth()->id();
            $user = $userId ? User::find($userId) : null;
            $userType = $user ? $user->sType : 'user';

            $pricing = [
                'airtime' => $this->pricingService->getAirtimePricing($userType),
                'data' => $this->pricingService->getDataPricing($userType),
                'cable_tv' => $this->pricingService->getCableTVPricing($userType),
                'electricity' => $this->pricingService->getElectricityPricing($userType),
                'exam_pins' => $this->pricingService->getExamPinPricing($userType),
                'recharge_pins' => $this->pricingService->getRechargePinPricing($userType),
                'alpha_topup' => $this->pricingService->getAlphaTopupPricing($userType),
                'user_info' => $user ? [
                    'type' => $user->sType,
                    'discount_level' => $this->getUserDiscountLevel($user->sType),
                    'special_rates' => $this->hasSpecialRates($user)
                ] : null,
                'pricing_metadata' => [
                    'last_updated' => now()->toISOString(),
                    'source' => 'real_time_api_with_fallback',
                    'cache_duration' => '30_minutes',
                    'user_type' => $userType
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $pricing
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting all pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get pricing information'
            ], 500);
        }
    }

    /**
     * Get airtime pricing for all networks
     */
    public function getAirtimePricing(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $pricing = $this->pricingService->getAirtimePricing($userType);

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'source' => 'real_time_api_with_fallback',
                    'user_type' => $userType,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting airtime pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get airtime pricing'
            ], 500);
        }
    }

    /**
     * Get data pricing for all networks
     */
    public function getDataPricing(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $pricing = $this->pricingService->getDataPricing($userType);

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'source' => 'real_time_api_with_fallback',
                    'user_type' => $userType,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting data pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get data pricing'
            ], 500);
        }
    }

    /**
     * Get cable TV pricing
     */
    public function getCableTVPricing(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $pricing = $this->pricingService->getCableTVPricing($userType);

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'source' => 'real_time_api_with_fallback',
                    'user_type' => $userType,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting cable TV pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get cable TV pricing'
            ], 500);
        }
    }

    /**
     * Get electricity pricing
     */
    public function getElectricityPricing(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $pricing = $this->pricingService->getElectricityPricing($userType);

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'source' => 'real_time_api_with_fallback',
                    'user_type' => $userType,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting electricity pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get electricity pricing'
            ], 500);
        }
    }

    /**
     * Get exam pin pricing
     */
    public function getExamPinPricing(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $pricing = $this->pricingService->getExamPinPricing($userType);

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'source' => 'real_time_api_with_fallback',
                    'user_type' => $userType,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting exam pin pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get exam pin pricing'
            ], 500);
        }
    }

    /**
     * Get recharge pin pricing
     */
    public function getRechargePinPricing(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $pricing = $this->pricingService->getRechargePinPricing($userType);

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'source' => 'real_time_api_with_fallback',
                    'user_type' => $userType,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recharge pin pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get recharge pin pricing'
            ], 500);
        }
    }

    /**
     * Get alpha topup pricing
     */
    public function getAlphaTopupPricing(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $pricing = $this->pricingService->getAlphaTopupPricing($userType);

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'source' => 'real_time_api_with_fallback',
                    'user_type' => $userType,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting alpha topup pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get alpha topup pricing'
            ], 500);
        }
    }

    /**
     * Get specific service pricing with parameters
     */
    public function getSpecificPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable_tv,electricity,exam_pin,recharge_pin,alpha_topup',
            'network' => 'sometimes|string',
            'plan_id' => 'sometimes|integer',
            'amount' => 'sometimes|numeric|min:50',
            'provider' => 'sometimes|string',
            'quantity' => 'sometimes|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $serviceType = $request->service_type;

            // Get base pricing from PricingService
            switch ($serviceType) {
                case 'airtime':
                    $basePricing = $this->pricingService->getAirtimePricing($userType);
                    $pricing = $this->calculateSpecificAirtimePricing($request, $basePricing, $userType);
                    break;
                case 'data':
                    $basePricing = $this->pricingService->getDataPricing($userType);
                    $pricing = $this->calculateSpecificDataPricing($request, $basePricing, $userType);
                    break;
                case 'cable_tv':
                    $basePricing = $this->pricingService->getCableTVPricing($userType);
                    $pricing = $this->calculateSpecificCableTVPricing($request, $basePricing, $userType);
                    break;
                case 'electricity':
                    $basePricing = $this->pricingService->getElectricityPricing($userType);
                    $pricing = $this->calculateSpecificElectricityPricing($request, $basePricing, $userType);
                    break;
                case 'exam_pin':
                    $basePricing = $this->pricingService->getExamPinPricing($userType);
                    $pricing = $this->calculateSpecificExamPinPricing($request, $basePricing, $userType);
                    break;
                case 'recharge_pin':
                    $basePricing = $this->pricingService->getRechargePinPricing($userType);
                    $pricing = $this->calculateSpecificRechargePinPricing($request, $basePricing, $userType);
                    break;
                case 'alpha_topup':
                    $basePricing = $this->pricingService->getAlphaTopupPricing($userType);
                    $pricing = $this->calculateSpecificAlphaTopupPricing($request, $basePricing, $userType);
                    break;
                default:
                    throw new \Exception('Invalid service type');
            }

            return response()->json([
                'status' => 'success',
                'data' => $pricing,
                'metadata' => [
                    'service_type' => $serviceType,
                    'user_type' => $userType,
                    'source' => 'real_time_calculation',
                    'calculated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting specific $serviceType pricing: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get pricing information'
            ], 500);
        }
    }

    /**
     * Calculate bulk pricing discount
     */
    public function calculateBulkPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable_tv,electricity,exam_pin,recharge_pin,alpha_topup',
            'quantity' => 'required|integer|min:1|max:100',
            'unit_price' => 'required|numeric|min:0.01',
            'bulk_discounts' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $quantity = $request->quantity;
            $unitPrice = $request->unit_price;
            $serviceType = $request->service_type;

            // Calculate base total
            $baseTotal = $unitPrice * $quantity;

            // Apply bulk discount
            $bulkDiscountPercentage = $this->getBulkDiscountPercentage($serviceType, $quantity, $userType);
            $bulkDiscountAmount = ($baseTotal * $bulkDiscountPercentage) / 100;
            $discountedTotal = $baseTotal - $bulkDiscountAmount;

            // Apply user type discount (if not already applied in unit price)
            $userDiscountPercentage = $this->getUserDiscountPercentage($userType);
            $userDiscountAmount = ($discountedTotal * $userDiscountPercentage) / 100;
            $finalTotal = $discountedTotal - $userDiscountAmount;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'service_type' => $serviceType,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitPrice, 2),
                    'base_total' => number_format($baseTotal, 2),
                    'bulk_discount' => [
                        'percentage' => $bulkDiscountPercentage,
                        'amount' => number_format($bulkDiscountAmount, 2)
                    ],
                    'user_discount' => [
                        'percentage' => $userDiscountPercentage,
                        'amount' => number_format($userDiscountAmount, 2)
                    ],
                    'final_total' => number_format($finalTotal, 2),
                    'total_savings' => number_format($baseTotal - $finalTotal, 2),
                    'total_discount_percentage' => number_format((($baseTotal - $finalTotal) / $baseTotal) * 100, 2),
                    'calculation_source' => 'real_time',
                    'calculated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error calculating bulk pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate bulk pricing'
            ], 500);
        }
    }

    /**
     * Clear pricing cache
     */
    public function clearPricingCache(Request $request)
    {
        try {
            $success = $this->pricingService->clearPricingCache();

            if ($success) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pricing cache cleared successfully',
                    'cleared_at' => now()->toISOString()
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to clear pricing cache'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error clearing pricing cache: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear pricing cache'
            ], 500);
        }
    }

    /**
     * Get pricing cache status
     */
    public function getPricingCacheStatus(Request $request)
    {
        try {
            $status = $this->pricingService->getPricingCacheStatus();

            return response()->json([
                'status' => 'success',
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting pricing cache status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get cache status'
            ], 500);
        }
    }

    /**
     * Force refresh pricing from external APIs
     */
    public function refreshPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'sometimes|string|in:airtime,data,cable_tv,electricity,exam_pin,recharge_pin,alpha_topup,all'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = auth()->user();
            $userType = $user ? $user->sType : 'user';
            $serviceType = $request->get('service_type', 'all');

            // Clear cache first
            $this->pricingService->clearPricingCache();

            $refreshedData = [];

            if ($serviceType === 'all') {
                $refreshedData = [
                    'airtime' => $this->pricingService->getAirtimePricing($userType),
                    'data' => $this->pricingService->getDataPricing($userType),
                    'cable_tv' => $this->pricingService->getCableTVPricing($userType),
                    'electricity' => $this->pricingService->getElectricityPricing($userType),
                    'exam_pins' => $this->pricingService->getExamPinPricing($userType),
                    'recharge_pins' => $this->pricingService->getRechargePinPricing($userType),
                    'alpha_topup' => $this->pricingService->getAlphaTopupPricing($userType)
                ];
            } else {
                switch ($serviceType) {
                    case 'airtime':
                        $refreshedData = $this->pricingService->getAirtimePricing($userType);
                        break;
                    case 'data':
                        $refreshedData = $this->pricingService->getDataPricing($userType);
                        break;
                    case 'cable_tv':
                        $refreshedData = $this->pricingService->getCableTVPricing($userType);
                        break;
                    case 'electricity':
                        $refreshedData = $this->pricingService->getElectricityPricing($userType);
                        break;
                    case 'exam_pin':
                        $refreshedData = $this->pricingService->getExamPinPricing($userType);
                        break;
                    case 'recharge_pin':
                        $refreshedData = $this->pricingService->getRechargePinPricing($userType);
                        break;
                    case 'alpha_topup':
                        $refreshedData = $this->pricingService->getAlphaTopupPricing($userType);
                        break;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pricing data refreshed successfully',
                'data' => $refreshedData,
                'metadata' => [
                    'service_type' => $serviceType,
                    'user_type' => $userType,
                    'refreshed_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error refreshing pricing: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to refresh pricing data'
            ], 500);
        }
    }

    /**
     * Get user discount percentage for service
     */
    private function getUserDiscountPercentage(string $userType): float
    {
        $discountMap = [
            'user' => 0,
            'agent' => 2,
            'super_agent' => 5,
            'api_user' => 3,
            'vendor' => 7
        ];

        return $discountMap[$userType] ?? 0;
    }

    /**
     * Get bulk discount percentage
     */
    private function getBulkDiscountPercentage(string $serviceType, int $quantity, string $userType): float
    {
        $bulkTiers = [
            5 => 0.5,   // 5-9 items: 0.5% discount
            10 => 1,    // 10-19 items: 1% discount
            20 => 2,    // 20-49 items: 2% discount
            50 => 3     // 50+ items: 3% discount
        ];

        $discount = 0;
        foreach (array_reverse($bulkTiers, true) as $tier => $tierDiscount) {
            if ($quantity >= $tier) {
                $discount = $tierDiscount;
                break;
            }
        }

        // Add bonus discount for higher user types
        if (in_array($userType, ['agent', 'super_agent', 'vendor'])) {
            $discount += 0.5;
        }

        return $discount;
    }

    /**
     * Get user discount level
     */
    private function getUserDiscountLevel(string $userType): string
    {
        $levels = [
            'user' => 'Standard',
            'agent' => 'Agent',
            'super_agent' => 'Super Agent',
            'api_user' => 'API User',
            'vendor' => 'Vendor'
        ];

        return $levels[$userType] ?? 'Standard';
    }

    /**
     * Check if user has special rates
     */
    private function hasSpecialRates($user): bool
    {
        if (!$user) return false;
        return in_array($user->sType, ['super_agent', 'api_user', 'vendor']);
    }

    /**
     * Calculate specific pricing methods using new PricingService data
     */
    private function calculateSpecificAirtimePricing(Request $request, array $basePricing, string $userType): array
    {
        $network = $request->network;
        $amount = $request->amount ?? 100;
        $quantity = $request->quantity ?? 1;

        // Find network pricing
        $networkPricing = collect($basePricing)->firstWhere('network_code', strtolower($network));
        if (!$networkPricing) {
            throw new \Exception('Network not found or not supported');
        }

        $rate = $networkPricing['rate'] ?? 1.0;
        $unitPrice = $amount * $rate;
        $totalAmount = $unitPrice * $quantity;

        return [
            'network' => $network,
            'amount_per_unit' => $amount,
            'quantity' => $quantity,
            'rate' => $rate,
            'unit_price' => number_format($unitPrice, 2),
            'total_amount' => number_format($totalAmount, 2),
            'discount_percentage' => $networkPricing['discount_percentage'] ?? 0,
            'savings_per_unit' => number_format($amount - $unitPrice, 2),
            'total_savings' => number_format(($amount - $unitPrice) * $quantity, 2),
            'source' => $networkPricing['source'] ?? 'unknown'
        ];
    }

    private function calculateSpecificDataPricing(Request $request, array $basePricing, string $userType): array
    {
        $planId = $request->plan_id;
        $quantity = $request->quantity ?? 1;

        // Find plan pricing
        $planPricing = collect($basePricing)->firstWhere('plan_id', $planId);
        if (!$planPricing) {
            throw new \Exception('Data plan not found');
        }

        $userPrice = (float) str_replace(',', '', $planPricing['user_price']);
        $totalAmount = $userPrice * $quantity;

        return [
            'plan_id' => $planId,
            'network' => $planPricing['network'],
            'plan_name' => $planPricing['plan_name'],
            'data_size' => $planPricing['data_size'],
            'validity' => $planPricing['validity'],
            'quantity' => $quantity,
            'unit_price' => number_format($userPrice, 2),
            'total_amount' => number_format($totalAmount, 2),
            'external_price' => $planPricing['external_price'],
            'savings_per_unit' => $planPricing['savings'] ?? '0.00',
            'total_savings' => number_format(((float) str_replace(',', '', $planPricing['savings'] ?? '0')) * $quantity, 2),
            'source' => $planPricing['source'] ?? 'unknown'
        ];
    }

    private function calculateSpecificCableTVPricing(Request $request, array $basePricing, string $userType): array
    {
        $planId = $request->plan_id;
        $quantity = $request->quantity ?? 1;

        // Find plan pricing
        $planPricing = collect($basePricing)->firstWhere('plan_id', $planId);
        if (!$planPricing) {
            throw new \Exception('Cable TV plan not found');
        }

        $userPrice = (float) str_replace(',', '', $planPricing['user_price']);
        $totalAmount = $userPrice * $quantity;

        return [
            'plan_id' => $planId,
            'provider' => $planPricing['provider'],
            'plan_name' => $planPricing['plan_name'],
            'plan_code' => $planPricing['plan_code'],
            'quantity' => $quantity,
            'unit_price' => number_format($userPrice, 2),
            'total_amount' => number_format($totalAmount, 2),
            'external_price' => $planPricing['external_price'],
            'savings_per_unit' => $planPricing['savings'] ?? '0.00',
            'total_savings' => number_format(((float) str_replace(',', '', $planPricing['savings'] ?? '0')) * $quantity, 2),
            'source' => $planPricing['source'] ?? 'unknown'
        ];
    }

    private function calculateSpecificElectricityPricing(Request $request, array $basePricing, string $userType): array
    {
        $amount = $request->amount ?? 1000;
        $quantity = $request->quantity ?? 1;

        $rate = $basePricing['user_rate'] ?? 1.0;
        $unitPrice = $amount * $rate;
        $totalAmount = $unitPrice * $quantity;

        return [
            'amount_per_unit' => $amount,
            'quantity' => $quantity,
            'rate' => $rate,
            'unit_price' => number_format($unitPrice, 2),
            'total_amount' => number_format($totalAmount, 2),
            'discount_percentage' => $basePricing['discount_percentage'] ?? 0,
            'savings_per_unit' => number_format($amount - $unitPrice, 2),
            'total_savings' => number_format(($amount - $unitPrice) * $quantity, 2),
            'service_charge' => $basePricing['service_charge'] ?? 0,
            'source' => $basePricing['source'] ?? 'unknown'
        ];
    }

    private function calculateSpecificExamPinPricing(Request $request, array $basePricing, string $userType): array
    {
        $examId = $request->exam_id ?? $request->plan_id;
        $quantity = $request->quantity ?? 1;

        // Find exam pricing
        $examPricing = collect($basePricing)->firstWhere('exam_id', $examId);
        if (!$examPricing) {
            throw new \Exception('Exam type not found');
        }

        $userPrice = (float) str_replace(',', '', $examPricing['user_price']);
        $totalAmount = $userPrice * $quantity;

        return [
            'exam_id' => $examId,
            'exam_name' => $examPricing['exam_name'],
            'quantity' => $quantity,
            'unit_price' => number_format($userPrice, 2),
            'total_amount' => number_format($totalAmount, 2),
            'external_price' => $examPricing['external_price'],
            'savings_per_unit' => $examPricing['savings'] ?? '0.00',
            'total_savings' => number_format(((float) str_replace(',', '', $examPricing['savings'] ?? '0')) * $quantity, 2),
            'source' => $examPricing['source'] ?? 'unknown'
        ];
    }

    private function calculateSpecificRechargePinPricing(Request $request, array $basePricing, string $userType): array
    {
        $network = $request->network;
        $denomination = $request->amount ?? $request->denomination ?? 100;
        $quantity = $request->quantity ?? 1;

        // Find recharge pin pricing
        $pinPricing = collect($basePricing)->first(function ($pin) use ($network, $denomination) {
            return strtolower($pin['network_code']) === strtolower($network) && 
                   $pin['denomination'] == $denomination;
        });

        if (!$pinPricing) {
            throw new \Exception('Recharge pin not available for this network and denomination');
        }

        $userPrice = (float) str_replace(',', '', $pinPricing['user_price']);
        $totalAmount = $userPrice * $quantity;

        return [
            'network' => $network,
            'denomination' => $denomination,
            'quantity' => $quantity,
            'unit_price' => number_format($userPrice, 2),
            'total_amount' => number_format($totalAmount, 2),
            'external_price' => $pinPricing['external_price'],
            'savings_per_unit' => $pinPricing['savings'] ?? '0.00',
            'total_savings' => number_format(((float) str_replace(',', '', $pinPricing['savings'] ?? '0')) * $quantity, 2),
            'source' => $pinPricing['source'] ?? 'unknown'
        ];
    }

    private function calculateSpecificAlphaTopupPricing(Request $request, array $basePricing, string $userType): array
    {
        $planId = $request->plan_id;
        $amount = $request->amount;
        $quantity = $request->quantity ?? 1;

        // Find alpha topup pricing by plan_id or amount
        $alphaPricing = collect($basePricing)->first(function ($plan) use ($planId, $amount) {
            if ($planId) {
                return $plan['plan_id'] == $planId;
            } elseif ($amount) {
                return $plan['amount'] == $amount;
            }
            return false;
        });

        if (!$alphaPricing) {
            throw new \Exception('Alpha Topup plan not found');
        }

        $userPrice = (float) str_replace(',', '', $alphaPricing['user_price']);
        $totalAmount = $userPrice * $quantity;

        return [
            'plan_id' => $alphaPricing['plan_id'],
            'amount' => $alphaPricing['amount'],
            'quantity' => $quantity,
            'unit_price' => number_format($userPrice, 2),
            'total_amount' => number_format($totalAmount, 2),
            'external_price' => $alphaPricing['external_price'],
            'savings_per_unit' => $alphaPricing['savings'] ?? '0.00',
            'total_savings' => number_format(((float) str_replace(',', '', $alphaPricing['savings'] ?? '0')) * $quantity, 2),
            'source' => $alphaPricing['source'] ?? 'unknown'
        ];
    }
}
