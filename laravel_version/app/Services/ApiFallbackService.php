<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\ApiLog;
use App\Services\ApiMonitoringService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class ApiFallbackService
{
    protected $monitoringService;
    
    public function __construct(ApiMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Execute API call with fallback mechanisms
     */
    public function executeWithFallback($service, $primaryConfig, $requestData, $options = [])
    {
        $maxRetries = $options['max_retries'] ?? 3;
        $retryDelay = $options['retry_delay'] ?? 1; // seconds
        $enableFallback = $options['enable_fallback'] ?? true;
        
        // Try primary API first
        $result = $this->tryApiCall($service, $primaryConfig, $requestData, 'primary');
        
        if ($result['success']) {
            return $result;
        }
        
        // Check if we should retry primary API
        if ($maxRetries > 0 && $this->shouldRetry($result['error_type'])) {
            Log::info("Retrying primary API for {$service}", [
                'attempt' => 1,
                'max_retries' => $maxRetries
            ]);
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                sleep($retryDelay * $attempt); // Exponential backoff
                
                $retryResult = $this->tryApiCall($service, $primaryConfig, $requestData, "primary_retry_{$attempt}");
                
                if ($retryResult['success']) {
                    return $retryResult;
                }
            }
        }
        
        // Try fallback APIs if enabled
        if ($enableFallback) {
            $fallbackConfigs = $this->getFallbackConfigs($service);
            
            foreach ($fallbackConfigs as $index => $fallbackConfig) {
                Log::info("Trying fallback API for {$service}", [
                    'fallback_index' => $index,
                    'fallback_name' => $fallbackConfig['name'] ?? "Fallback {$index}"
                ]);
                
                $fallbackResult = $this->tryApiCall($service, $fallbackConfig, $requestData, "fallback_{$index}");
                
                if ($fallbackResult['success']) {
                    // Mark primary as potentially down
                    $this->monitoringService->checkForFailures($service);
                    
                    return $fallbackResult;
                }
            }
        }
        
        // All attempts failed
        $this->monitoringService->markApiDown($service, 'All API providers failed');
        
        return [
            'success' => false,
            'error' => 'All API providers are currently unavailable',
            'error_type' => 'all_providers_failed',
            'attempts' => $maxRetries + 1 + count($fallbackConfigs ?? [])
        ];
    }

    /**
     * Try a single API call
     */
    protected function tryApiCall($service, $config, $requestData, $attemptType = 'primary')
    {
        $startTime = microtime(true);
        $endpoint = $config['url'];
        
        try {
            // Check if this API is marked as down
            if ($this->monitoringService->isApiDown($service) && $attemptType === 'primary') {
                return [
                    'success' => false,
                    'error' => 'API is marked as down',
                    'error_type' => 'api_down'
                ];
            }
            
            // Prepare HTTP client
            $httpClient = Http::timeout($config['timeout'] ?? 30)
                             ->retry(1, 1000); // Basic retry
            
            // Add authentication
            if (isset($config['auth_type'])) {
                switch ($config['auth_type']) {
                    case 'bearer':
                        $httpClient = $httpClient->withToken($config['api_key']);
                        break;
                    case 'basic':
                        $httpClient = $httpClient->withBasicAuth($config['username'] ?? $config['api_key'], $config['password'] ?? '');
                        break;
                    case 'header':
                        $httpClient = $httpClient->withHeaders([
                            $config['auth_header'] ?? 'Authorization' => $config['api_key']
                        ]);
                        break;
                }
            }
            
            // Add custom headers
            if (isset($config['headers'])) {
                $httpClient = $httpClient->withHeaders($config['headers']);
            }
            
            // Make the API call
            $method = strtolower($config['method'] ?? 'post');
            
            if ($method === 'get') {
                $response = $httpClient->get($endpoint, $requestData);
            } else {
                $response = $httpClient->post($endpoint, $requestData);
            }
            
            $responseTime = (microtime(true) - $startTime) * 1000;
            
            // Check response
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Validate response format
                if ($this->isValidResponse($responseData, $config['response_format'] ?? [])) {
                    // Log successful call
                    $this->monitoringService->logApiCall(
                        $service,
                        $endpoint,
                        $requestData,
                        $responseData,
                        $responseTime,
                        'success'
                    );
                    
                    // Check for recovery if this was fallback
                    if ($attemptType !== 'primary') {
                        $this->monitoringService->checkForRecovery($service);
                    }
                    
                    return [
                        'success' => true,
                        'data' => $responseData,
                        'response_time' => $responseTime,
                        'attempt_type' => $attemptType,
                        'provider' => $config['name'] ?? 'unknown'
                    ];
                }
            }
            
            // Log failed call
            $errorType = $this->categorizeError($response->status(), $response->body());
            
            $this->monitoringService->logApiCall(
                $service,
                $endpoint,
                $requestData,
                $response->body(),
                $responseTime,
                $errorType
            );
            
            return [
                'success' => false,
                'error' => "API returned error: {$response->status()}",
                'error_type' => $errorType,
                'response_time' => $responseTime,
                'attempt_type' => $attemptType
            ];
            
        } catch (Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            $errorType = $this->categorizeException($e);
            
            // Log exception
            $this->monitoringService->logApiCall(
                $service,
                $endpoint,
                $requestData,
                $e->getMessage(),
                $responseTime,
                $errorType
            );
            
            Log::error("API call failed for {$service}", [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'attempt_type' => $attemptType
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => $errorType,
                'response_time' => $responseTime,
                'attempt_type' => $attemptType
            ];
        }
    }

    /**
     * Get fallback configurations for a service
     */
    protected function getFallbackConfigs($service)
    {
        $fallbacks = [];
        
        // Try to get fallback configurations from database
        $fallbackCount = Configuration::getValue("{$service}_fallback_count", 0);
        
        for ($i = 1; $i <= $fallbackCount; $i++) {
            $config = [
                'name' => Configuration::getValue("{$service}_fallback_{$i}_name"),
                'url' => Configuration::getValue("{$service}_fallback_{$i}_url"),
                'api_key' => Configuration::getValue("{$service}_fallback_{$i}_key"),
                'auth_type' => Configuration::getValue("{$service}_fallback_{$i}_auth_type", 'bearer'),
                'timeout' => Configuration::getValue("{$service}_fallback_{$i}_timeout", 30),
                'method' => Configuration::getValue("{$service}_fallback_{$i}_method", 'post')
            ];
            
            if ($config['url'] && $config['api_key']) {
                $fallbacks[] = $config;
            }
        }
        
        return $fallbacks;
    }

    /**
     * Check if we should retry based on error type
     */
    protected function shouldRetry($errorType)
    {
        $retryableErrors = [
            'timeout',
            'connection_error',
            'server_error',
            'rate_limit',
            'temporary_failure'
        ];
        
        return in_array($errorType, $retryableErrors);
    }

    /**
     * Validate API response format
     */
    protected function isValidResponse($responseData, $expectedFormat)
    {
        if (empty($expectedFormat)) {
            return true; // No validation required
        }
        
        // Check required fields
        if (isset($expectedFormat['required_fields'])) {
            foreach ($expectedFormat['required_fields'] as $field) {
                if (!isset($responseData[$field])) {
                    return false;
                }
            }
        }
        
        // Check success indicators
        if (isset($expectedFormat['success_field'])) {
            $successField = $expectedFormat['success_field'];
            $successValue = $expectedFormat['success_value'] ?? true;
            
            if (!isset($responseData[$successField]) || $responseData[$successField] !== $successValue) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Categorize HTTP error by status code
     */
    protected function categorizeError($statusCode, $responseBody)
    {
        switch ($statusCode) {
            case 400:
                return 'bad_request';
            case 401:
                return 'authentication_error';
            case 403:
                return 'authorization_error';
            case 404:
                return 'not_found';
            case 429:
                return 'rate_limit';
            case 500:
            case 502:
            case 503:
            case 504:
                return 'server_error';
            default:
                if ($statusCode >= 400 && $statusCode < 500) {
                    return 'client_error';
                } elseif ($statusCode >= 500) {
                    return 'server_error';
                } else {
                    return 'unknown_error';
                }
        }
    }

    /**
     * Categorize exception type
     */
    protected function categorizeException($exception)
    {
        $message = strtolower($exception->getMessage());
        
        if (strpos($message, 'timeout') !== false) {
            return 'timeout';
        } elseif (strpos($message, 'connection') !== false) {
            return 'connection_error';
        } elseif (strpos($message, 'dns') !== false) {
            return 'dns_error';
        } elseif (strpos($message, 'ssl') !== false || strpos($message, 'certificate') !== false) {
            return 'ssl_error';
        } else {
            return 'network_error';
        }
    }

    /**
     * Get API circuit breaker status
     */
    public function getCircuitBreakerStatus($service)
    {
        $cacheKey = "circuit_breaker_{$service}";
        
        return Cache::get($cacheKey, [
            'state' => 'closed', // closed, open, half_open
            'failure_count' => 0,
            'last_failure' => null,
            'next_attempt' => null
        ]);
    }

    /**
     * Update circuit breaker status
     */
    public function updateCircuitBreaker($service, $success)
    {
        $cacheKey = "circuit_breaker_{$service}";
        $status = $this->getCircuitBreakerStatus($service);
        
        $failureThreshold = Configuration::getValue("{$service}_circuit_breaker_threshold", 5);
        $timeout = Configuration::getValue("{$service}_circuit_breaker_timeout", 300); // 5 minutes
        
        if ($success) {
            // Reset on success
            $status = [
                'state' => 'closed',
                'failure_count' => 0,
                'last_failure' => null,
                'next_attempt' => null
            ];
        } else {
            // Increment failure count
            $status['failure_count']++;
            $status['last_failure'] = now();
            
            if ($status['failure_count'] >= $failureThreshold) {
                $status['state'] = 'open';
                $status['next_attempt'] = now()->addSeconds($timeout);
            }
        }
        
        Cache::put($cacheKey, $status, 3600); // Cache for 1 hour
        
        return $status;
    }

    /**
     * Check if circuit breaker allows request
     */
    public function canMakeRequest($service)
    {
        $status = $this->getCircuitBreakerStatus($service);
        
        if ($status['state'] === 'closed') {
            return true;
        }
        
        if ($status['state'] === 'open') {
            if (isset($status['next_attempt']) && now() >= $status['next_attempt']) {
                // Try half-open state
                $status['state'] = 'half_open';
                Cache::put("circuit_breaker_{$service}", $status, 3600);
                return true;
            }
            return false;
        }
        
        // Half-open state allows limited requests
        return true;
    }

    /**
     * Get service health summary
     */
    public function getHealthSummary()
    {
        $services = ['airtime', 'data', 'cable_tv', 'electricity', 'exam_pin', 'recharge_pin'];
        $summary = [];
        
        foreach ($services as $service) {
            $circuitBreaker = $this->getCircuitBreakerStatus($service);
            $isDown = $this->monitoringService->isApiDown($service);
            $canRequest = $this->canMakeRequest($service);
            
            $summary[$service] = [
                'name' => ucfirst(str_replace('_', ' ', $service)),
                'is_down' => $isDown,
                'can_make_request' => $canRequest,
                'circuit_breaker_state' => $circuitBreaker['state'],
                'failure_count' => $circuitBreaker['failure_count'],
                'health_status' => $this->determineHealthStatus($isDown, $canRequest, $circuitBreaker)
            ];
        }
        
        return $summary;
    }

    /**
     * Determine overall health status
     */
    protected function determineHealthStatus($isDown, $canRequest, $circuitBreaker)
    {
        if ($isDown || !$canRequest) {
            return 'critical';
        }
        
        if ($circuitBreaker['state'] === 'half_open' || $circuitBreaker['failure_count'] > 0) {
            return 'degraded';
        }
        
        return 'healthy';
    }
}