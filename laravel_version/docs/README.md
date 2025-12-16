# VTU System Documentation

## Table of Contents

1. [System Overview](#system-overview)
2. [API Integration Guide](#api-integration-guide)
3. [Configuration Management](#configuration-management)
4. [Monitoring & Health Checks](#monitoring--health-checks)
5. [Failover & Circuit Breaker](#failover--circuit-breaker)
6. [Security Features](#security-features)
7. [Troubleshooting Guide](#troubleshooting-guide)
8. [Administrative Dashboard](#administrative-dashboard)
9. [Database Schema](#database-schema)
10. [Deployment Guide](#deployment-guide)

---

## System Overview

This VTU (Virtual Top-Up) system is a comprehensive Laravel-based platform that provides:

- **Multi-provider API integrations** for airtime, data, cable TV, electricity, and exam pins
- **Intelligent failover** with circuit breaker patterns
- **Real-time monitoring** and health checks
- **Advanced security** features and audit logging
- **Comprehensive admin dashboard** with analytics
- **Automated reconciliation** and transaction management

### Key Components

- **Services Layer**: Business logic for each VTU service type
- **Configuration System**: Dynamic provider management and settings
- **Monitoring Service**: Health checks and performance tracking
- **Failover Service**: Intelligent provider switching
- **Logging Service**: Comprehensive audit and performance logging
- **Dashboard Service**: Real-time metrics and analytics

### Architecture Overview

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Admin Panel   │    │   User Frontend  │    │   API Clients   │
├─────────────────┤    ├──────────────────┤    ├─────────────────┤
│   Controllers   │    │   Controllers    │    │   Webhooks      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
          │                       │                       │
          └───────────────────────┼───────────────────────┘
                                  │
                    ┌─────────────▼─────────────┐
                    │      Service Layer        │
                    │                           │
                    │  ┌─────────────────────┐  │
                    │  │ Configuration       │  │
                    │  │ Service             │  │
                    │  └─────────────────────┘  │
                    │                           │
                    │  ┌─────────────────────┐  │
                    │  │ External API        │  │
                    │  │ Service             │  │
                    │  └─────────────────────┘  │
                    │                           │
                    │  ┌─────────────────────┐  │
                    │  │ Failover Service    │  │
                    │  └─────────────────────┘  │
                    │                           │
                    │  ┌─────────────────────┐  │
                    │  │ Monitoring Service  │  │
                    │  └─────────────────────┘  │
                    │                           │
                    │  ┌─────────────────────┐  │
                    │  │ Logging Service     │  │
                    │  └─────────────────────┘  │
                    └───────────┬───────────────┘
                                │
                    ┌───────────▼───────────────┐
                    │     External APIs         │
                    │                           │
                    │ • Alphano API             │
                    │ • VTU Pro API             │
                    │ • ClubKonnect API         │
                    │ • DataDen API             │
                    │ • Alpha Topup API         │
                    └───────────────────────────┘
```

---

## API Integration Guide

### Supported Providers

The system supports multiple VTU service providers with automatic failover:

#### Primary Providers
- **Alphano API**: Full service support (airtime, data, cable, electricity)
- **VTU Pro**: Alternative provider for mobile services
- **ClubKonnect**: Data and cable TV specialist
- **DataDen**: Data bundle provider
- **Alpha Topup**: Alternative airtime/data provider

### Configuration Structure

Each provider is configured with the following structure:

```php
'provider_name' => [
    'base_url' => 'https://api.provider.com',
    'api_key' => 'your-api-key',
    'secret_key' => 'your-secret-key',
    'username' => 'your-username',
    'password' => 'your-password',
    'timeout' => 30,
    'retry_attempts' => 3,
    'endpoints' => [
        'airtime' => '/airtime',
        'data' => '/data',
        'cable' => '/cable',
        'electricity' => '/electricity',
        'exam' => '/exam',
        'recharge_pin' => '/recharge-pin'
    ],
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ]
]
```

### Adding New Providers

1. **Add Configuration**:
   ```bash
   php artisan config:add-provider ProviderName
   ```

2. **Update Database**:
   ```sql
   INSERT INTO api_configurations (service_type, provider, config_data, is_active) 
   VALUES ('airtime', 'new_provider', JSON_OBJECT(...), 1);
   ```

3. **Test Integration**:
   ```bash
   php artisan test:provider new_provider airtime
   ```

### Service Integration Examples

#### Airtime Purchase

```php
use App\Services\ExternalApiService;

$apiService = app(ExternalApiService::class);

$response = $apiService->makeRequest('airtime', 'alphano', [
    'phone' => '08012345678',
    'network' => 'mtn',
    'amount' => '100',
    'reference' => 'TXN_' . time()
]);
```

#### Data Bundle Purchase

```php
$response = $apiService->makeRequest('data', 'alphano', [
    'phone' => '08012345678',
    'network' => 'airtel',
    'plan_id' => 'AIR_1GB_30DAYS',
    'reference' => 'TXN_' . time()
]);
```

---

## Configuration Management

### Dynamic Configuration

The system uses a dynamic configuration system that allows real-time updates without deployment:

#### Configuration Service

Located at: `app/Services/ConfigurationService.php`

Key features:
- Database-driven configuration storage
- Environment-based overrides
- Cache optimization for performance
- Validation and sanitization

#### Configuration Types

1. **API Configurations**: Provider settings and credentials
2. **Service Parameters**: Network plans, pricing, limits
3. **Feature Toggles**: Enable/disable services
4. **Security Settings**: Rate limits, IP whitelisting

#### Managing Configurations

```php
// Get configuration
$config = app(ConfigurationService::class)->getServiceConfig('airtime', 'alphano');

// Update configuration
app(ConfigurationService::class)->updateServiceConfig('airtime', 'alphano', [
    'api_key' => 'new-api-key',
    'timeout' => 45
]);

// Add new provider
app(ConfigurationService::class)->addProvider('new_provider', [
    'base_url' => 'https://api.newprovider.com',
    'api_key' => 'api-key'
]);
```

#### Configuration Validation

All configurations are validated before storage:

```php
class ConfigurationValidator
{
    public function validateApiConfig(array $config): array
    {
        return [
            'base_url' => ['required', 'url'],
            'api_key' => ['required', 'string', 'min:10'],
            'timeout' => ['required', 'integer', 'min:5', 'max:120']
        ];
    }
}
```

---

## Monitoring & Health Checks

### Monitoring Service

Located at: `app/Services/MonitoringService.php`

The monitoring service provides comprehensive health checks and performance monitoring:

#### Health Check Types

1. **Service Health Checks**: API endpoint availability
2. **Response Time Monitoring**: Performance tracking
3. **Error Rate Analysis**: Failure pattern detection
4. **Provider Comparison**: Performance benchmarking

#### Health Check Schedule

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Run health checks every 5 minutes
    $schedule->command('monitoring:health-check')
             ->everyFiveMinutes()
             ->withoutOverlapping();
    
    // Generate monitoring reports hourly
    $schedule->command('monitoring:generate-reports')
             ->hourly();
}
```

#### Manual Health Checks

```bash
# Check all services
php artisan monitoring:health-check

# Check specific service
php artisan monitoring:health-check --service=airtime

# Check specific provider
php artisan monitoring:health-check --provider=alphano
```

#### Monitoring Dashboard

Access monitoring data via:
- Web Dashboard: `/admin/monitoring`
- API Endpoints: `/admin/dashboard/provider-health`
- Real-time updates: WebSocket connections

### Performance Metrics

#### Key Metrics Tracked

1. **Response Time**: Average, min, max, percentiles
2. **Success Rate**: Success/failure ratios
3. **Error Patterns**: Common failure types
4. **Provider Rankings**: Performance comparison
5. **Circuit Breaker Status**: Failover system health

#### Metric Collection

```php
// Response time tracking
$startTime = microtime(true);
$response = $this->makeApiCall($provider, $data);
$responseTime = (microtime(true) - $startTime) * 1000;

// Store metrics
$this->monitoringService->recordMetric([
    'service_type' => 'airtime',
    'provider' => $provider,
    'response_time' => $responseTime,
    'success' => $response['success'],
    'timestamp' => now()
]);
```

---

## Failover & Circuit Breaker

### Failover Service

Located at: `app/Services/FailoverService.php`

#### Circuit Breaker Pattern

The system implements the Circuit Breaker pattern to handle provider failures:

```
                    ┌─────────────┐
                    │   CLOSED    │
                    │  (Normal)   │
                    └──────┬──────┘
                           │
                    Failure threshold
                       exceeded
                           │
                           ▼
                    ┌─────────────┐
                    │    OPEN     │
                    │ (Bypassed)  │
                    └──────┬──────┘
                           │
                    Timeout period
                       elapsed
                           │
                           ▼
                    ┌─────────────┐
                    │ HALF_OPEN   │
                    │ (Testing)   │
                    └─────────────┘
```

#### Configuration

```php
// Circuit breaker thresholds
const FAILURE_THRESHOLD = 5;  // Failures to trigger circuit breaker
const TIMEOUT_WINDOW = 300;   // 5 minutes in OPEN state
const SUCCESS_THRESHOLD = 3;  // Successes to close circuit
```

#### Automatic Failover

When a provider fails:

1. **Record Failure**: Log failure details and increment counter
2. **Check Threshold**: If failures exceed threshold, open circuit
3. **Switch Provider**: Route requests to next available provider
4. **Monitor Recovery**: Test failed provider periodically
5. **Close Circuit**: Resume using provider when healthy

#### Manual Failover Controls

```bash
# Reset circuit breaker
php artisan failover:reset-circuit --provider=alphano --service=airtime

# Force provider switch
php artisan failover:switch-provider --service=data --from=alphano --to=vtupro

# Check failover status
php artisan failover:status
```

### Provider Priority System

Providers are ranked by performance and reliability:

```php
class ProviderRanking
{
    public function calculateScore($provider): float
    {
        $metrics = $this->getProviderMetrics($provider);
        
        return (
            $metrics['success_rate'] * 0.4 +
            (1 / $metrics['avg_response_time']) * 0.3 +
            $metrics['uptime_percentage'] * 0.2 +
            (1 / $metrics['error_count']) * 0.1
        );
    }
}
```

---

## Security Features

### Security Monitoring

Located at: `app/Services/SecurityService.php`

#### Threat Detection

1. **Rate Limiting**: API request throttling
2. **IP Whitelisting**: Allowed IP management
3. **Failed Login Monitoring**: Brute force detection
4. **Suspicious Activity**: Pattern recognition
5. **Transaction Anomalies**: Fraud detection

#### Security Logging

All security events are logged with structured data:

```php
$this->securityLogger->logEvent([
    'event_type' => 'failed_login',
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'attempted_username' => $request->username,
    'timestamp' => now(),
    'severity' => 'medium'
]);
```

#### Security Configuration

```php
// Rate limiting
'rate_limits' => [
    'api_requests' => '100:1',     // 100 per minute
    'login_attempts' => '5:5',     // 5 per 5 minutes
    'transaction_requests' => '20:1' // 20 per minute
],

// IP restrictions
'ip_whitelist' => [
    '192.168.1.0/24',
    '10.0.0.0/8'
],

// Security thresholds
'security_thresholds' => [
    'max_failed_logins' => 5,
    'suspicious_activity_threshold' => 10,
    'fraud_detection_threshold' => 5
]
```

### API Security

#### Authentication

All API endpoints use token-based authentication:

```php
// Generate API token
$token = Str::random(64);
$user->api_tokens()->create([
    'name' => 'Mobile App',
    'token' => hash('sha256', $token),
    'abilities' => ['transactions:create', 'balance:read']
]);
```

#### Request Validation

```php
// API request validation
class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:50', 'max:50000'],
            'phone' => ['required', 'regex:/^0[789][01]\d{8}$/'],
            'service_type' => ['required', 'in:airtime,data,cable,electricity']
        ];
    }
}
```

---

## Troubleshooting Guide

### Common Issues

#### 1. Provider API Failures

**Symptoms:**
- High failure rates in monitoring dashboard
- Circuit breaker status showing OPEN
- User complaints about failed transactions

**Diagnosis:**
```bash
# Check provider health
php artisan monitoring:health-check --provider=alphano

# View recent logs
php artisan log:view --service=external_api --provider=alphano --limit=50

# Check circuit breaker status
php artisan failover:status --service=airtime
```

**Resolution:**
```bash
# Reset circuit breaker
php artisan failover:reset-circuit --provider=alphano --service=airtime

# Switch to backup provider
php artisan failover:switch-provider --service=airtime --to=vtupro

# Update provider configuration
php artisan config:update-provider alphano --api_key=new_key
```

#### 2. Slow Response Times

**Symptoms:**
- Dashboard showing high response times
- User timeout complaints
- Monitoring alerts for performance

**Diagnosis:**
```bash
# Check performance metrics
php artisan monitoring:performance-report --hours=24

# View slow query log
php artisan db:slow-queries --limit=20

# Check cache status
php artisan cache:status
```

**Resolution:**
```bash
# Clear and rebuild caches
php artisan cache:clear
php artisan config:cache
php artisan view:cache

# Optimize database
php artisan db:optimize

# Restart queue workers
php artisan queue:restart
```

#### 3. Failed Transactions

**Symptoms:**
- Transactions stuck in "pending" state
- Money deducted but service not delivered
- Reconciliation mismatches

**Diagnosis:**
```bash
# Check pending transactions
php artisan transactions:check-pending --hours=24

# Verify webhook status
php artisan webhooks:verify-status

# Check reconciliation
php artisan reconciliation:run --date=today
```

**Resolution:**
```bash
# Process pending transactions
php artisan transactions:process-pending

# Retry failed transactions
php artisan transactions:retry-failed --limit=100

# Manual reconciliation
php artisan reconciliation:manual --transaction_id=TXN123456
```

### Log Analysis

#### Important Log Locations

```bash
# Application logs
storage/logs/laravel.log

# External API logs  
storage/logs/external_api.log

# Security logs
storage/logs/security.log

# Performance logs
storage/logs/performance.log
```

#### Log Analysis Commands

```bash
# Search for errors
grep "ERROR" storage/logs/laravel.log | tail -50

# Find API failures
grep "API_FAILURE" storage/logs/external_api.log

# Check security events
grep "SECURITY_EVENT" storage/logs/security.log
```

### Database Troubleshooting

#### Common Database Issues

1. **Connection Pool Exhaustion**
   ```bash
   # Check active connections
   php artisan db:connections
   
   # Restart database connections
   php artisan db:reconnect
   ```

2. **Lock Timeouts**
   ```bash
   # Check for locks
   php artisan db:locks
   
   # Kill long-running queries
   php artisan db:kill-long-queries --timeout=300
   ```

3. **Storage Issues**
   ```bash
   # Check disk usage
   df -h
   
   # Clean old logs
   php artisan log:cleanup --days=30
   ```

---

## Administrative Dashboard

### Dashboard Features

The administrative dashboard provides comprehensive system management:

#### Real-time Monitoring

1. **Provider Health Status**: Live status of all API providers
2. **Performance Metrics**: Response times and success rates
3. **Transaction Flow**: Real-time transaction monitoring
4. **Security Alerts**: Security event notifications
5. **System Resources**: Server health monitoring

#### Navigation Structure

```
Admin Dashboard
├── Dashboard Overview
├── System Monitoring
│   ├── Provider Health
│   ├── API Performance
│   ├── Security Metrics
│   └── System Alerts
├── Transaction Management
│   ├── Transaction History
│   ├── Pending Transactions
│   ├── Failed Transactions
│   └── Reconciliation
├── User Management
│   ├── User Accounts
│   ├── User Verification
│   └── Account Suspension
├── Configuration
│   ├── API Settings
│   ├── Provider Management
│   ├── Feature Toggles
│   └── Security Settings
└── Reports & Analytics
    ├── Transaction Reports
    ├── Performance Reports
    ├── Financial Reports
    └── Security Reports
```

#### Dashboard APIs

Real-time data endpoints:

```javascript
// Real-time metrics
fetch('/admin/dashboard/realtime')

// Provider health
fetch('/admin/dashboard/provider-health')  

// API performance
fetch('/admin/dashboard/api-performance')

// Security metrics
fetch('/admin/dashboard/security-metrics')
```

### Administrative Functions

#### User Management

```php
// Verify user account
POST /admin/users/{id}/verify

// Suspend user account  
POST /admin/users/{id}/suspend

// Credit user wallet
POST /admin/users/{id}/credit
```

#### Transaction Management

```php
// Retry failed transaction
POST /admin/transactions/{id}/retry

// Refund transaction
POST /admin/transactions/{id}/refund

// Manual reconciliation
POST /admin/transactions/{id}/reconcile
```

#### System Configuration

```php
// Update API configuration
PUT /admin/configuration/api/{service}/{provider}

// Toggle feature
POST /admin/configuration/feature-toggle/{feature}

// Update security settings
PUT /admin/configuration/security
```

---

## Database Schema

### Core Tables

#### users
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sId VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20) UNIQUE,
    wallet_balance DECIMAL(10,2) DEFAULT 0.00,
    reg_status ENUM('active', 'suspended', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### transactions
```sql
CREATE TABLE transactions (
    tId BIGINT PRIMARY KEY AUTO_INCREMENT,
    transref VARCHAR(255) UNIQUE,
    sId VARCHAR(255),
    servicename VARCHAR(100),
    servicedesc TEXT,
    amount DECIMAL(10,2),
    phone VARCHAR(20),
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded'),
    profit DECIMAL(10,2) DEFAULT 0.00,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    api_response TEXT,
    FOREIGN KEY (sId) REFERENCES users(sId)
);
```

#### api_configurations
```sql
CREATE TABLE api_configurations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    service_type VARCHAR(50),
    provider VARCHAR(100),
    config_data JSON,
    is_active BOOLEAN DEFAULT TRUE,
    priority INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### monitoring_metrics
```sql
CREATE TABLE monitoring_metrics (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    service_type VARCHAR(50),
    provider VARCHAR(100),
    metric_type VARCHAR(50),
    value DECIMAL(10,4),
    metadata JSON,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### security_logs
```sql
CREATE TABLE security_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    event_type VARCHAR(100),
    user_id VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    event_data JSON,
    severity ENUM('low', 'medium', 'high', 'critical'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Indexes for Performance

```sql
-- Transaction queries
CREATE INDEX idx_transactions_status ON transactions(status);
CREATE INDEX idx_transactions_date ON transactions(date);
CREATE INDEX idx_transactions_user ON transactions(sId);

-- Monitoring queries  
CREATE INDEX idx_monitoring_service_provider ON monitoring_metrics(service_type, provider);
CREATE INDEX idx_monitoring_recorded_at ON monitoring_metrics(recorded_at);

-- Security queries
CREATE INDEX idx_security_event_type ON security_logs(event_type);
CREATE INDEX idx_security_created_at ON security_logs(created_at);
```

---

## Deployment Guide

### Server Requirements

#### Minimum Requirements
- PHP 8.1+
- MySQL 8.0+
- Redis 6.0+
- Nginx 1.18+
- Composer 2.0+

#### Recommended Specifications
- CPU: 4 cores
- RAM: 8GB
- Storage: 100GB SSD
- Network: 1Gbps

### Installation Steps

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd vtu-system
   ```

2. **Install Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Cache Optimization**
   ```bash
   php artisan config:cache
   php artisan view:cache
   php artisan route:cache
   ```

### Production Configuration

#### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vtu_system
DB_USERNAME=vtu_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/vtu-system/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### Process Management
```bash
# Supervisor configuration for queues
[program:vtu-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/vtu-system/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
```

### Monitoring & Maintenance

#### Scheduled Tasks
```bash
# Add to crontab
* * * * * cd /var/www/vtu-system && php artisan schedule:run >> /dev/null 2>&1
```

#### Log Rotation
```bash
# Logrotate configuration
/var/www/vtu-system/storage/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    missingok
    notifempty
    create 644 www-data www-data
}
```

#### Backup Strategy
```bash
#!/bin/bash
# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > backup_$(date +%Y%m%d).sql

# File backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz /var/www/vtu-system

# Upload to S3 or remote storage
aws s3 cp backup_$(date +%Y%m%d).sql s3://your-backup-bucket/
```

---

## Support and Maintenance

### Monitoring Commands

```bash
# System health check
php artisan system:health-check

# Performance report
php artisan monitoring:performance-report

# Security audit
php artisan security:audit

# Database maintenance
php artisan db:maintenance
```

### Emergency Procedures

1. **Service Outage Response**
   - Check provider status
   - Switch to backup providers
   - Notify users via dashboard

2. **Security Incident Response**
   - Review security logs
   - Block suspicious IPs
   - Reset API keys if compromised

3. **Data Backup Recovery**
   - Restore from latest backup
   - Verify data integrity
   - Resume normal operations

For additional support, please contact the development team or refer to the inline code documentation.
