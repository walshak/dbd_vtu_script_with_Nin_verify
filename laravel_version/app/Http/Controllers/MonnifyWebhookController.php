<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MonnifyService;
use Illuminate\Support\Facades\Log;

class MonnifyWebhookController extends Controller
{
    protected $monnifyService;

    /**
     * Monnify webhook IP addresses (from latest documentation)
     */
    private const MONNIFY_IPS = [
        '162.246.254.36',
        '162.246.254.37',
        '162.246.254.38',
        '162.246.254.39',
    ];

    public function __construct(MonnifyService $monnifyService)
    {
        $this->monnifyService = $monnifyService;
    }

    /**
     * Validate webhook IP address
     */
    private function isValidMonnifyIP(string $ip): bool
    {
        $allowedIPs = config('monnify.webhook_ips', self::MONNIFY_IPS);
        return in_array($ip, $allowedIPs);
    }

    /**
     * Handle Monnify webhook notifications
     * Following latest Monnify best practices (2024):
     * 1. Validate IP address
     * 2. Verify signature
     * 3. Return 200 immediately
     * 4. Process asynchronously
     */
    public function handleWebhook(Request $request)
    {
        try {
            $clientIP = $request->ip();
            $payload = $request->getContent();
            $signature = $request->header('Monnify-Signature');

            Log::info('Monnify webhook received', [
                'ip' => $clientIP,
                'has_signature' => !empty($signature)
            ]);

            // Step 1: Validate IP address (Critical security check)
            if (!$this->isValidMonnifyIP($clientIP)) {
                Log::warning('Monnify webhook from unauthorized IP', [
                    'ip' => $clientIP,
                    'allowed_ips' => config('monnify.webhook_ips', self::MONNIFY_IPS)
                ]);
                return response()->json(['error' => 'Unauthorized IP'], 403);
            }

            // Step 2: Validate signature
            if (!$signature) {
                Log::warning('Monnify webhook missing signature', ['ip' => $clientIP]);
                return response()->json(['error' => 'Missing signature'], 400);
            }

            if (!$this->monnifyService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Monnify webhook invalid signature', ['ip' => $clientIP]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Step 3: Return 200 immediately to acknowledge receipt
            // This prevents Monnify from retrying due to timeout
            
            // Step 4: Process webhook synchronously for now
            // TODO: Implement async processing with queue jobs
            $result = $this->monnifyService->processWebhook($payload);

            if ($result['success']) {
                Log::info('Monnify webhook processed successfully', [
                    'ip' => $clientIP,
                    'result' => $result
                ]);
            } else {
                Log::warning('Monnify webhook processing failed', [
                    'ip' => $clientIP,
                    'result' => $result
                ]);
            }

            // Always return 200 to prevent retries
            return response()->json(['status' => 'received'], 200);

        } catch (\Exception $e) {
            Log::error('Monnify webhook error: ' . $e->getMessage(), [
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString()
            ]);

            // Still return 200 to prevent retries for system errors
            return response()->json(['status' => 'error_logged'], 200);
        }
    }
}
