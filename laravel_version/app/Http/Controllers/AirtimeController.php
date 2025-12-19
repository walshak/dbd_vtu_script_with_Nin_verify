<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AirtimeService;
use App\Services\ExternalApiService;
use App\Services\LoggingService;
use App\Models\NetworkId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AirtimeController extends Controller
{
    protected $airtimeService;
    protected $externalApiService;
    protected $loggingService;

    public function __construct(
        AirtimeService $airtimeService,
        ExternalApiService $externalApiService,
        LoggingService $loggingService
    ) {
        $this->airtimeService = $airtimeService;
        $this->externalApiService = $externalApiService;
        $this->loggingService = $loggingService;
    }

    /**
     * Show airtime purchase page
     */
    public function index()
    {
        $networks = NetworkId::getSupportedNetworks();
        return view('airtime.index', compact('networks'));
    }

    /**
     * Get airtime pricing for a network
     */
    public function getPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid network selected'
            ], 400);
        }

        $pricing = $this->airtimeService->getAirtimePricing(
            Auth::user()->id,
            $request->network
        );

        return response()->json([
            'status' => 'success',
            'data' => $pricing
        ]);
    }

    /**
     * Purchase airtime
     */
    public function purchase(Request $request)
    {
        $startTime = microtime(true);

        // Log the incoming request
        $this->loggingService->logApiRequest(
            $request->path(),
            $request->method(),
            $request->except(['password', 'token']),
            [],
            0,
            0,
            'internal'
        );

        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'amount' => 'required|numeric|min:50|max:10000',
            'type' => 'sometimes|string|in:VTU,awuf4U,Share and Sell',
            'ported_number' => 'sometimes|in:true,false,0,1'
        ]);

        if ($validator->fails()) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log validation failure
            $this->loggingService->logTransaction(
                'airtime_purchase',
                'validation_failed',
                $request->all(),
                Auth::user()->id,
                [
                    'validation_errors' => $validator->errors(),
                    'response_time_ms' => $responseTime
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Convert ported_number to boolean
        $portedNumber = filter_var($request->ported_number ?? false, FILTER_VALIDATE_BOOLEAN);

        // Get pricing/discount for this network and service type
        $discountRates = $this->airtimeService->getDiscountRates($user->id, $request->network);
        $serviceType = $request->type ?? 'VTU';
        $discount = $serviceType === 'VTU' ? ($discountRates['vtu_discount'] ?? 0) : ($discountRates['share_and_sell_discount'] ?? 0);

        // Calculate selling price (amount user pays)
        $sellingPrice = $request->amount - ($request->amount * ($discount / 100));

        // Check wallet balance
        if ($user->wallet_balance < $sellingPrice) {
            return response()->json([
                'status' => 'error',
                'message' => sprintf(
                    'Insufficient wallet balance. You need ₦%s but have ₦%s',
                    number_format($sellingPrice, 2),
                    number_format($user->wallet_balance, 2)
                )
            ], 400);
        }

        // Use ExternalApiService directly (Uzobest only)
        try {

            $result = $this->externalApiService->purchaseAirtime(
                $request->network,
                $request->phone,
                $request->amount,
                $request->type ?? 'VTU',
                $portedNumber
            );

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Create transaction record
            $transactionRef = 'TXN' . strtoupper(uniqid());
            $transaction = null;

            if ($result['success'] ?? false) {
                try {
                    $serviceDesc = strtoupper($request->network) . ' Airtime - ' . $request->phone . ' (' . ($request->type ?? 'VTU') . ')';
                    $oldBalance = $user->wallet_balance;
                    $newBalance = $oldBalance - $sellingPrice;
                    $profit = $request->amount - $sellingPrice;

                    $transaction = \App\Models\Transaction::create([
                        'sId' => $user->id,
                        'transref' => $transactionRef,
                        // Old columns (for backward compatibility)
                        'servicename' => 'Airtime',
                        'servicedesc' => $serviceDesc,
                        'amount' => $request->amount,
                        'oldbal' => (string)$oldBalance,
                        'newbal' => (string)$newBalance,
                        'profit' => $profit,
                        // New columns (required)
                        'service_name' => 'airtime',
                        'service_description' => $serviceDesc,
                        'old_balance' => $oldBalance,
                        'new_balance' => $newBalance,
                        'api_response' => json_encode($result['api_response'] ?? []),
                        'status' => 1, // Success
                        'date' => now()
                    ]);

                    // Update user wallet
                    $user->wallet_balance = $newBalance;
                    $user->save();
                } catch (\Exception $e) {
                    \Log::warning('Failed to create transaction record', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Log transaction result (wrapped in try-catch to prevent logging failures from affecting response)
            try {
                $this->loggingService->logTransaction(
                    'airtime_purchase',
                    ($result['success'] ?? false) ? 'success' : 'error',
                    [
                        'phone' => $request->phone,
                        'amount' => $request->amount,
                        'network' => $request->network,
                        'type' => $request->type ?? 'VTU',
                        'ported_number' => $portedNumber,
                        'user_id' => $user->id,
                        'reference' => $transactionRef
                    ],
                    $user->id,
                    [
                        'provider_used' => 'uzobest',
                        'response_time_ms' => $responseTime,
                        'api_response' => $result
                    ]
                );

                // Log performance metrics
                $this->loggingService->logPerformance(
                    'airtime_purchase_complete',
                    $responseTime,
                    [
                        'amount' => $request->amount,
                        'network' => $request->network,
                        'provider_response_time' => $result['metadata']['response_time'] ?? 0
                    ],
                    [
                        'user_id' => $user->id,
                        'success' => $result['success'] ?? false
                    ]
                );
            } catch (\Exception $logException) {
                // Log the logging failure but don't let it affect the response
                \Log::warning('Failed to log airtime transaction', [
                    'error' => $logException->getMessage(),
                    'user_id' => $user->id
                ]);
            }

            if ($result['success'] ?? false) {
                // Transform response to match frontend expectations
                return response()->json([
                    'status' => 'success',
                    'message' => $result['message'] ?? 'Airtime purchase successful',
                    'data' => [
                        'reference' => $transactionRef,
                        'transaction_id' => $result['transaction_id'],
                        'amount' => $result['amount'],
                        'amount_paid' => $sellingPrice,
                        'discount' => $discount,
                        'you_saved' => $request->amount - $sellingPrice,
                        'phone' => $result['phone'],
                        'network' => strtoupper($result['network']),
                        'service_type' => $result['service_type'],
                        'new_balance' => $newBalance,
                        'api_response' => $result['api_response'] ?? null
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Transaction failed'
                ], $result['code'] ?? 400);
            }
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log the exception (also wrapped to prevent cascading failures)
            try {
                $this->loggingService->logError($e, [
                    'operation' => 'airtime_purchase',
                    'request_data' => [
                        'phone' => $request->phone,
                        'amount' => $request->amount,
                        'network' => $request->network,
                        'type' => $request->type ?? 'VTU',
                        'ported_number' => $portedNumber ?? false
                    ],
                    'user_id' => Auth::user()->id,
                    'response_time_ms' => $responseTime
                ]);
            } catch (\Exception $logException) {
                \Log::error('Failed to log airtime error', [
                    'original_error' => $e->getMessage(),
                    'log_error' => $logException->getMessage()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Transaction failed due to system error'
            ], 500);
        }
    }

    /**
     * Get transaction history for airtime
     */
    public function history()
    {
        $transactions = $this->airtimeService->getAirtimeHistory(Auth::user()->id);

        return view('user.airtime-history', compact('transactions'));
    }

    /**
     * API endpoint for mobile app
     */
    public function apiPurchase(Request $request)
    {
        $startTime = microtime(true);

        // Log API request for mobile
        $this->loggingService->logApiRequest(
            $request->path(),
            $request->method(),
            $request->except(['password', 'token']),
            [],
            0,
            0,
            'mobile_api'
        );

        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'amount' => 'required|numeric|min:50|max:10000',
            'type' => 'sometimes|string|in:VTU,awuf4U,Share and Sell',
            'ported_number' => 'sometimes|in:true,false,0,1'
        ]);

        if ($validator->fails()) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log validation failure for API
            $this->loggingService->logTransaction(
                'airtime_api_purchase',
                'validation_failed',
                $request->all(),
                Auth::user()->id,
                [
                    'validation_errors' => $validator->errors(),
                    'response_time_ms' => $responseTime,
                    'api_client' => 'mobile'
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Convert ported_number to boolean
        $portedNumber = filter_var($request->ported_number ?? false, FILTER_VALIDATE_BOOLEAN);

        // Use ExternalApiService directly (Uzobest only)
        try {
            $result = $this->externalApiService->purchaseAirtime(
                $request->network,
                $request->phone,
                $request->amount,
                $request->type ?? 'VTU',
                $portedNumber
            );

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log API transaction result
            $this->loggingService->logTransaction(
                'airtime_api_purchase',
                $result['status'] === 'success' ? 'success' : 'error',
                [
                    'phone' => $request->phone,
                    'amount' => $request->amount,
                    'network' => $request->network,
                    'type' => $request->type ?? 'VTU',
                    'ported_number' => $portedNumber,
                    'user_id' => Auth::user()->id
                ],
                Auth::user()->id,
                [
                    'provider_used' => 'uzobest',
                    'response_time_ms' => $responseTime,
                    'api_client' => 'mobile',
                    'api_response' => $result
                ]
            );

            return response()->json($result);
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log API exception
            $this->loggingService->logError($e, [
                'operation' => 'airtime_api_purchase',
                'request_data' => $requestData,
                'user_id' => Auth::user()->id,
                'response_time_ms' => $responseTime,
                'api_client' => 'mobile'
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Transaction failed due to system error'
            ], 500);
        }
    }
}
