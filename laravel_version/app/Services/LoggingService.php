<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class LoggingService
{
    protected $configurationService;
    protected $defaultChannel = 'daily';
    protected $externalLoggers = [];

    // Log levels
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    // Log categories for VTU system
    const CATEGORY_TRANSACTION = 'transaction';
    const CATEGORY_AUTHENTICATION = 'authentication';
    const CATEGORY_API_REQUEST = 'api_request';
    const CATEGORY_SYSTEM = 'system';
    const CATEGORY_SECURITY = 'security';
    const CATEGORY_PERFORMANCE = 'performance';
    const CATEGORY_ERROR = 'error';
    const CATEGORY_AUDIT = 'audit';

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
        $this->initializeExternalLoggers();
    }

    /**
     * Initialize external logging services
     */
    protected function initializeExternalLoggers(): void
    {
        try {
            // Defensive check to ensure configurationService is properly initialized
            if (!$this->configurationService) {
                Log::warning('ConfigurationService not available during LoggingService initialization');
                return;
            }

            // Check if the method exists before calling it
            if (!method_exists($this->configurationService, 'getConfiguration')) {
                Log::warning('ConfigurationService::getConfiguration method not found');
                return;
            }

            $loggingConfig = $this->configurationService->getConfiguration('logging');

            if ($loggingConfig && isset($loggingConfig['external_services'])) {
                $this->externalLoggers = $loggingConfig['external_services'];
            }
        } catch (Exception $e) {
            Log::error('Failed to initialize external loggers: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'service_class' => get_class($this->configurationService ?? 'NULL'),
                'service_methods' => $this->configurationService ? get_class_methods($this->configurationService) : []
            ]);
        }
    }

    /**
     * Log transaction events with structured data
     */
    public function logTransaction(
        string $transactionType,
        string $status,
        array $transactionData,
        string $userId = null,
        array $metadata = []
    ): void {
        $logData = [
            'category' => self::CATEGORY_TRANSACTION,
            'transaction_type' => $transactionType,
            'status' => $status,
            'user_id' => $userId,
            'transaction_data' => $this->sanitizeData($transactionData),
            'metadata' => $metadata,
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        $level = $status === 'success' ? self::INFO : self::ERROR;
        $message = "Transaction {$transactionType}: {$status}";

        $this->log($level, $message, $logData);

        // Store transaction log in database for analytics
        $this->storeTransactionLog($logData);
    }

    /**
     * Log API requests and responses
     */
    public function logApiRequest(
        string $endpoint,
        string $method,
        array $requestData,
        array $responseData,
        int $statusCode,
        float $responseTime,
        string $provider = null
    ): void {
        $logData = [
            'category' => self::CATEGORY_API_REQUEST,
            'endpoint' => $endpoint,
            'method' => $method,
            'provider' => $provider,
            'request_data' => $this->sanitizeData($requestData),
            'response_data' => $this->sanitizeData($responseData),
            'status_code' => $statusCode,
            'response_time_ms' => $responseTime,
            'timestamp' => now()->toISOString(),
            'success' => $statusCode >= 200 && $statusCode < 300
        ];

        $level = $logData['success'] ? self::INFO : self::ERROR;
        $message = "API Request to {$endpoint}: {$statusCode} ({$responseTime}ms)";

        $this->log($level, $message, $logData);

        // Store API metrics
        $this->storeApiMetrics($logData);
    }

    /**
     * Log authentication events
     */
    public function logAuthentication(
        string $event,
        string $username = null,
        bool $success = true,
        array $metadata = []
    ): void {
        $logData = [
            'category' => self::CATEGORY_AUTHENTICATION,
            'event' => $event,
            'username' => $username,
            'success' => $success,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
            'timestamp' => now()->toISOString()
        ];

        $level = $success ? self::INFO : self::WARNING;
        $message = "Authentication {$event}: " . ($success ? 'success' : 'failed');

        if (!$success) {
            $level = self::ERROR;
        }

        $this->log($level, $message, $logData);

        // Track failed login attempts
        if (!$success && $event === 'login') {
            $this->trackFailedLogin(request()->ip(), $username);
        }
    }

    /**
     * Log security events
     */
    public function logSecurity(
        string $event,
        string $severity,
        array $details = [],
        string $userId = null
    ): void {
        $logData = [
            'category' => self::CATEGORY_SECURITY,
            'event' => $event,
            'severity' => $severity,
            'user_id' => $userId,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        $level = $this->mapSeverityToLevel($severity);
        $message = "Security Event: {$event} ({$severity})";

        $this->log($level, $message, $logData);

        // Alert on high-severity security events
        if (in_array($severity, ['high', 'critical'])) {
            $this->sendSecurityAlert($logData);
        }
    }

    /**
     * Log system performance metrics
     */
    public function logPerformance(
        string $operation,
        float $duration,
        array $metrics = [],
        array $metadata = []
    ): void {
        $logData = [
            'category' => self::CATEGORY_PERFORMANCE,
            'operation' => $operation,
            'duration_ms' => $duration,
            'metrics' => $metrics,
            'metadata' => $metadata,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'timestamp' => now()->toISOString()
        ];

        $level = $duration > 5000 ? self::WARNING : self::DEBUG; // Warn on slow operations
        $message = "Performance: {$operation} completed in {$duration}ms";

        $this->log($level, $message, $logData);

        // Store performance metrics for analysis
        $this->storePerformanceMetrics($logData);
    }

    /**
     * Log system errors with context
     */
    public function logError(
        Exception $exception,
        array $context = [],
        string $category = self::CATEGORY_ERROR
    ): void {
        $logData = [
            'category' => $category,
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $this->sanitizeTrace($exception->getTrace()),
            'context' => $context,
            'timestamp' => now()->toISOString()
        ];

        $this->log(self::ERROR, $exception->getMessage(), $logData);

        // Track error patterns
        $this->trackErrorPattern($exception, $context);
    }

    /**
     * Log audit events for compliance
     */
    public function logAudit(
        string $action,
        string $resource,
        array $changes = [],
        string $userId = null
    ): void {
        $logData = [
            'category' => self::CATEGORY_AUDIT,
            'action' => $action,
            'resource' => $resource,
            'changes' => $changes,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString()
        ];

        $this->log(self::INFO, "Audit: {$action} on {$resource}", $logData);

        // Store audit trail in database
        $this->storeAuditLog($logData);
    }

    /**
     * Core logging method with external service integration
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        // Add common context data
        $context = array_merge($context, [
            'environment' => app()->environment(),
            'server' => gethostname(),
            'request_id' => $this->getRequestId(),
            'logged_at' => now()->toISOString()
        ]);

        // Log to Laravel's default logger
        Log::channel($this->defaultChannel)->{$level}($message, $context);

        // Send to external logging services
        $this->sendToExternalLoggers($level, $message, $context);
    }

    /**
     * Send logs to external logging services
     */
    protected function sendToExternalLoggers(string $level, string $message, array $context): void
    {
        foreach ($this->externalLoggers as $service => $config) {
            if (!$config['enabled'] ?? false) {
                continue;
            }

            try {
                switch ($service) {
                    case 'elasticsearch':
                        $this->sendToElasticsearch($level, $message, $context, $config);
                        break;

                    case 'logstash':
                        $this->sendToLogstash($level, $message, $context, $config);
                        break;

                    case 'slack':
                        if ($this->shouldNotifySlack($level, $context)) {
                            $this->sendToSlack($level, $message, $context, $config);
                        }
                        break;

                    case 'webhook':
                        $this->sendToWebhook($level, $message, $context, $config);
                        break;

                    case 'sentry':
                        if (in_array($level, ['error', 'critical', 'emergency'])) {
                            $this->sendToSentry($level, $message, $context, $config);
                        }
                        break;
                }
            } catch (Exception $e) {
                // Don't fail the main application if external logging fails
                Log::error("Failed to send log to {$service}: " . $e->getMessage());
            }
        }
    }

    /**
     * Send logs to Elasticsearch
     */
    protected function sendToElasticsearch(string $level, string $message, array $context, array $config): void
    {
        $index = $config['index'] ?? 'vtu-logs';
        $url = $config['url'] . "/{$index}/_doc";

        $logDocument = [
            '@timestamp' => now()->toISOString(),
            'level' => $level,
            'message' => $message,
            'application' => 'vtu-system',
            'environment' => app()->environment()
        ] + $context;

        Http::timeout(5)
            ->withHeaders($config['headers'] ?? [])
            ->post($url, $logDocument);
    }

    /**
     * Send logs to Logstash
     */
    protected function sendToLogstash(string $level, string $message, array $context, array $config): void
    {
        $logEntry = [
            'timestamp' => now()->toISOString(),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];

        Http::timeout(5)
            ->withHeaders($config['headers'] ?? [])
            ->post($config['url'], $logEntry);
    }

    /**
     * Send critical alerts to Slack
     */
    protected function sendToSlack(string $level, string $message, array $context, array $config): void
    {
        $color = $this->getSlackColor($level);
        $emoji = $this->getSlackEmoji($level);

        $payload = [
            'text' => "{$emoji} VTU System Alert",
            'attachments' => [
                [
                    'color' => $color,
                    'fields' => [
                        [
                            'title' => 'Level',
                            'value' => strtoupper($level),
                            'short' => true
                        ],
                        [
                            'title' => 'Message',
                            'value' => $message,
                            'short' => false
                        ],
                        [
                            'title' => 'Environment',
                            'value' => app()->environment(),
                            'short' => true
                        ],
                        [
                            'title' => 'Time',
                            'value' => now()->format('Y-m-d H:i:s T'),
                            'short' => true
                        ]
                    ]
                ]
            ]
        ];

        Http::timeout(5)->post($config['webhook_url'], $payload);
    }

    /**
     * Send logs to custom webhook
     */
    protected function sendToWebhook(string $level, string $message, array $context, array $config): void
    {
        $payload = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => now()->toISOString(),
            'application' => 'vtu-system'
        ];

        Http::timeout(5)
            ->withHeaders($config['headers'] ?? [])
            ->post($config['url'], $payload);
    }

    /**
     * Send error logs to Sentry
     */
    protected function sendToSentry(string $level, string $message, array $context, array $config): void
    {
        // This would integrate with Sentry SDK if configured
        // For now, just log the attempt
        Log::debug("Would send to Sentry: {$level} - {$message}");
    }

    /**
     * Store transaction logs in database for analytics
     */
    protected function storeTransactionLog(array $logData): void
    {
        try {
            DB::table('transaction_logs')->insert([
                'transaction_type' => $logData['transaction_type'],
                'status' => $logData['status'],
                'user_id' => $logData['user_id'],
                'data' => json_encode($logData['transaction_data']),
                'metadata' => json_encode($logData['metadata']),
                'ip_address' => $logData['ip_address'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to store transaction log: ' . $e->getMessage());
        }
    }

    /**
     * Store API metrics for performance analysis
     */
    protected function storeApiMetrics(array $logData): void
    {
        try {
            DB::table('api_metrics')->insert([
                'endpoint' => $logData['endpoint'],
                'method' => $logData['method'],
                'provider' => $logData['provider'],
                'status_code' => $logData['status_code'],
                'response_time' => $logData['response_time_ms'],
                'success' => $logData['success'],
                'date' => now()->format('Y-m-d'),
                'hour' => now()->format('H'),
                'created_at' => now()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to store API metrics: ' . $e->getMessage());
        }
    }

    /**
     * Store performance metrics
     */
    protected function storePerformanceMetrics(array $logData): void
    {
        try {
            DB::table('performance_metrics')->insert([
                'operation' => $logData['operation'],
                'duration_ms' => $logData['duration_ms'],
                'memory_usage' => $logData['memory_usage'],
                'peak_memory' => $logData['peak_memory'],
                'metrics' => json_encode($logData['metrics']),
                'created_at' => now()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to store performance metrics: ' . $e->getMessage());
        }
    }

    /**
     * Store audit logs for compliance
     */
    protected function storeAuditLog(array $logData): void
    {
        try {
            DB::table('audit_logs')->insert([
                'action' => $logData['action'],
                'resource' => $logData['resource'],
                'changes' => json_encode($logData['changes']),
                'user_id' => $logData['user_id'],
                'ip_address' => $logData['ip_address'],
                'created_at' => now()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to store audit log: ' . $e->getMessage());
        }
    }

    /**
     * Track failed login attempts for security monitoring
     */
    protected function trackFailedLogin(string $ipAddress, ?string $username): void
    {
        $key = "failed_login_{$ipAddress}";
        $attempts = Cache::get($key, 0) + 1;

        Cache::put($key, $attempts, 3600); // Track for 1 hour

        if ($attempts >= 5) {
            $this->logSecurity('brute_force_attempt', 'high', [
                'ip_address' => $ipAddress,
                'username' => $username,
                'attempt_count' => $attempts
            ]);
        }
    }

    /**
     * Track error patterns for analysis
     */
    protected function trackErrorPattern(Exception $exception, array $context): void
    {
        $pattern = get_class($exception) . ':' . $exception->getCode();
        $key = "error_pattern_{$pattern}";

        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, 3600);

        // Alert on high error frequency
        if ($count >= 10) {
            $this->logSecurity('error_pattern_detected', 'medium', [
                'pattern' => $pattern,
                'count' => $count,
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Send security alerts for high-priority events
     */
    protected function sendSecurityAlert(array $logData): void
    {
        // This could send emails, SMS, or push notifications
        Log::critical('Security Alert: ' . $logData['event'], $logData);
    }

    /**
     * Get unique request ID for tracking
     */
    protected function getRequestId(): string
    {
        return request()->header('X-Request-ID') ?? uniqid();
    }

    /**
     * Sanitize sensitive data from logs
     */
    protected function sanitizeData(array $data): array
    {
        $sensitiveKeys = [
            'password', 'token', 'secret', 'key', 'pin', 'cvv',
            'api_key', 'secret_key', 'access_token', 'refresh_token'
        ];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
            } elseif (is_string($key) && in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '***REDACTED***';
            }
        }

        return $data;
    }

    /**
     * Sanitize stack traces to remove sensitive paths
     */
    protected function sanitizeTrace(array $trace): array
    {
        return array_map(function ($item) {
            if (isset($item['file'])) {
                $item['file'] = basename($item['file']);
            }
            return $item;
        }, array_slice($trace, 0, 10)); // Limit trace depth
    }

    /**
     * Map severity to log level
     */
    protected function mapSeverityToLevel(string $severity): string
    {
        $mapping = [
            'critical' => self::CRITICAL,
            'high' => self::ERROR,
            'medium' => self::WARNING,
            'low' => self::INFO
        ];

        return $mapping[$severity] ?? self::INFO;
    }

    /**
     * Determine if Slack notification should be sent
     */
    protected function shouldNotifySlack(string $level, array $context): bool
    {
        $notifyLevels = ['error', 'critical', 'emergency'];

        if (in_array($level, $notifyLevels)) {
            return true;
        }

        // Notify on security events
        if (($context['category'] ?? '') === self::CATEGORY_SECURITY) {
            return true;
        }

        return false;
    }

    /**
     * Get Slack color for log level
     */
    protected function getSlackColor(string $level): string
    {
        $colors = [
            'emergency' => 'danger',
            'alert' => 'danger',
            'critical' => 'danger',
            'error' => 'danger',
            'warning' => 'warning',
            'notice' => 'good',
            'info' => 'good',
            'debug' => '#439FE0'
        ];

        return $colors[$level] ?? 'good';
    }

    /**
     * Get Slack emoji for log level
     */
    protected function getSlackEmoji(string $level): string
    {
        $emojis = [
            'emergency' => '🚨',
            'alert' => '⚠️',
            'critical' => '🔥',
            'error' => '❌',
            'warning' => '⚠️',
            'notice' => 'ℹ️',
            'info' => 'ℹ️',
            'debug' => '🐛'
        ];

        return $emojis[$level] ?? 'ℹ️';
    }

    /**
     * Get logging statistics for dashboard
     */
    public function getLoggingStatistics(string $timeRange = 'today'): array
    {
        $startDate = $this->getStartDate($timeRange);

        try {
            $stats = [
                'total_logs' => DB::table('transaction_logs')
                    ->where('created_at', '>=', $startDate)
                    ->count(),

                'error_logs' => DB::table('transaction_logs')
                    ->where('created_at', '>=', $startDate)
                    ->where('status', 'error')
                    ->count(),

                'transaction_types' => DB::table('transaction_logs')
                    ->where('created_at', '>=', $startDate)
                    ->select('transaction_type', DB::raw('count(*) as count'))
                    ->groupBy('transaction_type')
                    ->get()
                    ->pluck('count', 'transaction_type')
                    ->toArray(),

                'api_performance' => DB::table('api_metrics')
                    ->where('created_at', '>=', $startDate)
                    ->select(
                        DB::raw('AVG(response_time) as avg_response_time'),
                        DB::raw('MAX(response_time) as max_response_time'),
                        DB::raw('COUNT(*) as total_requests'),
                        DB::raw('SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_requests')
                    )
                    ->first()
            ];

            $stats['success_rate'] = $stats['api_performance']->total_requests > 0
                ? round(($stats['api_performance']->successful_requests / $stats['api_performance']->total_requests) * 100, 2)
                : 0;

            return $stats;

        } catch (Exception $e) {
            Log::error('Failed to get logging statistics: ' . $e->getMessage());
            return [
                'total_logs' => 0,
                'error_logs' => 0,
                'transaction_types' => [],
                'api_performance' => null,
                'success_rate' => 0
            ];
        }
    }

    /**
     * Get start date for time range
     */
    protected function getStartDate(string $timeRange): Carbon
    {
        switch ($timeRange) {
            case 'hour':
                return now()->subHour();
            case 'today':
                return now()->startOfDay();
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            default:
                return now()->startOfDay();
        }
    }
}
