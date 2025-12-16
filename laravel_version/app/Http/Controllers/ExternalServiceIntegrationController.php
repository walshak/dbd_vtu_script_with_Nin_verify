<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\MonitoringService;
use App\Services\ConfigurationService;
use App\Services\ExternalApiService;

class ExternalServiceIntegrationController extends Controller
{
    protected $monitoringService;
    protected $configurationService;
    protected $externalApiService;

    public function __construct(
        MonitoringService $monitoringService,
        ConfigurationService $configurationService,
        ExternalApiService $externalApiService
    ) {
        $this->monitoringService = $monitoringService;
        $this->configurationService = $configurationService;
        $this->externalApiService = $externalApiService;
    }
    /**
     * Get real-time service health status
     */
    public function getServiceHealthStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'sometimes|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup',
            'detailed' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            if ($request->has('service_type')) {
                $healthStatus = $this->monitoringService->checkServiceHealth($request->service_type);

                return response()->json([
                    'status' => 'success',
                    'data' => $healthStatus
                ]);
            } else {
                $healthStatuses = $this->monitoringService->performHealthChecks();

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'services' => $healthStatuses,
                        'overall_status' => $this->calculateOverallStatus($healthStatuses),
                        'checked_at' => now()->toISOString()
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error getting service health status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get service health status'
            ], 500);
        }
    }

    /**
     * Get monitoring dashboard data
     */
    public function getMonitoringDashboard(Request $request)
    {
        try {
            $dashboardData = $this->monitoringService->getMonitoringDashboard();

            return response()->json([
                'status' => 'success',
                'data' => $dashboardData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting monitoring dashboard: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get monitoring dashboard data'
            ], 500);
        }
    }

    /**
     * Get real provider performance statistics
     */
    public function getProviderStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'sometimes|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup',
            'time_range' => 'sometimes|string|in:today,week,month,all'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid statistics parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $serviceType = $request->service_type ?? 'all';
            $timeRange = $request->time_range ?? 'week';

            if ($serviceType === 'all') {
                $services = ['airtime', 'data', 'cable', 'electricity', 'exam', 'recharge_pin', 'data_pin', 'alpha_topup'];
                $allStats = [];

                foreach ($services as $service) {
                    $allStats[$service] = $this->monitoringService->getProviderStatistics($service, $timeRange);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'time_range' => $timeRange,
                        'services' => $allStats,
                        'generated_at' => now()->toISOString()
                    ]
                ]);
            } else {
                $stats = $this->monitoringService->getProviderStatistics($serviceType, $timeRange);

                return response()->json([
                    'status' => 'success',
                    'data' => $stats
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error getting provider statistics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get provider statistics'
            ], 500);
        }
    }

    /**
     * Get response time trends
     */
    public function getResponseTimeTrends(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup',
            'hours' => 'sometimes|integer|min:1|max:168' // Max 1 week
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $serviceType = $request->service_type;
            $hours = $request->hours ?? 24;

            $trends = $this->monitoringService->getResponseTimeTrends($serviceType, $hours);

            return response()->json([
                'status' => 'success',
                'data' => $trends
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting response time trends: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get response time trends'
            ], 500);
        }
    }

    /**
     * Test specific service connection
     */
    public function testServiceConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup',
            'force_check' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid test parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $serviceType = $request->service_type;
            $forceCheck = $request->force_check ?? false;

            // Clear cache if force check is requested
            if ($forceCheck) {
                Cache::forget("health_check_{$serviceType}");
            }

            $connectionTest = $this->monitoringService->checkServiceHealth($serviceType);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'service_type' => $serviceType,
                    'test_result' => $connectionTest,
                    'tested_at' => now()->toISOString(),
                    'force_checked' => $forceCheck
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error testing service connection: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to test service connection'
            ], 500);
        }
    }

    /**
     * Private helper methods
     */

    private function performConnectionTest($provider, $endpoint, $testParameters = [])
    {
        $startTime = microtime(true);

        try {
            $headers = $this->buildRequestHeaders($provider);
            $requestData = array_merge($this->getTestRequestData($provider->service_type), $testParameters);

            $response = Http::timeout($endpoint->timeout)
                ->withHeaders($headers)
                ->send($endpoint->method, $endpoint->url, $requestData);

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

            $success = $response->successful();

            return [
                'success' => $success,
                'response_time' => round($responseTime, 2),
                'status_code' => $response->status(),
                'response_body' => $response->json() ?? $response->body(),
                'headers_sent' => $headers,
                'test_data_sent' => $requestData
            ];

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            return [
                'success' => false,
                'response_time' => round($responseTime, 2),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        }
    }

    private function getAvailableProviders($serviceType, $preferredProviderId = null)
    {
        // For compatibility with existing code, return mock data
        // In real implementation, this would be handled by MonitoringService
        return collect([
            (object)[
                'id' => 1,
                'name' => 'Primary Provider',
                'service_type' => $serviceType,
                'priority' => 1,
                'success_rate' => 95.5
            ]
        ]);
    }

    private function executeWithProvider($provider, $serviceType, $transactionData)
    {
        $startTime = microtime(true);

        try {
            $endpoint = $provider->endpoints->where('name', 'purchase')->first();
            if (!$endpoint) {
                throw new \Exception('Purchase endpoint not configured for provider');
            }

            $headers = $this->buildRequestHeaders($provider);
            $requestData = $this->buildRequestData($provider, $serviceType, $transactionData);

            $response = Http::timeout($endpoint->timeout)
                ->withHeaders($headers)
                ->send($endpoint->method, $endpoint->url, $requestData);

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $success = $response->successful() && $this->isSuccessfulResponse($response->json());

            return [
                'success' => $success,
                'response_time' => round($responseTime, 2),
                'provider_response' => $response->json(),
                'transaction_reference' => $this->extractTransactionReference($response->json()),
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            return [
                'success' => false,
                'response_time' => round($responseTime, 2),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        }
    }

    private function buildRequestHeaders($provider)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        if ($provider->api_key) {
            $headers['Authorization'] = 'Bearer ' . $provider->api_key;
        }

        if ($provider->additional_headers) {
            $additionalHeaders = json_decode($provider->additional_headers, true);
            if (is_array($additionalHeaders)) {
                $headers = array_merge($headers, $additionalHeaders);
            }
        }

        return $headers;
    }

    private function buildRequestData($provider, $serviceType, $transactionData)
    {
        // Transform transaction data to provider-specific format
        $requestData = [
            'service_type' => $serviceType,
            'reference' => $transactionData['reference'] ?? uniqid(),
            'amount' => $transactionData['amount'],
            'phone' => $transactionData['phone'] ?? null,
            'meter_number' => $transactionData['meter_number'] ?? null,
            'iuc_number' => $transactionData['iuc_number'] ?? null,
            'plan_id' => $transactionData['plan_id'] ?? null
        ];

        // Add provider-specific fields based on configuration
        if ($provider->request_format) {
            $format = json_decode($provider->request_format, true);
            if (is_array($format)) {
                $requestData = array_merge($requestData, $format);
            }
        }

        return array_filter($requestData);
    }

    private function getTestRequestData($serviceType)
    {
        $testData = [
            'airtime' => ['phone' => '08012345678', 'amount' => 100],
            'data' => ['phone' => '08012345678', 'plan_id' => 1],
            'cable_tv' => ['iuc_number' => '1234567890', 'plan_id' => 1],
            'electricity' => ['meter_number' => '12345678901', 'amount' => 1000],
            'exam_pin' => ['exam_type' => 'WAEC', 'quantity' => 1],
            'recharge_pin' => ['network' => 'MTN', 'denomination' => 100, 'quantity' => 1]
        ];

        return $testData[$serviceType] ?? [];
    }

    private function isSuccessfulResponse($response)
    {
        // Check common success indicators in provider responses
        if (is_array($response)) {
            return isset($response['status']) &&
                   (in_array($response['status'], ['success', 'successful', 'completed']) ||
                    (isset($response['response_code']) && $response['response_code'] === '200'));
        }
        return false;
    }

    private function extractTransactionReference($response)
    {
        // Extract transaction reference from provider response
        if (is_array($response)) {
            return $response['transaction_id'] ??
                   $response['reference'] ??
                   $response['txn_ref'] ??
                   null;
        }
        return null;
    }

    private function updateProviderStats($provider, $success)
    {
        // Statistics now handled by MonitoringService
        // This method kept for backward compatibility
        Log::info("Provider {$provider->name} transaction result: " . ($success ? 'success' : 'failure'));
    }

    // Stats calculation methods
    private function getProviderTransactionCount($provider, $timeRange)
    {
        // Mock implementation - in reality would query transaction logs
        return rand(50, 500);
    }

    private function getProviderSuccessCount($provider, $timeRange)
    {
        $total = $this->getProviderTransactionCount($provider, $timeRange);
        return round($total * ($provider->success_rate / 100));
    }

    private function getProviderFailureCount($provider, $timeRange)
    {
        $total = $this->getProviderTransactionCount($provider, $timeRange);
        $successful = $this->getProviderSuccessCount($provider, $timeRange);
        return $total - $successful;
    }

    private function calculateUptime($provider, $timeRange)
    {
        // Mock uptime calculation - in reality would track downtime periods
        return round(rand(95, 100), 1);
    }

    /**
     * Calculate overall system status from individual service statuses
     */
    private function calculateOverallStatus(array $serviceStatuses): string
    {
        $totalServices = count($serviceStatuses);
        $healthyServices = 0;

        foreach ($serviceStatuses as $status) {
            if ($status['status'] === 'healthy') {
                $healthyServices++;
            }
        }

        $healthPercentage = $totalServices > 0 ? ($healthyServices / $totalServices) * 100 : 0;

        if ($healthPercentage >= 90) {
            return 'healthy';
        } elseif ($healthPercentage >= 70) {
            return 'degraded';
        } elseif ($healthPercentage >= 50) {
            return 'unstable';
        } else {
            return 'critical';
        }
    }
}
