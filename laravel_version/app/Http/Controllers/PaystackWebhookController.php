<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Handle Paystack webhook notifications
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('X-Paystack-Signature');

            Log::info('Paystack webhook received', [
                'headers' => $request->headers->all(),
                'payload' => $payload
            ]);

            if (!$signature) {
                Log::warning('Paystack webhook missing signature');
                return response()->json(['error' => 'Missing signature'], 400);
            }

            // Process the webhook
            $result = $this->paystackService->processWebhook($payload, $signature);

            if ($result['success']) {
                Log::info('Paystack webhook processed successfully', $result);
                return response()->json(['status' => 'success'], 200);
            }

            Log::warning('Paystack webhook processing failed', $result);
            return response()->json(['error' => $result['message']], 400);

        } catch (\Exception $e) {
            Log::error('Paystack webhook error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
