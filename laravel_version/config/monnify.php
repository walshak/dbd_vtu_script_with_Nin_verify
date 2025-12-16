<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Monnify API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Monnify payment gateway integration
    |
    */

    'transfer_fee' => env('MONNIFY_TRANSFER_FEE', 10),
    'max_fee' => env('MONNIFY_MAX_FEE', 50),
    
    // Monnify webhook IP addresses for validation
    'webhook_ips' => [
        '162.246.254.36',
        '162.246.254.37',
        '162.246.254.38',
        '162.246.254.39',
        // Add more as provided by Monnify
    ],
    
    // Access token cache duration (55 minutes - tokens valid for 1 hour)
    'token_cache_duration' => env('MONNIFY_TOKEN_CACHE_DURATION', 3300),
    
    // Webhook retry configuration
    'webhook_retry_attempts' => 3,
    'webhook_retry_delay' => 60, // seconds
];
