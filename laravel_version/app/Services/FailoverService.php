<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FailoverService
{
    protected $configurationService;
    protected $externalApiService;
    protected $monitoringService;

    // Failover configuration
    protected $maxRetryAttempts = 3;
    protected $circuitBreakerThreshold = 5; // failures before circuit opens
    protected $circuitBreakerTimeout = 300; // seconds before retry
    protected $healthCheckInterval = 60; // seconds

    public function __construct(
        ConfigurationService $configurationService,
        ExternalApiService $externalApiService,
        MonitoringService $monitoringService
    ) {
        $this->configurationService = $configurationService;
        $this->externalApiService = $externalApiService;
        $this->monitoringService = $monitoringService;
    }

    /**
     * Execute service with automatic failover
     */
    public function executeWithFailover(string $serviceType, array $requestData): array
    {
        $providers = $this->getAvailableProviders($serviceType);

        if (empty($providers)) {
            return $this->createErrorResponse('No available providers for service', 503);
        }

        $executionResult = null;
        $attemptedProviders = [];

        foreach ($providers as $provider) {
            $providerName = $provider['name'];

            // Check circuit breaker status
            if ($this->isCircuitOpen($serviceType, $providerName)) {
                Log::warning("Circuit breaker open for {$providerName}, skipping");
                continue;
            }

            try {
                Log::info("Attempting {$serviceType} with provider: {$providerName}");

                $startTime = microtime(true);
                $result = $this->executeWithProvider($serviceType, $provider, $requestData);
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);

                $attemptedProviders[] = [
                    'provider' => $providerName,
                    'success' => $result['success'],
                    'response_time' => $responseTime,
                    'error' => $result['error'] ?? null
                ];

                if ($result['success']) {
                    // Mark provider as healthy
                    $this->markProviderHealthy($serviceType, $providerName);
                    $this->recordSuccessfulTransaction($serviceType, $providerName, $responseTime);

                    return $this->createSuccessResponse($result['data'], [
                        'provider_used' => $providerName,
                        'response_time' => $responseTime,
                        'attempted_providers' => $attemptedProviders,
                        'failover_used' => count($attemptedProviders) > 1
                    ]);
                } else {
                    // Record failure
                    $this->recordFailedAttempt($serviceType, $providerName, $result['error'] ?? 'Unknown error');
                    Log::warning("Provider {$providerName} failed: " . ($result['error'] ?? 'Unknown error'));
                }
            } catch (\Exception $e) {
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                $errorMessage = $e->getMessage();

                $attemptedProviders[] = [
                    'provider' => $providerName,
                    'success' => false,
                    'response_time' => $responseTime,
                    'error' => $errorMessage
                ];

                $this->recordFailedAttempt($serviceType, $providerName, $errorMessage);
                Log::error("Provider {$providerName} exception: {$errorMessage}");
            }
        }

        // All providers failed
        return $this->createErrorResponse('All providers failed', 503, [
            'service_type' => $serviceType,
            'attempted_providers' => $attemptedProviders,
            'total_providers_tried' => count($attemptedProviders)
        ]);
    }

    /**
     * Get available providers for service type, ordered by priority and health
     */
    protected function getAvailableProviders(string $serviceType): array
    {
        $cacheKey = "available_providers_{$serviceType}";

        return Cache::remember($cacheKey, 300, function () use ($serviceType) {
            $configurations = $this->configurationService->getAllConfigurations();
            $providers = [];

            foreach ($configurations as $config) {
                if ($config['service_type'] === $serviceType && $config['status'] === 'active') {
                    $healthScore = $this->getProviderHealthScore($serviceType, $config['name']);

                    $providers[] = [
                        'name' => $config['name'],
                        'config' => $config,
                        'health_score' => $healthScore,
                        'priority' => $config['priority'] ?? 5,
                        'is_healthy' => $healthScore > 0.7 // Consider healthy if score > 70%
                    ];
                }
            }

            // Sort by health status first, then priority
            usort($providers, function ($a, $b) {
                // Healthy providers first
                if ($a['is_healthy'] !== $b['is_healthy']) {
                    return $b['is_healthy'] <=> $a['is_healthy'];
                }

                // Then by health score
                if ($a['health_score'] !== $b['health_score']) {
                    return $b['health_score'] <=> $a['health_score'];
                }

                // Finally by priority (lower number = higher priority)
                return $a['priority'] <=> $b['priority'];
            });

            return $providers;
        });
    }

    /**
     * Execute request with specific provider
     */
    protected function executeWithProvider(string $serviceType, array $provider, array $requestData): array
    {
        $config = $provider['config'];
        $retryCount = 0;
        $lastError = null;

        while ($retryCount < $this->maxRetryAttempts) {
            try {
                switch ($serviceType) {
                    case 'airtime':
                        $result = $this->externalApiService->purchaseAirtime(
                            $requestData['network'],
                            $requestData['phone'],
                            $requestData['amount'],
                            $requestData['type'] ?? 'VTU',
                            $requestData['ported_number'] ?? false
                        );
                        break;

                    case 'data':
                        $result = $this->externalApiService->purchaseData(
                            $requestData['network'],
                            $requestData['phone'],
                            $requestData['plan_id'],
                            $requestData['data_type'] ?? 'SME',
                            $requestData['ported_number'] ?? false
                        );
                        break;

                    case 'cable':
                        $result = $this->externalApiService->purchaseCable(
                            $requestData['provider'],
                            $requestData['iuc_number'],
                            $requestData['package_id'],
                            $requestData['reference'] ?? uniqid()
                        );
                        break;

                    case 'electricity':
                        $result = $this->externalApiService->purchaseElectricity(
                            $requestData['disco'],
                            $requestData['meter_number'],
                            $requestData['meter_type'] ?? 'prepaid',
                            $requestData['amount'],
                            $requestData['reference'] ?? uniqid(),
                            $requestData['phone'] ?? null
                        );
                        break;

                    case 'exam':
                        $result = $this->externalApiService->purchaseExamPin(
                            $requestData['exam_type'],
                            $requestData['quantity'],
                            $requestData['reference'] ?? uniqid(),
                            $requestData['phone'] ?? null
                        );
                        break;

                    case 'recharge_pin':
                        $result = $this->externalApiService->purchaseRechargePin(
                            $requestData['network'],
                            $requestData['denomination'],
                            $requestData['quantity'],
                            $requestData['reference'] ?? uniqid(),
                            $requestData['name_on_card'] ?? null
                        );
                        break;

                    case 'data_pin':
                        $result = $this->externalApiService->purchaseDataPin(
                            $requestData['network'],
                            $requestData['plan_id'],
                            $requestData['quantity'],
                            $requestData['reference'] ?? uniqid(),
                            $requestData['card_name'] ?? null
                        );
                        break;

                    case 'alpha_topup':
                        $result = $this->externalApiService->purchaseAlphaTopup(
                            $requestData['amount'],
                            $requestData['phone_number'],
                            $requestData['reference'] ?? uniqid()
                        );
                        break;

                    default:
                        throw new \InvalidArgumentException("Unsupported service type: {$serviceType}");
                }

                if ($result['success']) {
                    return [
                        'success' => true,
                        'data' => $result
                    ];
                } else {
                    $lastError = $result['message'] ?? 'Provider returned failure';
                    $retryCount++;

                    if ($retryCount < $this->maxRetryAttempts) {
                        $delay = pow(2, $retryCount) * 1000; // Exponential backoff in milliseconds
                        usleep($delay * 1000); // Convert to microseconds
                    }
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                $retryCount++;

                if ($retryCount < $this->maxRetryAttempts) {
                    $delay = pow(2, $retryCount) * 1000; // Exponential backoff
                    usleep($delay * 1000);
                }
            }
        }

        return [
            'success' => false,
            'error' => $lastError ?? 'Max retry attempts exceeded'
        ];
    }

    /**
     * Check if circuit breaker is open for provider
     */
    protected function isCircuitOpen(string $serviceType, string $providerName): bool
    {
        $cacheKey = "circuit_breaker_{$serviceType}_{$providerName}";
        $circuitState = Cache::get($cacheKey);

        if (!$circuitState) {
            return false; // Circuit is closed
        }

        $failures = $circuitState['failures'] ?? 0;
        $lastFailure = Carbon::parse($circuitState['last_failure']);

        // Check if enough time has passed to retry
        if ($lastFailure->addSeconds($this->circuitBreakerTimeout)->isPast()) {
            // Reset circuit breaker
            Cache::forget($cacheKey);
            return false;
        }

        // Circuit is open if failures exceed threshold
        return $failures >= $this->circuitBreakerThreshold;
    }

    /**
     * Record failed attempt and potentially open circuit breaker
     */
    protected function recordFailedAttempt(string $serviceType, string $providerName, string $error): void
    {
        $cacheKey = "circuit_breaker_{$serviceType}_{$providerName}";
        $circuitState = Cache::get($cacheKey, ['failures' => 0]);

        $circuitState['failures']++;
        $circuitState['last_failure'] = now()->toISOString();
        $circuitState['last_error'] = $error;

        Cache::put($cacheKey, $circuitState, 3600); // Store for 1 hour

        // Log circuit breaker status
        if ($circuitState['failures'] >= $this->circuitBreakerThreshold) {
            Log::warning("Circuit breaker opened for {$providerName} on {$serviceType} after {$circuitState['failures']} failures");
        }

        // Record in monitoring service
        $this->recordProviderFailure($serviceType, $providerName, $error);
    }

    /**
     * Mark provider as healthy and reset circuit breaker
     */
    protected function markProviderHealthy(string $serviceType, string $providerName): void
    {
        $cacheKey = "circuit_breaker_{$serviceType}_{$providerName}";
        Cache::forget($cacheKey);

        // Record healthy status
        $healthKey = "provider_health_{$serviceType}_{$providerName}";
        Cache::put($healthKey, [
            'status' => 'healthy',
            'last_success' => now()->toISOString(),
            'consecutive_successes' => Cache::get($healthKey . '_success_count', 0) + 1
        ], 3600);
    }

    /**
     * Get provider health score (0-1)
     */
    protected function getProviderHealthScore(string $serviceType, string $providerName): float
    {
        $healthKey = "provider_health_{$serviceType}_{$providerName}";
        $circuitKey = "circuit_breaker_{$serviceType}_{$providerName}";

        $healthData = Cache::get($healthKey);
        $circuitData = Cache::get($circuitKey);

        $score = 1.0; // Start with perfect score

        // Reduce score based on circuit breaker failures
        if ($circuitData) {
            $failures = $circuitData['failures'] ?? 0;
            $score -= min(0.8, $failures * 0.1); // Reduce by 10% per failure, max 80% reduction
        }

        // Boost score for recent successes
        if ($healthData && isset($healthData['consecutive_successes'])) {
            $successes = min(10, $healthData['consecutive_successes']);
            $score += ($successes * 0.05); // Add 5% per success, max 50% boost
        }

        return max(0.0, min(1.0, $score));
    }

    /**
     * Record successful transaction
     */
    protected function recordSuccessfulTransaction(string $serviceType, string $providerName, float $responseTime): void
    {
        try {
            DB::table('provider_statistics')->updateOrInsert(
                [
                    'service_type' => $serviceType,
                    'provider_name' => $providerName,
                    'date' => now()->format('Y-m-d')
                ],
                [
                    'successful_transactions' => DB::raw('successful_transactions + 1'),
                    'total_transactions' => DB::raw('total_transactions + 1'),
                    'total_response_time' => DB::raw("total_response_time + {$responseTime}"),
                    'last_success_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Update success streak
            $healthKey = "provider_health_{$serviceType}_{$providerName}_success_count";
            Cache::increment($healthKey);
        } catch (\Exception $e) {
            Log::error("Failed to record successful transaction: " . $e->getMessage());
        }
    }

    /**
     * Record provider failure
     */
    protected function recordProviderFailure(string $serviceType, string $providerName, string $error): void
    {
        try {
            DB::table('provider_statistics')->updateOrInsert(
                [
                    'service_type' => $serviceType,
                    'provider_name' => $providerName,
                    'date' => now()->format('Y-m-d')
                ],
                [
                    'failed_transactions' => DB::raw('failed_transactions + 1'),
                    'total_transactions' => DB::raw('total_transactions + 1'),
                    'last_failure_at' => now(),
                    'last_error' => $error,
                    'updated_at' => now()
                ]
            );

            // Reset success streak
            $healthKey = "provider_health_{$serviceType}_{$providerName}_success_count";
            Cache::forget($healthKey);
        } catch (\Exception $e) {
            Log::error("Failed to record provider failure: " . $e->getMessage());
        }
    }

    /**
     * Get failover status for all services
     */
    public function getFailoverStatus(): array
    {
        $services = ['airtime', 'data', 'cable', 'electricity', 'exam', 'recharge_pin', 'data_pin', 'alpha_topup'];
        $status = [];

        foreach ($services as $serviceType) {
            $providers = $this->getAvailableProviders($serviceType);
            $totalProviders = count($providers);
            $healthyProviders = count(array_filter($providers, fn($p) => $p['is_healthy']));

            $circuitBreakers = [];
            foreach ($providers as $provider) {
                if ($this->isCircuitOpen($serviceType, $provider['name'])) {
                    $circuitBreakers[] = $provider['name'];
                }
            }

            $status[$serviceType] = [
                'total_providers' => $totalProviders,
                'healthy_providers' => $healthyProviders,
                'unhealthy_providers' => $totalProviders - $healthyProviders,
                'open_circuits' => count($circuitBreakers),
                'circuit_breaker_providers' => $circuitBreakers,
                'availability_percentage' => $totalProviders > 0 ? round(($healthyProviders / $totalProviders) * 100, 2) : 0
            ];
        }

        return [
            'services' => $status,
            'overall_health' => $this->calculateOverallFailoverHealth($status),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Force provider health check and circuit breaker reset
     */
    public function resetCircuitBreaker(string $serviceType, string $providerName): array
    {
        $circuitKey = "circuit_breaker_{$serviceType}_{$providerName}";
        $healthKey = "provider_health_{$serviceType}_{$providerName}";

        // Clear circuit breaker
        Cache::forget($circuitKey);

        // Perform immediate health check
        $healthCheck = $this->performProviderHealthCheck($serviceType, $providerName);

        if ($healthCheck['success']) {
            $this->markProviderHealthy($serviceType, $providerName);
        }

        return [
            'circuit_breaker_reset' => true,
            'health_check_result' => $healthCheck,
            'provider' => $providerName,
            'service_type' => $serviceType,
            'reset_at' => now()->toISOString()
        ];
    }

    /**
     * Perform health check for specific provider
     */
    protected function performProviderHealthCheck(string $serviceType, string $providerName): array
    {
        try {
            // Get provider configuration
            $providers = $this->getAvailableProviders($serviceType);
            $provider = collect($providers)->firstWhere('name', $providerName);

            if (!$provider) {
                return [
                    'success' => false,
                    'error' => 'Provider not found'
                ];
            }

            // Simple ping/status check
            $testData = $this->getTestDataForService($serviceType);
            $result = $this->executeWithProvider($serviceType, $provider, $testData);

            return [
                'success' => $result['success'],
                'error' => $result['error'] ?? null,
                'checked_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'checked_at' => now()->toISOString()
            ];
        }
    }

    /**
     * Get test data for service health check
     */
    protected function getTestDataForService(string $serviceType): array
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
     * Calculate overall failover system health
     */
    protected function calculateOverallFailoverHealth(array $serviceStatuses): array
    {
        $totalServices = count($serviceStatuses);
        $totalAvailability = array_sum(array_column($serviceStatuses, 'availability_percentage'));
        $averageAvailability = $totalServices > 0 ? $totalAvailability / $totalServices : 0;

        $healthyServices = count(array_filter($serviceStatuses, fn($s) => $s['availability_percentage'] >= 80));

        $overallStatus = 'critical';
        if ($averageAvailability >= 90) {
            $overallStatus = 'healthy';
        } elseif ($averageAvailability >= 70) {
            $overallStatus = 'degraded';
        } elseif ($averageAvailability >= 50) {
            $overallStatus = 'unstable';
        }

        return [
            'status' => $overallStatus,
            'average_availability' => round($averageAvailability, 2),
            'healthy_services' => $healthyServices,
            'total_services' => $totalServices,
            'health_percentage' => round(($healthyServices / $totalServices) * 100, 2)
        ];
    }

    /**
     * Create standardized success response
     */
    protected function createSuccessResponse(array $data, array $metadata = []): array
    {
        return [
            'status' => 'success',
            'data' => $data,
            'metadata' => array_merge([
                'processed_at' => now()->toISOString(),
                'failover_enabled' => true
            ], $metadata)
        ];
    }

    /**
     * Create standardized error response
     */
    protected function createErrorResponse(string $message, int $code = 500, array $data = []): array
    {
        return [
            'status' => 'error',
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];
    }
}
