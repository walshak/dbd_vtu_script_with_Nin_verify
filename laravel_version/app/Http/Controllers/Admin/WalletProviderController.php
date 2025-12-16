<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WalletProviderController extends Controller
{
    /**
     * Show wallet provider settings page
     */
    public function index()
    {
        $configurations = Configuration::all()->keyBy('config_key');
        $apiProviders = $this->getApiProviders();

        return view('admin.wallet-providers.index', compact('configurations', 'apiProviders'));
    }

    /**
     * Show Monnify settings page
     */
    public function showMonnifySettings()
    {
        $configurations = Configuration::all()->keyBy('config_key');

        return view('admin.wallet-providers.monnify', compact('configurations'));
    }

    /**
     * Show Paystack settings page
     */
    public function showPaystackSettings()
    {
        $configurations = Configuration::all()->keyBy('config_key');

        return view('admin.wallet-providers.paystack', compact('configurations'));
    }

    /**
     * Show wallet API settings page
     */
    public function showWalletApiSettings()
    {
        $configurations = Configuration::all()->keyBy('config_key');
        $apiProviders = $this->getApiProviders();

        return view('admin.wallet-providers.wallet-api', compact('configurations', 'apiProviders'));
    }

    /**
     * Update Monnify configuration
     */
    public function updateMonnifyConfig(Request $request)
    {
        Log::info('updateMonnifyConfig method called', [
            'request_method' => $request->method(),
            'has_monifyApi' => $request->has('monifyApi'),
            'has_monifyStatus' => $request->has('monifyStatus')
        ]);

        // Test database connectivity
        try {
            $testCount = Configuration::count();
            Log::info('Database connection test passed', ['config_count' => $testCount]);
        } catch (\Exception $e) {
            Log::error('Database connection failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Database connection failed: ' . $e->getMessage());
        }

        try {
            $request->validate([
                'monifyApi' => 'required|string',
                'monifySecrete' => 'required|string',
                'monifyContract' => 'required|string',
                'monifyEnvironment' => 'required|in:sandbox,live',
                'monifyCharges' => 'required|numeric|min:0|max:100',
            ]);
            Log::info('Validation passed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        try {
            // Handle checkbox - if not checked, it won't be in the request
            $monifyStatus = $request->has('monifyStatus') ? 'On' : 'Off';

            $configs = [
                'monifyApi' => $request->monifyApi,
                'monifySecrete' => $request->monifySecrete,
                'monifyContract' => $request->monifyContract,
                'monifyEnvironment' => $request->monifyEnvironment,
                'monifyCharges' => $request->monifyCharges,
                'monifyStatus' => $monifyStatus,
            ];

            Log::info('Attempting to save Monnify configuration', [
                'request_data' => $request->except(['monifyApi', 'monifySecrete']), // Don't log sensitive data
                'configs_keys' => array_keys($configs)
            ]);

            // Save configurations one by one with individual error handling
            $savedConfigs = 0;
            foreach ($configs as $key => $value) {
                try {
                    $result = Configuration::updateOrCreate(
                        ['config_key' => $key],
                        ['config_value' => $value]
                    );

                    if ($result) {
                        $savedConfigs++;
                        Log::info("Configuration saved successfully: $key");
                    } else {
                        Log::warning("Configuration save returned false: $key");
                    }
                } catch (\Exception $configError) {
                    Log::error("Error saving individual config: $key", [
                        'error' => $configError->getMessage(),
                        'value' => $value
                    ]);
                    throw $configError;
                }
            }

            Log::info('All Monnify configurations processed', [
                'admin_id' => Auth::check() ? Auth::user()->id : 'unknown',
                'total_configs' => count($configs),
                'saved_configs' => $savedConfigs
            ]);

            if ($savedConfigs === count($configs)) {
                return back()->with('success', 'Monnify configuration updated successfully.');
            } else {
                return back()->with('error', "Only $savedConfigs of " . count($configs) . " configurations were saved.");
            }

        } catch (\Exception $e) {
            Log::error('Monnify configuration update error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to update Monnify configuration: ' . $e->getMessage());
        }
    }

    /**
     * Update Paystack configuration
     */
    public function updatePaystackConfig(Request $request)
    {
        $request->validate([
            'paystackApi' => 'required|string',
            'paystackCharges' => 'required|numeric|min:0|max:100',
        ]);

        try {
            // Handle checkbox - if not checked, it won't be in the request
            $paystackStatus = $request->has('paystackStatus') ? 'On' : 'Off';

            $configs = [
                'paystackApi' => $request->paystackApi,
                'paystackCharges' => $request->paystackCharges,
                'paystackStatus' => $paystackStatus,
            ];

            foreach ($configs as $key => $value) {
                Configuration::updateOrCreate(
                    ['config_key' => $key],
                    ['config_value' => $value]
                );
            }

            Log::info('Paystack configuration updated', [
                'admin_id' => Auth::user()->id,
                'changes' => $configs
            ]);

            return back()->with('success', 'Paystack configuration updated successfully.');

        } catch (\Exception $e) {
            Log::error('Paystack configuration update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update Paystack configuration.');
        }
    }

    /**
     * Update wallet API configuration
     */
    public function updateWalletApiConfig(Request $request)
    {
        $request->validate([
            'walletOneProviderName' => 'required|string',
            'walletOneApi' => 'required|string',
            'walletOneProvider' => 'required|string',
            'walletTwoProviderName' => 'required|string',
            'walletTwoApi' => 'required|string',
            'walletTwoProvider' => 'required|string',
            'walletThreeProviderName' => 'required|string',
            'walletThreeApi' => 'required|string',
            'walletThreeProvider' => 'required|string',
        ]);

        try {
            $configs = [
                'walletOneProviderName' => $request->walletOneProviderName,
                'walletOneApi' => $request->walletOneApi,
                'walletOneProvider' => $request->walletOneProvider,
                'walletTwoProviderName' => $request->walletTwoProviderName,
                'walletTwoApi' => $request->walletTwoApi,
                'walletTwoProvider' => $request->walletTwoProvider,
                'walletThreeProviderName' => $request->walletThreeProviderName,
                'walletThreeApi' => $request->walletThreeApi,
                'walletThreeProvider' => $request->walletThreeProvider,
            ];

            foreach ($configs as $key => $value) {
                Configuration::updateOrCreate(
                    ['config_key' => $key],
                    ['config_value' => $value]
                );
            }

            Log::info('Wallet API configuration updated', [
                'admin_id' => Auth::user()->id,
                'changes' => $configs
            ]);

            return back()->with('success', 'Wallet API configuration updated successfully.');

        } catch (\Exception $e) {
            Log::error('Wallet API configuration update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update Wallet API configuration.');
        }
    }

    /**
     * Get wallet balances from all providers
     */
    public function getWalletBalances()
    {
        try {
            $balances = [];

            // Get wallet one balance
            $walletOneData = $this->getWalletBalance('walletOne');
            $balances['walletOne'] = [
                'provider' => $this->getConfigValue('walletOneProviderName', 'Wallet One'),
                'balance' => $walletOneData['balance'] ?? 0,
                'status' => $walletOneData['status'] ?? 'error'
            ];

            // Get wallet two balance
            $walletTwoData = $this->getWalletBalance('walletTwo');
            $balances['walletTwo'] = [
                'provider' => $this->getConfigValue('walletTwoProviderName', 'Wallet Two'),
                'balance' => $walletTwoData['balance'] ?? 0,
                'status' => $walletTwoData['status'] ?? 'error'
            ];

            // Get wallet three balance
            $walletThreeData = $this->getWalletBalance('walletThree');
            $balances['walletThree'] = [
                'provider' => $this->getConfigValue('walletThreeProviderName', 'Wallet Three'),
                'balance' => $walletThreeData['balance'] ?? 0,
                'status' => $walletThreeData['status'] ?? 'error'
            ];

            return response()->json([
                'success' => true,
                'balances' => $balances
            ]);

        } catch (\Exception $e) {
            Log::error('Get wallet balances error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve wallet balances'
            ]);
        }
    }

    /**
     * Get wallet balance from specific provider
     */
    private function getWalletBalance($walletKey)
    {
        try {
            $providerUrl = $this->getConfigValue($walletKey . 'Provider');
            $apiKey = $this->getConfigValue($walletKey . 'Api');

            if (!$providerUrl || !$apiKey) {
                return ['balance' => 0, 'status' => 'not_configured'];
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Token ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->get($providerUrl . 'balance/');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'balance' => $data['balance'] ?? 0,
                    'status' => 'success'
                ];
            } else {
                Log::warning("Failed to get $walletKey balance", [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['balance' => 0, 'status' => 'error'];
            }

        } catch (\Exception $e) {
            Log::error("Error getting $walletKey balance: " . $e->getMessage());
            return ['balance' => 0, 'status' => 'error'];
        }
    }

    /**
     * Test provider connection
     */
    public function testProviderConnection(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:monnify,paystack,walletOne,walletTwo,walletThree'
        ]);

        try {
            $provider = $request->provider;
            $result = false;

            switch ($provider) {
                case 'monnify':
                    $result = $this->testMonnifyConnection();
                    break;
                case 'paystack':
                    $result = $this->testPaystackConnection();
                    break;
                case 'walletOne':
                case 'walletTwo':
                case 'walletThree':
                    $result = $this->testWalletProviderConnection($provider);
                    break;
            }

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Connection successful' : 'Connection failed'
            ]);

        } catch (\Exception $e) {
            Log::error('Test provider connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed'
            ]);
        }
    }

    /**
     * Test Monnify connection
     */
    private function testMonnifyConnection()
    {
        try {
            $apiKey = $this->getConfigValue('monifyApi');
            $secretKey = $this->getConfigValue('monifySecrete');

            if (!$apiKey || !$secretKey) {
                return false;
            }

            // Create basic auth token
            $token = base64_encode($apiKey . ':' . $secretKey);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Basic ' . $token,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://sandbox.monnify.com/api/v1/auth/login');

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Monnify connection test error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test Paystack connection
     */
    private function testPaystackConnection()
    {
        try {
            $apiKey = $this->getConfigValue('paystackApi');

            if (!$apiKey) {
                return false;
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->get('https://api.paystack.co/bank');

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Paystack connection test error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test wallet provider connection
     */
    private function testWalletProviderConnection($walletKey)
    {
        try {
            $providerUrl = $this->getConfigValue($walletKey . 'Provider');
            $apiKey = $this->getConfigValue($walletKey . 'Api');

            if (!$providerUrl || !$apiKey) {
                return false;
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Token ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->get($providerUrl . 'balance/');

            return $response->successful();

        } catch (\Exception $e) {
            Log::error("$walletKey connection test error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Switch active payment provider
     */
    public function switchPaymentProvider(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:monnify,paystack',
            'status' => 'required|in:On,Off'
        ]);

        try {
            $provider = $request->provider;
            $status = $request->status;

            // If turning on this provider, turn off the other
            if ($status === 'On') {
                $otherProvider = $provider === 'monnify' ? 'paystack' : 'monnify';
                Configuration::updateOrCreate(
                    ['config_key' => $otherProvider . 'Status'],
                    ['config_value' => 'Off']
                );
            }

            Configuration::updateOrCreate(
                ['config_key' => $provider . 'Status'],
                ['config_value' => $status]
            );

            Log::info('Payment provider switched', [
                'admin_id' => Auth::user()->id,
                'provider' => $provider,
                'status' => $status
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($provider) . ' provider ' . strtolower($status)
            ]);

        } catch (\Exception $e) {
            Log::error('Switch payment provider error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch payment provider'
            ]);
        }
    }

    /**
     * Get payment transactions
     */
    public function getPaymentTransactions(Request $request)
    {
        try {
            $query = Transaction::where('trans_type', 'LIKE', '%Wallet%');

            // Filter by provider if specified
            if ($request->provider) {
                $query->where('trans_id', 'LIKE', '%' . strtoupper($request->provider) . '%');
            }

            // Filter by date range if specified
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('trans_date', [$request->start_date, $request->end_date]);
            }

            $transactions = $query->orderBy('trans_date', 'desc')
                                ->paginate(50);

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            Log::error('Get payment transactions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transactions'
            ]);
        }
    }

    /**
     * Get API providers for wallet selection
     */
    private function getApiProviders()
    {
        // This would typically come from a database table
        // For now, return mock data based on the PHP system
        return collect([
            (object) ['name' => 'Maskawasub', 'value' => 'https://maskawasub.com/api/user/', 'type' => 'Wallet'],
            (object) ['name' => 'Topupmate', 'value' => 'https://topupmate.com/api/user/', 'type' => 'Wallet'],
            (object) ['name' => 'Aabaxztech', 'value' => 'https://aabaxztech.com/api/user/', 'type' => 'Wallet'],
            (object) ['name' => 'Custom Provider 1', 'value' => 'https://custom1.com/api/', 'type' => 'Wallet'],
            (object) ['name' => 'Custom Provider 2', 'value' => 'https://custom2.com/api/', 'type' => 'Wallet'],
        ]);
    }

    /**
     * Get configuration value
     */
    private function getConfigValue($key, $default = '')
    {
        $config = Configuration::where('config_key', $key)->first();
        return $config ? $config->config_value : $default;
    }

    /**
     * Generate webhook URL for providers
     */
    public function generateWebhookUrl(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:monnify,paystack'
        ]);

        $provider = $request->provider;
        $webhookUrl = url("/webhook/$provider");

        return response()->json([
            'success' => true,
            'webhook_url' => $webhookUrl,
            'instructions' => $this->getWebhookInstructions($provider)
        ]);
    }

    /**
     * Get webhook setup instructions
     */
    private function getWebhookInstructions($provider)
    {
        $instructions = [
            'monnify' => [
                'title' => 'Monnify Webhook Setup',
                'steps' => [
                    'Login to your Monnify dashboard',
                    'Navigate to Settings > Webhooks',
                    'Add the webhook URL provided above',
                    'Select events: SUCCESSFUL_TRANSACTION, FAILED_TRANSACTION',
                    'Save the webhook configuration'
                ]
            ],
            'paystack' => [
                'title' => 'Paystack Webhook Setup',
                'steps' => [
                    'Login to your Paystack dashboard',
                    'Navigate to Settings > Webhooks',
                    'Add the webhook URL provided above',
                    'Select events: charge.success, charge.failed',
                    'Save the webhook configuration'
                ]
            ]
        ];

        return $instructions[$provider] ?? [];
    }
}
