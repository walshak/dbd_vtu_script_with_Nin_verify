<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\FailoverService;

class FailoverController extends Controller
{
    protected $failoverService;

    public function __construct(FailoverService $failoverService)
    {
        $this->failoverService = $failoverService;
    }

    /**
     * Get overall failover system status
     */
    public function getStatus(Request $request)
    {
        try {
            $status = $this->failoverService->getFailoverStatus();

            return response()->json([
                'status' => 'success',
                'data' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get failover status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed provider status for specific service
     */
    public function getServiceStatus(Request $request, $serviceType)
    {
        $validator = Validator::make(['service_type' => $serviceType], [
            'service_type' => 'required|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid service type',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $status = $this->failoverService->getFailoverStatus();
            $serviceStatus = $status['services'][$serviceType] ?? null;

            if (!$serviceStatus) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Service status not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'service_type' => $serviceType,
                    'status' => $serviceStatus,
                    'timestamp' => $status['timestamp']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get service status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset circuit breaker for specific provider
     */
    public function resetCircuitBreaker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup',
            'provider_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = $this->failoverService->resetCircuitBreaker(
                $request->service_type,
                $request->provider_name
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Circuit breaker reset successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset circuit breaker',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test failover system with specific service
     */
    public function testFailover(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup',
            'test_data' => 'sometimes|array',
            'force_failover' => 'sometimes|boolean'
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
            $testData = $request->test_data ?? $this->getDefaultTestData($serviceType);

            // Execute test with failover
            $result = $this->failoverService->executeWithFailover($serviceType, $testData);

            return response()->json([
                'status' => 'success',
                'message' => 'Failover test completed',
                'data' => [
                    'service_type' => $serviceType,
                    'test_data' => $testData,
                    'result' => $result,
                    'tested_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failover test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get failover statistics and metrics
     */
    public function getStatistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_range' => 'sometimes|string|in:today,week,month',
            'service_type' => 'sometimes|string|in:airtime,data,cable,electricity,exam,recharge_pin,data_pin,alpha_topup'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $timeRange = $request->time_range ?? 'week';
            $serviceType = $request->service_type;

            // Get provider statistics from database
            $statistics = $this->getFailoverStatistics($timeRange, $serviceType);

            return response()->json([
                'status' => 'success',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get failover statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Configure failover settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'circuit_breaker_threshold' => 'sometimes|integer|min:1|max:10',
            'circuit_breaker_timeout' => 'sometimes|integer|min:60|max:3600',
            'max_retry_attempts' => 'sometimes|integer|min:1|max:5',
            'health_check_interval' => 'sometimes|integer|min:30|max:600'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid settings',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Update failover configuration
            $updatedSettings = [];

            if ($request->has('circuit_breaker_threshold')) {
                config(['services.failover.circuit_breaker_threshold' => $request->circuit_breaker_threshold]);
                $updatedSettings['circuit_breaker_threshold'] = $request->circuit_breaker_threshold;
            }

            if ($request->has('circuit_breaker_timeout')) {
                config(['services.failover.circuit_breaker_timeout' => $request->circuit_breaker_timeout]);
                $updatedSettings['circuit_breaker_timeout'] = $request->circuit_breaker_timeout;
            }

            if ($request->has('max_retry_attempts')) {
                config(['services.failover.max_retry_attempts' => $request->max_retry_attempts]);
                $updatedSettings['max_retry_attempts'] = $request->max_retry_attempts;
            }

            if ($request->has('health_check_interval')) {
                config(['services.failover.health_check_interval' => $request->health_check_interval]);
                $updatedSettings['health_check_interval'] = $request->health_check_interval;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Failover settings updated successfully',
                'data' => [
                    'updated_settings' => $updatedSettings,
                    'updated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update failover settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get default test data for service types
     */
    protected function getDefaultTestData(string $serviceType): array
    {
        $testData = [
            'airtime' => [
                'phone' => '08012345678',
                'amount' => 50,
                'network' => 'MTN'
            ],
            'data' => [
                'phone' => '08012345678',
                'plan_id' => 1,
                'network' => 'MTN'
            ],
            'cable' => [
                'iuc_number' => '1234567890',
                'package_id' => 1,
                'provider' => 'DSTV'
            ],
            'electricity' => [
                'meter_number' => '12345678901',
                'amount' => 1000,
                'disco' => 'IBEDC'
            ],
            'exam' => [
                'exam_type' => 'WAEC',
                'quantity' => 1
            ],
            'recharge_pin' => [
                'network' => 'MTN',
                'denomination' => 100,
                'quantity' => 1
            ],
            'data_pin' => [
                'network' => 'MTN',
                'plan' => '1GB',
                'quantity' => 1
            ],
            'alpha_topup' => [
                'account_id' => 'test_account',
                'amount' => 1000
            ]
        ];

        return $testData[$serviceType] ?? [];
    }

    /**
     * Get failover statistics from database
     */
    protected function getFailoverStatistics(string $timeRange, ?string $serviceType = null): array
    {
        // Mock implementation - in real scenario would query provider_statistics table
        $services = $serviceType ? [$serviceType] : ['airtime', 'data', 'cable', 'electricity', 'exam', 'recharge_pin', 'data_pin', 'alpha_topup'];
        $statistics = [];

        foreach ($services as $service) {
            $statistics[$service] = [
                'total_requests' => rand(100, 1000),
                'successful_requests' => rand(85, 950),
                'failed_requests' => rand(5, 50),
                'provider_switches' => rand(0, 10),
                'circuit_breaker_activations' => rand(0, 3),
                'average_response_time' => rand(500, 2000),
                'success_rate' => round(rand(85, 99) + (rand(0, 99) / 100), 2),
                'availability' => round(rand(95, 100) + (rand(0, 99) / 100), 2)
            ];
        }

        return [
            'time_range' => $timeRange,
            'services' => $statistics,
            'summary' => [
                'total_services' => count($services),
                'average_success_rate' => round(array_sum(array_column($statistics, 'success_rate')) / count($statistics), 2),
                'average_availability' => round(array_sum(array_column($statistics, 'availability')) / count($statistics), 2),
                'total_provider_switches' => array_sum(array_column($statistics, 'provider_switches')),
                'total_circuit_activations' => array_sum(array_column($statistics, 'circuit_breaker_activations'))
            ],
            'generated_at' => now()->toISOString()
        ];
    }
}
