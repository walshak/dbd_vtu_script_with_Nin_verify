# API Integration Guide

## Overview

This guide provides detailed instructions for integrating with the VTU system APIs and managing external service provider integrations.

## Provider Management

### Adding a New Provider

1. **Configuration Setup**
   ```bash
   php artisan config:add-provider NewProvider --service=airtime
   ```

2. **Database Configuration**
   ```sql
   INSERT INTO api_configurations (service_type, provider, config_data, is_active, priority) 
   VALUES (
       'airtime', 
       'new_provider', 
       JSON_OBJECT(
           'base_url', 'https://api.newprovider.com',
           'api_key', 'your-api-key',
           'secret', 'your-secret',
           'timeout', 30
       ), 
       1, 
       5
   );
   ```

3. **Test Integration**
   ```bash
   php artisan test:provider new_provider --service=airtime --test-data='{"phone":"08012345678","amount":"100"}'
   ```

### Provider Configuration Structure

```php
[
    'provider_name' => [
        'base_url' => 'https://api.provider.com',
        'credentials' => [
            'api_key' => 'your-api-key',
            'secret_key' => 'your-secret-key',
            'username' => 'your-username',
            'password' => 'your-password'
        ],
        'endpoints' => [
            'airtime' => '/api/v1/airtime',
            'data' => '/api/v1/data',
            'cable' => '/api/v1/cable',
            'electricity' => '/api/v1/electricity',
            'exam' => '/api/v1/exam'
        ],
        'settings' => [
            'timeout' => 30,
            'retry_attempts' => 3,
            'retry_delay' => 1000, // milliseconds
            'circuit_breaker' => [
                'failure_threshold' => 5,
                'recovery_timeout' => 300,
                'success_threshold' => 3
            ]
        ],
        'request_format' => [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer {api_key}'
            ],
            'body_format' => 'json', // json, form, xml
            'auth_method' => 'bearer' // bearer, basic, header, query
        ],
        'response_mapping' => [
            'success_field' => 'status',
            'success_value' => 'success',
            'message_field' => 'message',
            'data_field' => 'data',
            'reference_field' => 'reference'
        ]
    ]
]
```

## Service Integration Examples

### Airtime Service Integration

```php
use App\Services\ExternalApiService;

class AirtimeService
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function purchaseAirtime(array $requestData): array
    {
        // Validate request data
        $validated = $this->validateAirtimeRequest($requestData);

        // Prepare API request
        $apiData = [
            'phone' => $validated['phone'],
            'network' => $validated['network'],
            'amount' => $validated['amount'],
            'reference' => $this->generateReference()
        ];

        // Make API call with failover
        return $this->apiService->makeRequest('airtime', 'primary_provider', $apiData);
    }

    private function validateAirtimeRequest(array $data): array
    {
        return validator($data, [
            'phone' => 'required|regex:/^0[789][01]\d{8}$/',
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'amount' => 'required|numeric|min:50|max:20000'
        ])->validate();
    }

    private function generateReference(): string
    {
        return 'AIR_' . time() . '_' . Str::random(6);
    }
}
```

### Data Service Integration

```php
class DataService
{
    public function purchaseData(array $requestData): array
    {
        $validated = $this->validateDataRequest($requestData);

        // Get data plan details
        $plan = $this->getDataPlan($validated['plan_id']);

        $apiData = [
            'phone' => $validated['phone'],
            'network' => $plan['network'],
            'plan_id' => $validated['plan_id'],
            'amount' => $plan['amount'],
            'reference' => $this->generateReference()
        ];

        return $this->apiService->makeRequest('data', 'primary_provider', $apiData);
    }

    private function getDataPlan(string $planId): array
    {
        return DataPlan::where('plan_id', $planId)
                      ->where('is_active', true)
                      ->firstOrFail()
                      ->toArray();
    }
}
```

### Cable TV Service Integration

```php
class CableTVService
{
    public function subscribeCable(array $requestData): array
    {
        $validated = $this->validateCableRequest($requestData);

        // Verify customer details
        $customer = $this->verifyCableCustomer($validated['smartcard'], $validated['provider']);

        $apiData = [
            'smartcard' => $validated['smartcard'],
            'provider' => $validated['provider'],
            'package_id' => $validated['package_id'],
            'customer_name' => $customer['name'],
            'amount' => $validated['amount'],
            'reference' => $this->generateReference()
        ];

        return $this->apiService->makeRequest('cable', 'primary_provider', $apiData);
    }

    private function verifyCableCustomer(string $smartcard, string $provider): array
    {
        $verificationData = [
            'smartcard' => $smartcard,
            'provider' => $provider
        ];

        $response = $this->apiService->makeRequest('cable_verify', 'primary_provider', $verificationData);

        if (!$response['success']) {
            throw new \Exception('Invalid smartcard number');
        }

        return $response['data'];
    }
}
```

## Provider-Specific Implementations

### Alphano API Integration

```php
class AlphanoProvider implements ProviderInterface
{
    protected $config;

    public function makeRequest(string $endpoint, array $data): array
    {
        $url = $this->config['base_url'] . $this->config['endpoints'][$endpoint];
        
        $payload = [
            'api_key' => $this->config['api_key'],
            'secret_key' => $this->config['secret_key'],
            ...$data
        ];

        $response = Http::timeout($this->config['timeout'])
                       ->post($url, $payload);

        return $this->formatResponse($response);
    }

    private function formatResponse($response): array
    {
        $data = $response->json();

        return [
            'success' => $data['status'] === 'success',
            'message' => $data['message'] ?? '',
            'data' => $data['data'] ?? [],
            'reference' => $data['reference'] ?? null,
            'provider_response' => $data
        ];
    }
}
```

### VTU Pro API Integration

```php
class VtuProProvider implements ProviderInterface
{
    public function makeRequest(string $endpoint, array $data): array
    {
        $url = $this->config['base_url'] . '/api';
        
        $payload = [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'service' => $endpoint,
            'details' => $data
        ];

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_token']
            ])
            ->timeout($this->config['timeout'])
            ->post($url, $payload);

        return $this->formatVtuProResponse($response);
    }
}
```

## Error Handling

### Standard Error Responses

```php
class ApiErrorHandler
{
    public function handleApiError(\Exception $exception, string $provider): array
    {
        // Log the error
        Log::error('API Error', [
            'provider' => $provider,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Categorize error type
        $errorType = $this->categorizeError($exception);

        // Trigger failover if needed
        if ($this->shouldTriggerFailover($errorType)) {
            $this->triggerFailover($provider, $errorType);
        }

        return [
            'success' => false,
            'message' => $this->getUserFriendlyMessage($errorType),
            'error_code' => $errorType,
            'provider' => $provider
        ];
    }

    private function categorizeError(\Exception $exception): string
    {
        if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
            return 'CONNECTION_ERROR';
        }

        if ($exception instanceof \Illuminate\Http\Client\RequestException) {
            $statusCode = $exception->response->status();
            return match($statusCode) {
                401, 403 => 'AUTHENTICATION_ERROR',
                404 => 'ENDPOINT_NOT_FOUND',
                429 => 'RATE_LIMIT_EXCEEDED',
                500, 502, 503, 504 => 'SERVER_ERROR',
                default => 'HTTP_ERROR'
            };
        }

        return 'UNKNOWN_ERROR';
    }
}
```

### Provider Failover Logic

```php
class ProviderFailoverManager
{
    public function handleProviderFailure(string $service, string $provider, string $errorType): void
    {
        // Record the failure
        $this->recordFailure($service, $provider, $errorType);

        // Check if circuit breaker should be triggered
        $failureCount = $this->getRecentFailureCount($service, $provider);
        
        if ($failureCount >= config('services.circuit_breaker.threshold')) {
            $this->openCircuitBreaker($service, $provider);
        }

        // Update provider priority
        $this->adjustProviderPriority($service, $provider, $errorType);
    }

    private function openCircuitBreaker(string $service, string $provider): void
    {
        Cache::put(
            "circuit_breaker:{$service}:{$provider}",
            [
                'status' => 'OPEN',
                'opened_at' => now(),
                'timeout' => now()->addMinutes(config('services.circuit_breaker.timeout'))
            ],
            now()->addHours(24)
        );

        // Notify administrators
        event(new CircuitBreakerOpened($service, $provider));
    }
}
```

## Testing Provider Integrations

### Unit Testing

```php
class AirtimeServiceTest extends TestCase
{
    public function test_airtime_purchase_success()
    {
        // Mock API response
        Http::fake([
            'api.provider.com/*' => Http::response([
                'status' => 'success',
                'message' => 'Airtime purchase successful',
                'data' => ['reference' => 'TXN123456'],
                'reference' => 'TXN123456'
            ], 200)
        ]);

        $service = app(AirtimeService::class);
        
        $result = $service->purchaseAirtime([
            'phone' => '08012345678',
            'network' => 'mtn',
            'amount' => '100'
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('TXN123456', $result['reference']);
    }

    public function test_airtime_purchase_with_failover()
    {
        // Mock primary provider failure
        Http::fake([
            'api.primary.com/*' => Http::response(['error' => 'Server error'], 500),
            'api.backup.com/*' => Http::response([
                'status' => 'success',
                'message' => 'Backup provider success',
                'reference' => 'BACKUP123'
            ], 200)
        ]);

        $result = $this->airtimeService->purchaseAirtime([
            'phone' => '08012345678',
            'network' => 'mtn', 
            'amount' => '100'
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('BACKUP123', $result['reference']);
    }
}
```

### Integration Testing

```php
class ProviderIntegrationTest extends TestCase
{
    /** @test */
    public function test_all_providers_airtime_integration()
    {
        $providers = config('services.providers');
        
        foreach ($providers as $providerName => $config) {
            if (!$config['test_mode']) {
                continue;
            }

            $result = $this->testProviderAirtime($providerName);
            
            $this->assertTrue($result['success'], "Provider {$providerName} failed airtime test");
        }
    }

    private function testProviderAirtime(string $provider): array
    {
        $testData = [
            'phone' => config('services.test_phone'),
            'network' => 'mtn',
            'amount' => '50' // Minimum test amount
        ];

        return app(ExternalApiService::class)->makeRequest('airtime', $provider, $testData);
    }
}
```

## Monitoring and Analytics

### API Performance Monitoring

```php
class ApiPerformanceMonitor
{
    public function recordApiCall(string $service, string $provider, array $metrics): void
    {
        // Store performance metrics
        DB::table('api_performance_logs')->insert([
            'service_type' => $service,
            'provider' => $provider,
            'response_time' => $metrics['response_time'],
            'success' => $metrics['success'],
            'status_code' => $metrics['status_code'],
            'error_code' => $metrics['error_code'] ?? null,
            'created_at' => now()
        ]);

        // Update real-time metrics
        $this->updateRealTimeMetrics($service, $provider, $metrics);
    }

    private function updateRealTimeMetrics(string $service, string $provider, array $metrics): void
    {
        $key = "metrics:{$service}:{$provider}";
        
        $current = Cache::get($key, [
            'total_requests' => 0,
            'successful_requests' => 0,
            'total_response_time' => 0,
            'last_updated' => now()
        ]);

        $current['total_requests']++;
        $current['total_response_time'] += $metrics['response_time'];
        
        if ($metrics['success']) {
            $current['successful_requests']++;
        }

        $current['avg_response_time'] = $current['total_response_time'] / $current['total_requests'];
        $current['success_rate'] = ($current['successful_requests'] / $current['total_requests']) * 100;
        $current['last_updated'] = now();

        Cache::put($key, $current, now()->addHours(24));
    }
}
```

### Provider Health Scoring

```php
class ProviderHealthScorer
{
    public function calculateHealthScore(string $provider): float
    {
        $metrics = $this->getProviderMetrics($provider);
        
        // Weight factors for scoring
        $weights = [
            'success_rate' => 0.4,        // 40% weight
            'response_time' => 0.25,      // 25% weight
            'uptime' => 0.2,              // 20% weight
            'error_rate' => 0.15          // 15% weight
        ];

        $scores = [
            'success_rate' => min(100, $metrics['success_rate']) / 100,
            'response_time' => $this->normalizeResponseTime($metrics['avg_response_time']),
            'uptime' => $metrics['uptime_percentage'] / 100,
            'error_rate' => max(0, (100 - $metrics['error_rate']) / 100)
        ];

        $weightedScore = 0;
        foreach ($weights as $metric => $weight) {
            $weightedScore += $scores[$metric] * $weight;
        }

        return round($weightedScore * 100, 2);
    }

    private function normalizeResponseTime(float $responseTime): float
    {
        // Normalize response time to 0-1 scale
        // Anything under 1000ms gets full score
        // Anything over 5000ms gets 0 score
        return max(0, min(1, (5000 - $responseTime) / 4000));
    }
}
```

This comprehensive API integration guide covers all aspects of managing external service providers, from configuration and testing to monitoring and failover handling.
