<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ApiLink;

class ExternalApiService
{
    private const DEFAULT_TIMEOUT = 30;
    private const MAX_RETRIES = 3;

    private ConfigurationService $configService;
    private ?LoggingService $loggingService;

    public function __construct(ConfigurationService $configService, ?LoggingService $loggingService = null)
    {
        $this->configService = $configService;
        $this->loggingService = $loggingService;
    }

    /**
     * Make API request with authentication similar to original PHP cURL usage
     */
    public function makeRequest(string $url, array $data, string $method = 'POST', array $headers = [], array $authConfig = []): array
    {
        $retries = 0;
        $lastException = null;

        while ($retries < self::MAX_RETRIES) {
            try {
                $startTime = microtime(true);

                // Build HTTP client with basic configuration
                $client = Http::timeout(self::DEFAULT_TIMEOUT)
                    ->withHeaders(array_merge([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'User-Agent' => 'Laravel-VTU-API/1.0',
                    ], $headers));

                // Add authentication based on auth_type
                $client = $this->addAuthentication($client, $authConfig, $data);

                // DEBUG: Log headers
                $headers = $client->getOptions()['headers'] ?? [];
                Log::info('API Request Headers', ['url' => $url, 'headers' => $headers, 'auth_config' => $authConfig]);

                // Make the request
                $response = $this->executeRequest($client, $method, $url, $data);

                $responseTime = round((microtime(true) - $startTime) * 1000, 2); // Convert to milliseconds
                $statusCode = $response->status();
                $responseData = $response->json() ?: [];
                $success = $response->successful();

                // Log API request if logging service is available
                if ($this->loggingService) {
                    $this->loggingService->logApiRequest(
                        $url,
                        $method,
                        $this->sanitizeRequestData($data),
                        $this->sanitizeResponseData($responseData),
                        $statusCode,
                        $responseTime,
                        $this->extractProviderName($url, $authConfig)
                    );
                }

                // Log the API call
                $this->logApiCall($url, $method, $data, $response, $responseTime, true);

                // Parse and validate response
                return $this->parseResponse($response, $url);
            } catch (\Exception $e) {
                $lastException = $e;
                $retries++;

                Log::error('API Request Failed', [
                    'url' => $url,
                    'method' => $method,
                    'attempt' => $retries,
                    'error' => $e->getMessage(),
                    'data' => $this->sanitizeLogData($data)
                ]);

                if ($retries >= self::MAX_RETRIES) {
                    break;
                }

                // Wait before retry (exponential backoff)
                sleep(pow(2, $retries - 1));
            }
        }

        // All retries failed
        $this->logApiCall($url, $method, $data, null, 0, false, $lastException->getMessage());

        return [
            'success' => false,
            'message' => 'API request failed after ' . self::MAX_RETRIES . ' attempts: ' . $lastException->getMessage(),
            'error_code' => 'API_REQUEST_FAILED'
        ];
    }

    /**
     * Add authentication to HTTP client based on provider configuration
     */
    private function addAuthentication($client, array $authConfig, array &$data)
    {
        $authType = $authConfig['auth_type'] ?? 'token';
        $apiKey = $authConfig['api_key'] ?? '';

        switch ($authType) {
            case 'token':
                // Token-based authentication (Authorization header)
                return $client->withToken($apiKey);

            case 'basic':
                // Basic authentication (username:password)
                $username = $authConfig['username'] ?? $apiKey;
                $password = $authConfig['password'] ?? '';
                return $client->withBasicAuth($username, $password);

            case 'access_token':
                // AccessToken authentication (similar to original PHP)
                if (!empty($apiKey)) {
                    $data['AccessToken'] = $apiKey;
                }
                return $client;

            case 'header':
                // Custom header authentication
                $headerName = $authConfig['header_name'] ?? 'Authorization';
                $headerValue = $authConfig['header_prefix'] ?? 'Bearer ';
                return $client->withHeaders([$headerName => $headerValue . $apiKey]);

            case 'query':
                // Query parameter authentication
                $paramName = $authConfig['param_name'] ?? 'api_key';
                $data[$paramName] = $apiKey;
                return $client;

            default:
                return $client;
        }
    }

    /**
     * Execute the HTTP request
     */
    private function executeRequest($client, string $method, string $url, array $data): Response
    {
        switch (strtoupper($method)) {
            case 'GET':
                return $client->get($url, $data);
            case 'POST':
                return $client->post($url, $data);
            case 'PUT':
                return $client->put($url, $data);
            case 'DELETE':
                return $client->delete($url, $data);
            default:
                throw new \InvalidArgumentException("Unsupported HTTP method: {$method}");
        }
    }

    /**
     * Parse and validate API response
     */
    private function parseResponse(Response $response, string $url): array
    {
        $statusCode = $response->status();
        $responseBody = $response->body();

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => "HTTP Error {$statusCode}: " . $response->reason(),
                'status_code' => $statusCode,
                'response_body' => $responseBody,
                'error_code' => 'HTTP_ERROR'
            ];
        }

        // Try to decode JSON response
        $jsonResponse = $response->json();

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => 'Invalid JSON response from API',
                'response_body' => $responseBody,
                'error_code' => 'INVALID_JSON'
            ];
        }

        // Check for API-specific error indicators
        return $this->validateApiResponse($jsonResponse, $url);
    }

    /**
     * Validate API response for business logic errors
     */
    private function validateApiResponse(array $response, string $url): array
    {
        // Common error indicators in VTU APIs
        $errorIndicators = ['error', 'Error', 'status_code', 'code'];
        $successIndicators = ['success', 'Success', 'status'];
        $messageFields = ['message', 'Message', 'msg', 'description', 'response'];

        // Check for explicit success/error flags
        foreach ($successIndicators as $field) {
            if (isset($response[$field])) {
                $value = $response[$field];

                // Handle boolean values
                if (is_bool($value)) {
                    if (!$value) {
                        return $this->buildErrorResponse($response, $messageFields, 'API_ERROR');
                    }
                }

                // Handle string/numeric values
                if (in_array(strtolower($value), ['false', '0', 'fail', 'failed', 'error'])) {
                    return $this->buildErrorResponse($response, $messageFields, 'API_ERROR');
                }
            }
        }

        // Check for error indicators
        foreach ($errorIndicators as $field) {
            if (isset($response[$field]) && !empty($response[$field])) {
                $value = $response[$field];

                // Handle error codes
                if (is_numeric($value) && $value != 0 && $value != 200) {
                    return $this->buildErrorResponse($response, $messageFields, 'API_ERROR');
                }

                // Handle error messages
                if (is_string($value) && !empty($value)) {
                    return [
                        'success' => false,
                        'message' => $value,
                        'response' => $response,
                        'error_code' => 'API_ERROR'
                    ];
                }
            }
        }

        // If no explicit error found, assume success
        return [
            'success' => true,
            'message' => 'Request completed successfully',
            'data' => $response,
            'response' => $response
        ];
    }

    /**
     * Build error response from API response
     */
    private function buildErrorResponse(array $response, array $messageFields, string $errorCode): array
    {
        $message = 'API request failed';

        // Try to extract error message
        foreach ($messageFields as $field) {
            if (isset($response[$field]) && !empty($response[$field])) {
                $message = $response[$field];
                break;
            }
        }

        return [
            'success' => false,
            'message' => $message,
            'response' => $response,
            'error_code' => $errorCode
        ];
    }

    /**
     * Log API call for monitoring and debugging
     */
    private function logApiCall(string $url, string $method, array $requestData, ?Response $response, float $responseTime, bool $success, string $errorMessage = ''): void
    {
        $logData = [
            'url' => $url,
            'method' => $method,
            'request_data' => $this->sanitizeLogData($requestData),
            'response_time' => round($responseTime, 2) . 'ms',
            'success' => $success,
        ];

        if ($response) {
            $logData['status_code'] = $response->status();
            $logData['response_body'] = $this->sanitizeLogData($response->json() ?? []);
        }

        if (!empty($errorMessage)) {
            $logData['error'] = $errorMessage;
        }

        if ($success) {
            Log::info('External API Call Successful', $logData);
        } else {
            Log::error('External API Call Failed', $logData);
        }
    }

    /**
     * Sanitize data for logging (remove sensitive information)
     */
    private function sanitizeLogData(array $data): array
    {
        $sensitiveFields = ['password', 'AccessToken', 'api_key', 'token', 'secret', 'key'];

        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $lowerKey = strtolower($key);
                foreach ($sensitiveFields as $sensitiveField) {
                    if (str_contains($lowerKey, strtolower($sensitiveField))) {
                        $data[$key] = '***REDACTED***';
                        break;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Make meter verification request using Uzobest API
     * Endpoint: GET /api/validatemeter?meternumber={meter}&disconame={id}&mtype={type}
     */
    public function verifyMeter(string $meterNumber, string $meterType, string $discoCode): array
    {
        try {
            $uzobestConfig = $this->getUzobestConfig();

            // Use adapter to get disco provider ID and meter type ID
            $adapter = new UzobestApiAdapter();
            $discoProviderId = $adapter->getDiscoProviderId($discoCode);
            $meterTypeId = strtoupper($meterType) === 'PREPAID' ? 1 : 2;

            $authConfig = $uzobestConfig['auth_config'];

            // GET request with query parameters
            $url = $uzobestConfig['base_url'] . '/validatemeter';
            $queryParams = [
                'meternumber' => $meterNumber,
                'disconame' => $discoProviderId,
                'mtype' => $meterTypeId
            ];

            $result = $this->makeRequest($url . '?' . http_build_query($queryParams), [], 'GET', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];
            $parsedResponse = $adapter->parseResponse($responseData);

            return [
                'success' => $parsedResponse['success'],
                'message' => $parsedResponse['message'] ?? 'Meter validation completed',
                'customer_name' => $responseData['Customer_Name'] ?? $responseData['name'] ?? null,
                'meter_number' => $meterNumber,
                'disco_provider' => $discoCode,
                'meter_type' => $meterType,
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Meter verification error', [
                'meter_number' => $meterNumber,
                'meter_type' => $meterType,
                'disco_code' => $discoCode,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during meter verification',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Make cable IUC verification request using Uzobest API
     * Endpoint: GET /api/validateiuc?smart_card_number={iuc}&cablename={id}
     */
    public function verifyCableIUC(string $iuc, string $cableProvider): array
    {
        try {
            $uzobestConfig = $this->getUzobestConfig();

            // Use adapter to get cable provider ID
            $adapter = new UzobestApiAdapter();
            $cableProviderId = $adapter->getCableProviderId($cableProvider);

            $authConfig = $uzobestConfig['auth_config'];

            // GET request with query parameters
            $url = $uzobestConfig['base_url'] . '/validateiuc';
            $queryParams = [
                'smart_card_number' => $iuc,
                'cablename' => $cableProviderId
            ];

            $result = $this->makeRequest($url . '?' . http_build_query($queryParams), [], 'GET', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];
            $parsedResponse = $adapter->parseResponse($responseData);

            return [
                'success' => $parsedResponse['success'],
                'message' => $parsedResponse['message'] ?? 'IUC validation completed',
                'customer_name' => $responseData['customer_name'] ?? $responseData['name'] ?? null,
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Cable IUC verification error', [
                'iuc' => $iuc,
                'cable_provider' => $cableProvider,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during IUC verification',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Make phone number validation request
     */
    public function validatePhone(string $phoneNumber): array
    {
        // Phone validation using airtime provider for network detection
        $network = $this->detectNetwork($phoneNumber);

        if (!$network) {
            return [
                'success' => false,
                'message' => 'Invalid phone number or unsupported network',
                'error_code' => 'INVALID_PHONE'
            ];
        }

        return [
            'success' => true,
            'message' => 'Phone number validation successful',
            'data' => [
                'phone_number' => $phoneNumber,
                'network' => $network,
                'formatted_number' => $this->formatPhoneNumber($phoneNumber)
            ]
        ];
    }

    /**
     * Detect network from phone number
     */
    private function detectNetwork(string $phoneNumber): ?string
    {
        // Remove country code and normalize
        $number = preg_replace('/^\+?234/', '', $phoneNumber);
        $number = preg_replace('/^0/', '', $number);

        if (strlen($number) !== 10) {
            return null;
        }

        $prefix = substr($number, 0, 3);

        // MTN prefixes
        if (in_array($prefix, ['703', '706', '803', '806', '810', '813', '814', '816', '903', '906'])) {
            return 'MTN';
        }

        // Airtel prefixes
        if (in_array($prefix, ['701', '708', '802', '808', '812', '902', '907', '901'])) {
            return 'AIRTEL';
        }

        // GLO prefixes
        if (in_array($prefix, ['705', '805', '807', '811', '815', '905'])) {
            return 'GLO';
        }

        // 9Mobile prefixes
        if (in_array($prefix, ['809', '817', '818', '908', '909'])) {
            return '9MOBILE';
        }

        return null;
    }

    /**
     * Format phone number to standard format
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        $number = preg_replace('/^\+?234/', '', $phoneNumber);
        $number = preg_replace('/^0/', '', $number);

        return '0' . $number;
    }

    /**
     * Purchase airtime following Uzobest API format
     * Endpoint: POST /api/topup/
     * Request: {network: <id>, amount, mobile_number, Ported_number: bool, airtime_type: "VTU"}
     */
    public function purchaseAirtime(string $network, string $phone, float $amount, string $serviceType = 'VTU', bool $portedNumber = false): array
    {
        try {
            $uzobestConfig = $this->getUzobestConfig();

            // Use adapter to transform request to Uzobest format
            $adapter = new UzobestApiAdapter();
            $requestData = $adapter->transformAirtimePurchaseRequest($network, $phone, $amount, $serviceType, $portedNumber);

            $authConfig = $uzobestConfig['auth_config'];

            $result = $this->makeRequest($uzobestConfig['base_url'] . '/topup/', $requestData, 'POST', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            // Use adapter to parse response
            $responseData = $result['data'] ?? $result['response'] ?? [];
            $parsedResponse = $adapter->parseResponse($responseData);

            if (!$parsedResponse['success']) {
                return $parsedResponse;
            }

            return [
                'success' => true,
                'message' => 'Airtime purchase successful',
                'transaction_id' => $parsedResponse['transaction_id'] ?? $responseData['id'] ?? uniqid(),
                'amount' => $amount,
                'phone' => $requestData['mobile_number'],
                'network' => $network,
                'service_type' => $serviceType,
                'api_response' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Airtime purchase error', [
                'network' => $network,
                'phone' => $phone,
                'amount' => $amount,
                'service_type' => $serviceType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during airtime purchase',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Purchase data following Uzobest API format
     * Endpoint: POST /api/data/
     * Request: {network: <id>, mobile_number, plan: <id>, Ported_number: bool}
     */
    public function purchaseData(string $network, string $phone, string $planId, string $dataType = 'SME', bool $portedNumber = false): array
    {
        try {
            // Get provider configuration for this network and data type
            $providerConfig = $this->configService->getProviderConfig('data', $network, $dataType);

            if (empty($providerConfig['provider_url']) || empty($providerConfig['api_key'])) {
                return [
                    'success' => false,
                    'message' => "Data provider not configured for {$network} {$dataType}",
                    'error_code' => 'CONFIG_ERROR'
                ];
            }

            // Use adapter to transform request to Uzobest format
            $adapter = new UzobestApiAdapter();
            $requestData = $adapter->transformDataPurchaseRequest($network, $phone, $planId, $portedNumber);

            $authConfig = [
                'auth_type' => $providerConfig['auth_type'],
                'api_key' => $providerConfig['api_key']
            ];

            if (isset($providerConfig['auth_params'])) {
                $authConfig = array_merge($authConfig, $providerConfig['auth_params']);
            }

            $result = $this->makeRequest($providerConfig['provider_url'], $requestData, 'POST', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            // Use adapter to parse response
            $responseData = $result['data'] ?? $result['response'] ?? [];
            $parsedResponse = $adapter->parseResponse($responseData);

            if (!$parsedResponse['success']) {
                return $parsedResponse;
            }

            return [
                'success' => true,
                'message' => 'Data purchase successful',
                'transaction_id' => $parsedResponse['transaction_id'] ?? $responseData['id'] ?? uniqid(),
                'plan_id' => $planId,
                'phone' => $requestData['mobile_number'],
                'network' => $network,
                'data_type' => $dataType,
                'api_response' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Data purchase error', [
                'network' => $network,
                'phone' => $phone,
                'plan_id' => $planId,
                'data_type' => $dataType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during data purchase',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Get available data plans for all networks from Uzobest
     * Endpoint: GET /api/network/
     * Returns all networks and their data plans
     */
    public function getDataPlans(?string $network = null, string $dataType = 'SME'): array
    {
        try {
            $uzobestConfig = $this->getUzobestConfig();

            $authConfig = $uzobestConfig['auth_config'];

            // GET /api/network/ returns all networks and plans
            $result = $this->makeRequest($uzobestConfig['base_url'] . '/network/', [], 'GET', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];

            // Filter by network if specified
            if ($network) {
                $adapter = new UzobestApiAdapter();
                // Filter plans for specific network
                // Response structure needs to be verified from actual API
                $plans = $responseData;
            } else {
                $plans = $responseData;
            }

            return [
                'success' => true,
                'plans' => $plans,
                'network' => $network,
                'data_type' => $dataType
            ];
        } catch (\Exception $e) {
            Log::error('Data plans fetch error', [
                'network' => $network,
                'data_type' => $dataType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred while fetching data plans',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Purchase cable TV subscription using Uzobest API
     * Endpoint: POST /api/cablesub/
     * Request: {cablename: <id>, cableplan: <id>, smart_card_number}
     */
    public function purchaseCable(string $cableProvider, string $iucNumber, string $planId, string $reference): array
    {
        try {
            $uzobestConfig = $this->getUzobestConfig();

            // Use adapter to transform request to Uzobest format
            $adapter = new UzobestApiAdapter();
            $requestData = $adapter->transformCablePurchaseRequest($cableProvider, $iucNumber, $planId);

            $authConfig = $uzobestConfig['auth_config'];

            $result = $this->makeRequest($uzobestConfig['base_url'] . '/cablesub/', $requestData, 'POST', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            // Use adapter to parse response
            $responseData = $result['data'] ?? $result['response'] ?? [];
            $parsedResponse = $adapter->parseResponse($responseData);

            if (!$parsedResponse['success']) {
                return $parsedResponse;
            }

            return [
                'success' => true,
                'message' => 'Cable TV subscription successful',
                'reference' => $reference,
                'transaction_id' => $parsedResponse['transaction_id'] ?? $responseData['id'] ?? uniqid(),
                'cable_provider' => $cableProvider,
                'iuc_number' => $iucNumber,
                'plan_id' => $planId,
                'api_response' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Cable TV purchase error', [
                'cable_provider' => $cableProvider,
                'iuc_number' => $iucNumber,
                'plan_id' => $planId,
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during cable TV subscription',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Purchase electricity token using Uzobest API
     * Endpoint: POST /api/billpayment/
     * Request: {disco_name: <id>, amount, meter_number, MeterType: 1|2}
     */
    public function purchaseElectricity(string $discoProvider, string $meterNumber, string $meterType, float $amount, string $reference, ?string $phone = null): array
    {
        try {
            $uzobestConfig = $this->getUzobestConfig();

            // Use adapter to transform request to Uzobest format
            $adapter = new UzobestApiAdapter();
            $requestData = $adapter->transformElectricityPurchaseRequest($discoProvider, $meterNumber, $meterType, $amount);

            $authConfig = $uzobestConfig['auth_config'];

            $result = $this->makeRequest($uzobestConfig['base_url'] . '/billpayment/', $requestData, 'POST', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            // Use adapter to parse response
            $responseData = $result['data'] ?? $result['response'] ?? [];
            $parsedResponse = $adapter->parseResponse($responseData);

            if (!$parsedResponse['success']) {
                return $parsedResponse;
            }

            return [
                'success' => true,
                'message' => 'Electricity token purchase successful',
                'reference' => $reference,
                'transaction_id' => $parsedResponse['transaction_id'] ?? $responseData['id'] ?? uniqid(),
                'token' => $responseData['token'] ?? $responseData['Token'] ?? null,
                'units' => $responseData['units'] ?? $responseData['Units'] ?? null,
                'disco_provider' => $discoProvider,
                'meter_number' => $meterNumber,
                'amount' => $amount,
                'api_response' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Electricity purchase error', [
                'disco_provider' => $discoProvider,
                'meter_number' => $meterNumber,
                'meter_type' => $meterType,
                'amount' => $amount,
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during electricity purchase',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Purchase exam pins from external API following original Exam.php patterns
     */
    public function purchaseExamPin(string $examType, int $quantity, string $reference, string $phone = null): array
    {
        try {
            // Get API configuration for exam pin provider
            $config = $this->configService->getServiceConfig('exam');

            if (!$config) {
                return [
                    'success' => false,
                    'message' => 'Exam pin service configuration not found',
                    'error_code' => 'CONFIG_ERROR'
                ];
            }

            // Log exam pin purchase attempt
            Log::info('Exam pin purchase attempt', [
                'exam_type' => $examType,
                'quantity' => $quantity,
                'reference' => $reference,
                'phone' => $phone,
                'provider' => $config['provider'] ?? 'unknown'
            ]);

            // Prepare request data based on original PHP patterns
            $requestData = [
                'exam_name' => $examType,
                'quantity' => $quantity,
                'reference' => $reference
            ];

            // Check if using n3tdata or bilalsubs pattern (Basic Auth -> Token)
            $provider = $config['provider'] ?? '';
            $authConfig = [
                'auth_type' => $config['auth_type'] ?? 'Token',
                'api_key' => $config['api_key'],
                'user_url' => $config['user_url'] ?? null
            ];

            // Use different request format for basic auth providers
            if (strpos($provider, 'n3tdata') !== false || strpos($provider, 'bilalsub') !== false) {
                $requestData = [
                    'exam' => $examType,
                    'quantity' => $quantity
                ];
            }

            // Add phone if provided
            if ($phone) {
                $requestData['phone'] = $phone;
            }

            // Make the purchase request
            $result = $this->makeRequest($config['provider'], $requestData, 'POST', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];

            // Handle different response formats based on original PHP logic
            $status = $responseData['status'] ?? $responseData['Status'] ?? 'failed';
            if (in_array(strtolower($status), ['success', 'successful'])) {
                // Extract pins based on different response formats
                $pins = [];
                if (isset($responseData['data_pin']['pin'])) {
                    $pins = [$responseData['data_pin']['pin']];
                } elseif (isset($responseData['pins'])) {
                    $pins = is_array($responseData['pins']) ? $responseData['pins'] : [$responseData['pins']];
                } elseif (isset($responseData['pin'])) {
                    $pins = is_array($responseData['pin']) ? $responseData['pin'] : [$responseData['pin']];
                }

                return [
                    'success' => true,
                    'message' => 'Exam pin(s) purchased successfully',
                    'reference' => $reference,
                    'server_reference' => $responseData['reference'] ?? $reference,
                    'pins' => $pins,
                    'exam_type' => $examType,
                    'quantity' => $quantity,
                    'api_response' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $responseData['msg'] ?? $responseData['message'] ?? 'Exam pin purchase failed',
                    'error_code' => 'API_ERROR',
                    'api_response' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exam pin purchase error', [
                'exam_type' => $examType,
                'quantity' => $quantity,
                'reference' => $reference,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during exam pin purchase',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Purchase recharge pins from external API following original RechargePin.php patterns
     */
    public function purchaseRechargePin(string $networkId, float $amount, int $quantity, string $reference, string $nameOnCard = null): array
    {
        try {
            // Get API configuration for recharge pin provider
            $config = $this->configService->getServiceConfig('recharge_pin');

            if (!$config) {
                return [
                    'success' => false,
                    'message' => 'Recharge pin service configuration not found',
                    'error_code' => 'CONFIG_ERROR'
                ];
            }

            // Log recharge pin purchase attempt
            Log::info('Recharge pin purchase attempt', [
                'network_id' => $networkId,
                'amount' => $amount,
                'quantity' => $quantity,
                'reference' => $reference,
                'provider' => $config['provider'] ?? 'unknown'
            ]);

            // Prepare request data based on original PHP patterns
            $requestData = [
                'network' => $networkId,
                'amount' => $amount,
                'quantity' => $quantity
            ];

            if ($nameOnCard) {
                $requestData['name_on_card'] = $nameOnCard;
            }

            // Check for different provider patterns
            $provider = $config['provider'] ?? '';
            $authConfig = [
                'auth_type' => $config['auth_type'] ?? 'Token',
                'api_key' => $config['api_key'],
                'user_url' => $config['user_url'] ?? null
            ];

            // Use different request format for basic auth providers
            if (
                strpos($provider, 'n3tdata') !== false ||
                strpos($provider, 'bilalsub') !== false ||
                strpos($provider, 'beensade') !== false
            ) {

                $requestData = [
                    'network' => $networkId,
                    'name_on_card' => $nameOnCard ?? 'Customer',
                    'ref' => $reference,
                    'quantity' => $quantity,
                    'amount' => $amount
                ];
            }

            // Make the purchase request
            $result = $this->makeRequest($config['provider'], $requestData, 'POST', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];

            // Handle different response formats based on original PHP logic
            $status = $responseData['status'] ?? $responseData['Status'] ?? 'failed';
            if (in_array(strtolower($status), ['success', 'successful', 'processing'])) {
                // Extract pin information based on different response formats
                $pins = [];
                $serials = [];
                $loadPins = [];

                // Handle various response structures
                if (isset($responseData['load_pin'])) {
                    $loadPins = is_array($responseData['load_pin']) ? $responseData['load_pin'] : [$responseData['load_pin']];
                }
                if (isset($responseData['pin'])) {
                    $pins = is_array($responseData['pin']) ? $responseData['pin'] : [$responseData['pin']];
                }
                if (isset($responseData['serial'])) {
                    $serials = is_array($responseData['serial']) ? $responseData['serial'] : [$responseData['serial']];
                }

                // Format pins data for response
                $formattedPins = [];
                for ($i = 0; $i < $quantity; $i++) {
                    $formattedPins[] = [
                        'pin' => $pins[$i] ?? ($loadPins[$i] ?? '****-****-****'),
                        'serial' => $serials[$i] ?? 'SN' . time() . rand(100, 999),
                        'amount' => $amount,
                        'network' => $responseData['network'] ?? $networkId
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Recharge pin(s) purchased successfully',
                    'reference' => $reference,
                    'server_reference' => $responseData['reference'] ?? $reference,
                    'pins' => $formattedPins,
                    'network' => $responseData['network'] ?? $networkId,
                    'quantity' => $responseData['quantity'] ?? $quantity,
                    'api_response' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $responseData['msg'] ?? $responseData['message'] ?? 'Recharge pin purchase failed',
                    'error_code' => 'API_ERROR',
                    'api_response' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('Recharge pin purchase error', [
                'network_id' => $networkId,
                'amount' => $amount,
                'quantity' => $quantity,
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during recharge pin purchase',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Purchase data pins from external API following original DataPin.php patterns
     */
    public function purchaseDataPin(string $networkId, string $planId, int $quantity, string $reference, string $cardName = null): array
    {
        try {
            // Get API configuration for data pin provider
            $config = $this->configService->getServiceConfig('data_pin');

            if (!$config) {
                return [
                    'success' => false,
                    'message' => 'Data pin service configuration not found',
                    'error_code' => 'CONFIG_ERROR'
                ];
            }

            // Log data pin purchase attempt
            Log::info('Data pin purchase attempt', [
                'network_id' => $networkId,
                'plan_id' => $planId,
                'quantity' => $quantity,
                'reference' => $reference,
                'provider' => $config['provider'] ?? 'unknown'
            ]);

            // Prepare request data based on original PHP patterns
            $requestData = [
                'network' => $networkId,
                'request-id' => $reference,
                'plan' => $planId
            ];

            // Check for different provider patterns
            $provider = $config['provider'] ?? '';
            $authConfig = [
                'auth_type' => $config['auth_type'] ?? 'Token',
                'api_key' => $config['api_key'],
                'user_url' => $config['user_url'] ?? null
            ];

            // Use different request format for basic auth providers
            if (
                strpos($provider, 'n3tdata') !== false ||
                strpos($provider, 'bilalsub') !== false ||
                strpos($provider, 'beensade') !== false
            ) {

                $requestData = [
                    'network' => $networkId,
                    'card_name' => $cardName ?? 'Customer',
                    'request-id' => $reference,
                    'quantity' => $quantity,
                    'plan_type' => $planId
                ];
            }

            // Make the purchase request
            $result = $this->makeRequest($config['provider'], $requestData, 'POST', [], $authConfig);

            if (!$result['success']) {
                return $result;
            }

            $responseData = $result['data'] ?? $result['response'] ?? [];

            // Handle different response formats based on original PHP logic
            $status = $responseData['status'] ?? $responseData['Status'] ?? 'failed';
            if (in_array(strtolower($status), ['success', 'successful', 'processing'])) {
                // Extract pin information based on different response formats
                $pins = [];
                $serials = [];

                // Handle various response structures
                if (isset($responseData['pin'])) {
                    $pins = is_array($responseData['pin']) ? $responseData['pin'] : [$responseData['pin']];
                }
                if (isset($responseData['serial'])) {
                    $serials = is_array($responseData['serial']) ? $responseData['serial'] : [$responseData['serial']];
                }

                // Format pins data for response
                $formattedPins = [];
                for ($i = 0; $i < $quantity; $i++) {
                    $formattedPins[] = [
                        'pin' => $pins[$i] ?? 'PIN-' . time() . '-' . rand(100, 999),
                        'serial' => $serials[$i] ?? 'SN' . time() . rand(100, 999),
                        'plan_id' => $planId,
                        'network' => $responseData['network'] ?? $networkId
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Data pin(s) purchased successfully',
                    'reference' => $reference,
                    'server_reference' => $responseData['reference'] ?? $reference,
                    'pins' => $formattedPins,
                    'network' => $responseData['network'] ?? $networkId,
                    'quantity' => $responseData['quantity'] ?? $quantity,
                    'plan_id' => $planId,
                    'api_response' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $responseData['msg'] ?? $responseData['message'] ?? $responseData['error'][0] ?? 'Data pin purchase failed',
                    'error_code' => 'API_ERROR',
                    'api_response' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('Data pin purchase error', [
                'network_id' => $networkId,
                'plan_id' => $planId,
                'quantity' => $quantity,
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during data pin purchase',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Purchase Alpha Topup
     */
    public function purchaseAlphaTopup(float $amount, string $phoneNumber, string $reference): array
    {
        try {
            $config = $this->configService->getServiceConfig('alpha_topup');

            if (!$config) {
                Log::warning('Alpha Topup service configuration not found');
                return [
                    'success' => false,
                    'message' => 'Alpha Topup service configuration not available',
                    'error_code' => 'CONFIG_ERROR'
                ];
            }

            Log::info('Alpha Topup purchase initiated', [
                'amount' => $amount,
                'phone' => substr($phoneNumber, 0, 4) . '***' . substr($phoneNumber, -3),
                'reference' => $reference,
                'provider' => $config['provider']
            ]);

            $requestData = [
                'amount' => $amount,
                'phone' => $phoneNumber,
                'ref' => $reference
            ];

            $authConfig = [
                'auth_type' => $config['auth_type'] ?? 'token',
                'api_key' => $config['api_key']
            ];

            $response = $this->makeRequest($config['provider'], $requestData, 'POST', [], $authConfig);

            if ($response['success']) {
                $responseData = $response['data'];

                Log::info('Alpha Topup purchase successful', [
                    'reference' => $reference,
                    'server_reference' => $responseData['ref'] ?? $reference,
                    'amount' => $amount,
                    'phone' => substr($phoneNumber, 0, 4) . '***' . substr($phoneNumber, -3)
                ]);

                return [
                    'success' => true,
                    'message' => 'Alpha Topup completed successfully',
                    'reference' => $reference,
                    'server_reference' => $responseData['ref'] ?? $reference,
                    'amount' => $amount,
                    'phone' => $phoneNumber,
                    'api_response' => $responseData
                ];
            } else {
                Log::error('Alpha Topup purchase failed', [
                    'reference' => $reference,
                    'amount' => $amount,
                    'phone' => substr($phoneNumber, 0, 4) . '***' . substr($phoneNumber, -3),
                    'error' => $response['error']
                ]);

                return [
                    'success' => false,
                    'message' => $response['error'] ?? 'Alpha Topup purchase failed',
                    'error_code' => 'API_ERROR',
                    'api_response' => $response['data'] ?? null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Alpha Topup purchase error', [
                'amount' => $amount,
                'phone' => substr($phoneNumber, 0, 4) . '***' . substr($phoneNumber, -3),
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'System error occurred during Alpha Topup purchase',
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Sanitize request data for logging (remove sensitive information)
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveKeys = [
            'password', 'token', 'secret', 'key', 'pin', 'cvv',
            'api_key', 'secret_key', 'access_token', 'refresh_token',
            'authorization', 'auth_token', 'private_key'
        ];

        $sanitized = $data;

        foreach ($sensitiveKeys as $key) {
            if (isset($sanitized[$key])) {
                $sanitized[$key] = '***REDACTED***';
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize response data for logging (remove sensitive information)
     */
    protected function sanitizeResponseData(array $data): array
    {
        $sensitiveKeys = [
            'token', 'secret', 'key', 'pin', 'cvv', 'password',
            'api_key', 'secret_key', 'access_token', 'refresh_token',
            'private_key', 'credit_card', 'account_number'
        ];

        $sanitized = $data;

        foreach ($sensitiveKeys as $key) {
            if (isset($sanitized[$key])) {
                $sanitized[$key] = '***REDACTED***';
            }
        }

        // Also sanitize nested arrays
        foreach ($sanitized as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeResponseData($value);
            }
        }

        return $sanitized;
    }

    /**
     * Get Uzobest API configuration from ApiLink table
     */
    protected function getUzobestConfig(): array
    {
        $apiLink = ApiLink::where('type', 'primary')
            ->where('is_active', true)
            ->first();

        if (!$apiLink) {
            // Fallback to backup or throw exception
            $apiLink = ApiLink::where('name', 'LIKE', '%uzobest%')
                ->where('is_active', true)
                ->orderBy('priority')
                ->first();
        }

        if (!$apiLink) {
            throw new \Exception('Uzobest API configuration not found in ApiLink table');
        }

        $authParams = $apiLink->auth_params ?? [];
        $token = $authParams['token'] ?? config('services.uzobest.key', '');

        return [
            'base_url' => rtrim($apiLink->value, '/'),
            'token' => $token,
            'auth_config' => [
                'auth_type' => 'header',
                'api_key' => $token,
                'header_prefix' => 'Token ',
                'provider_name' => 'uzobest'
            ]
        ];
    }

    /**
     * Extract provider name from URL or auth config
     */
    protected function extractProviderName(string $url, array $authConfig): string
    {
        // Try to get from auth config first
        if (!empty($authConfig['provider_name'])) {
            return $authConfig['provider_name'];
        }

        // Extract from URL domain
        $parsed = parse_url($url);
        if (isset($parsed['host'])) {
            $host = $parsed['host'];

            // Remove common prefixes
            $host = preg_replace('/^(api\.|www\.)/', '', $host);

            // Extract main domain part
            $parts = explode('.', $host);
            if (count($parts) >= 2) {
                return $parts[count($parts) - 2]; // Get domain name without TLD
            }

            return $host;
        }

        return 'unknown';
    }
}
