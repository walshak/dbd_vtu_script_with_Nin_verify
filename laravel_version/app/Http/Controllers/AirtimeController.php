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
            Auth::id(),
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
                Auth::id(),
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

            // Log transaction result
            $this->loggingService->logTransaction(
                'airtime_purchase',
                $result['status'] === 'success' ? 'success' : 'error',
                [
                    'phone' => $request->phone,
                    'amount' => $request->amount,
                    'network' => $request->network,
                    'type' => $request->type ?? 'VTU',
                    'ported_number' => $portedNumber,
                    'user_id' => Auth::id()
                ],
                Auth::id(),
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
                    'user_id' => Auth::id(),
                    'success' => $result['status'] === 'success'
                ]
            );

            if ($result['status'] === 'success') {
                return response()->json($result);
            } else {
                return response()->json($result, $result['code'] ?? 400);
            }
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log the exception
            $this->loggingService->logError($e, [
                'operation' => 'airtime_purchase',
                'request_data' => [
                    'phone' => $request->phone,
                    'amount' => $request->amount,
                    'network' => $request->network,
                    'type' => $request->type ?? 'VTU',
                    'ported_number' => $portedNumber ?? false
                ],
                'user_id' => Auth::id(),
                'response_time_ms' => $responseTime
            ]);

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
        $transactions = $this->airtimeService->getAirtimeHistory(Auth::id());

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
                Auth::id(),
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
                    'user_id' => Auth::id()
                ],
                Auth::id(),
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
                'user_id' => Auth::id(),
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
