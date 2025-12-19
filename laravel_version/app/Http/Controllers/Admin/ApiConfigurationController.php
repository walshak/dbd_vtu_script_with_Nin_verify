<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiConfig;
use App\Models\ApiLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiConfigurationController extends Controller
{
    public function index()
    {
        $configs = ApiConfig::all()->keyBy('key');
        $apiLinks = ApiLink::active()->get()->groupBy('type');

        return view('admin.api-configuration.index', compact('configs', 'apiLinks'));
    }

    public function airtime(Request $request)
    {
        $network = $request->get('network', 'MTN');
        $configs = ApiConfig::all()->keyBy('key');
        $apiLinks = ApiLink::byType('Airtime')->active()->get();

        return view('admin.api-configuration.airtime', compact('configs', 'apiLinks', 'network'));
    }

    public function data(Request $request)
    {
        $network = $request->get('network', 'MTN');
        $configs = ApiConfig::all()->keyBy('key');
        $apiLinks = ApiLink::byType('Data')->active()->get();

        return view('admin.api-configuration.data', compact('configs', 'apiLinks', 'network'));
    }

    public function wallet()
    {
        $configs = ApiConfig::all()->keyBy('key');
        $apiLinks = ApiLink::byType('Wallet')->active()->get();

        return view('admin.api-configuration.wallet', compact('configs', 'apiLinks'));
    }

    public function uzobest()
    {
        $apiUrl = config('services.uzobest.url', 'https://uzobestgsm.com/api');
        $apiKey = config('services.uzobest.key', '');

        $uzobestLinks = ApiLink::where('name', 'Uzobest')->get();

        return view('admin.api-configuration.uzobest', compact('apiUrl', 'apiKey', 'uzobestLinks'));
    }

    public function updateUzobest(Request $request)
    {
        $request->validate([
            'uzobest_api_key' => 'required|string|min:10',
        ]);

        try {
            $apiKey = $request->uzobest_api_key;

            // Update the .env file
            $envPath = base_path('.env');

            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);

                // Check if UZOBEST_API_KEY exists
                if (strpos($envContent, 'UZOBEST_API_KEY=') !== false) {
                    // Update existing key
                    $envContent = preg_replace(
                        '/UZOBEST_API_KEY=.*/',
                        'UZOBEST_API_KEY=' . $apiKey,
                        $envContent
                    );
                } else {
                    // Add new key
                    $envContent .= "\nUZOBEST_API_KEY=" . $apiKey;
                }

                file_put_contents($envPath, $envContent);

                // Clear config cache
                \Artisan::call('config:clear');
            }

            // Update the database (ApiLink table)
            // Find or create Uzobest API link entries
            $apiUrl = config('services.uzobest.url', 'https://uzobestgsm.com/api');

            // Update or create the main Uzobest API link
            ApiLink::updateOrCreate(
                [
                    'name' => 'Uzobest',
                    'type' => 'primary'
                ],
                [
                    'value' => $apiUrl,
                    'is_active' => true,
                    'auth_type' => 'header',
                    'auth_params' => [
                        'token' => $apiKey,
                        'header_name' => 'Authorization',
                        'header_prefix' => 'Token '
                    ],
                    'priority' => 1
                ]
            );

            // Also update service-specific endpoints
            $services = [
                'Data' => '/data/',
                'Airtime' => '/topup/',
                'Cable' => '/cabletv/',
                'Electricity' => '/billpayment/'
            ];

            foreach ($services as $type => $endpoint) {
                ApiLink::updateOrCreate(
                    [
                        'name' => 'Uzobest',
                        'type' => $type
                    ],
                    [
                        'value' => $apiUrl . $endpoint,
                        'is_active' => true,
                        'auth_type' => 'header',
                        'auth_params' => [
                            'token' => $apiKey,
                            'header_name' => 'Authorization',
                            'header_prefix' => 'Token '
                        ],
                        'priority' => 1
                    ]
                );
            }

            \Log::info('Uzobest API credentials updated', [
                'updated_by' => \Auth::guard('admin')->user()->sysUsername ?? 'system',
                'updated_locations' => ['env_file', 'apilinks_table']
            ]);

            return redirect()->back()
                ->with('success', 'Uzobest API credentials updated successfully in both .env and database. Changes will take effect immediately.');

        } catch (\Exception $e) {
            \Log::error('Failed to update Uzobest credentials: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update credentials: ' . $e->getMessage());
        }
    }

    public function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cableVerificationApi' => 'nullable|string',
            'cableVerificationProvider' => 'required|string',
            'cableApi' => 'nullable|string',
            'cableProvider' => 'required|string',
            'meterVerificationApi' => 'nullable|string',
            'meterVerificationProvider' => 'required|string',
            'meterApi' => 'nullable|string',
            'meterProvider' => 'required|string',
            'examApi' => 'nullable|string',
            'examProvider' => 'required|string',
            'rechargePinApi' => 'nullable|string',
            'rechargePinProvider' => 'nullable|string',
            'dataPinApi' => 'nullable|string',
            'dataPinProvider' => 'nullable|string',
            'alphaApi' => 'nullable|string',
            'alphaProvider' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $configs = $request->except(['_token', 'update-api-config']);

        foreach ($configs as $key => $value) {
            ApiConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()
            ->with('success', 'API configuration updated successfully');
    }

    public function updateAirtime(Request $request)
    {
        $network = strtolower($request->get('network', 'mtn'));

        $validator = Validator::make($request->all(), [
            $network . 'VtuKey' => 'required|string',
            $network . 'VtuProvider' => 'required|string',
            $network . 'SharesellKey' => 'required|string',
            $network . 'SharesellProvider' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $configs = $request->except(['_token', 'update-api-config', 'network']);

        foreach ($configs as $key => $value) {
            ApiConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()
            ->with('success', ucfirst($network) . ' airtime configuration updated successfully');
    }

    public function updateData(Request $request)
    {
        $network = strtolower($request->get('network', 'mtn'));

        $validator = Validator::make($request->all(), [
            $network . 'SmeApi' => 'required|string',
            $network . 'SmeProvider' => 'required|string',
            $network . 'GiftingApi' => 'required|string',
            $network . 'GiftingProvider' => 'required|string',
            $network . 'CorporateApi' => 'required|string',
            $network . 'CorporateProvider' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $configs = $request->except(['_token', 'update-api-config', 'network']);

        foreach ($configs as $key => $value) {
            ApiConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()
            ->with('success', ucfirst($network) . ' data configuration updated successfully');
    }

    public function providers()
    {
        $providers = ApiLink::all()->groupBy('type');
        return view('admin.api-configuration.providers', compact('providers'));
    }

    public function createProvider()
    {
        return view('admin.api-configuration.create-provider');
    }

    public function storeProvider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'value' => 'required|url|max:100',
            'type' => 'required|string|in:Wallet,Airtime,Data,Cable,CableVer,Electricity,ElectricityVer,Exam,Data Pin',
            'priority' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ApiLink::create($request->all());

        return redirect()->route('admin.api-configuration.providers')
            ->with('success', 'API provider created successfully');
    }

    public function editProvider(ApiLink $provider)
    {
        return view('admin.api-configuration.edit-provider', compact('provider'));
    }

    public function updateProvider(Request $request, ApiLink $provider)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'value' => 'required|url|max:100',
            'type' => 'required|string|in:Wallet,Airtime,Data,Cable,CableVer,Electricity,ElectricityVer,Exam,Data Pin',
            'priority' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $provider->update($request->all());

        return redirect()->route('admin.api-configuration.providers')
            ->with('success', 'API provider updated successfully');
    }

    public function toggleProvider(ApiLink $provider)
    {
        $provider->update(['is_active' => !$provider->is_active]);

        $status = $provider->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Provider {$status} successfully");
    }

    public function destroyProvider(ApiLink $provider)
    {
        $provider->delete();

        return redirect()->route('admin.api-configuration.providers')
            ->with('success', 'API provider deleted successfully');
    }

    public function testProvider(ApiLink $provider)
    {
        // Test API provider connectivity
        $start = microtime(true);

        try {
            $response = \Http::timeout(10)->get($provider->value);
            $responseTime = (microtime(true) - $start) * 1000; // Convert to milliseconds

            $success = $response->successful();
            $provider->updatePerformance($success, $responseTime);

            return response()->json([
                'success' => $success,
                'response_time' => round($responseTime, 2),
                'status_code' => $response->status(),
                'message' => $success ? 'Provider is responding' : 'Provider returned error'
            ]);
        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $start) * 1000;
            $provider->updatePerformance(false, $responseTime);

            return response()->json([
                'success' => false,
                'response_time' => round($responseTime, 2),
                'message' => 'Connection failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Uzobest API connection
     */
    public function testUzobestConnection()
    {
        $start = microtime(true);

        try {
            $apiUrl = config('services.uzobest.url', 'https://uzobestgsm.com/api');
            $apiKey = config('services.uzobest.key');

            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uzobest API key not configured'
                ]);
            }

            // Test the user endpoint to verify authentication
            $response = \Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Token ' . $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->get($apiUrl . '/user/');

            $responseTime = round((microtime(true) - $start) * 1000, 2);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'response_time' => $responseTime,
                    'message' => 'Connection successful',
                    'balance' => $data['Account_Balance'] ?? 'N/A',
                    'username' => $data['username'] ?? 'N/A'
                ]);
            }

            return response()->json([
                'success' => false,
                'response_time' => $responseTime,
                'message' => 'Connection failed: ' . $response->body()
            ]);

        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $start) * 1000, 2);
            return response()->json([
                'success' => false,
                'response_time' => $responseTime,
                'message' => 'Connection error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fetch data plans from Uzobest
     */
    public function fetchUzobestPlans()
    {
        \Log::emergency('FETCHUZOBESTPLANS METHOD CALLED - THIS SHOULD APPEAR IN LOGS');

        try {
            $apiUrl = config('services.uzobest.url', 'https://uzobestgsm.com/api');
            $apiKey = config('services.uzobest.key');

            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uzobest API key not configured'
                ]);
            }

            // Fetch data plans from network endpoint
            $response = \Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Token ' . $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->get($apiUrl . '/network/');

            if ($response->successful()) {
                $data = $response->json();
                $totalProcessed = 0;
                $updatedPlans = 0;
                $newPlans = 0;

                // Network ID mapping (Uzobest network ID -> Our network ID)
                $networkMap = [
                    1 => 1,  // MTN
                    2 => 2,  // GLO
                    3 => 3,  // 9MOBILE
                    4 => 4,  // AIRTEL
                ];

                // Helper function to normalize data amount strings
                $normalizeAmount = function($amount) {
                    // Convert "1.0GB" to "1GB", "500.0MB" to "500MB"
                    $amount = str_replace('.0', '', $amount);
                    return strtoupper($amount);
                };

                // Process each network's plans
                $debugCounter = 0;
                \Log::info('Starting plan sync', ['networks_found' => array_keys($data)]);

                foreach (['MTN_PLAN', 'GLO_PLAN', 'AIRTEL_PLAN', '9MOBILE_PLAN'] as $networkKey) {
                    if (!isset($data[$networkKey])) {
                        \Log::info('Network key not found', ['key' => $networkKey]);
                        continue;
                    }

                    \Log::info('Processing network', ['key' => $networkKey, 'plan_count' => count($data[$networkKey])]);

                    foreach ($data[$networkKey] as $plan) {
                        $totalProcessed++;

                        $networkId = $networkMap[$plan['network']] ?? null;
                        if (!$networkId) continue;

                        $planType = strtoupper($plan['plan_type']);
                        $dataAmount = $normalizeAmount($plan['plan']);
                        $uzobestPlanId = $plan['id'];
                        $uzobestPrice = floatval($plan['plan_amount']);

                        // Debug logging for first 5 plans
                        if ($debugCounter < 5) {
                            \Log::info('Plan matching attempt', [
                                'uzobest_id' => $uzobestPlanId,
                                'search_criteria' => [
                                    'nId' => $networkId,
                                    'dGroup' => $planType,
                                    'dAmount' => $dataAmount
                                ],
                                'uzobest_price' => $uzobestPrice
                            ]);
                            $debugCounter++;
                        }

                        // Try to find matching plan in our database
                        // Match by network, plan type, and data amount
                        $existingPlan = \App\Models\DataPlan::where('nId', $networkId)
                            ->where('dGroup', $planType)
                            ->where('dAmount', $dataAmount)
                            ->first();

                        if ($existingPlan) {
                            // Update existing plan with Uzobest ID and price
                            $existingPlan->uzobest_plan_id = $uzobestPlanId;
                            $existingPlan->cost_price = $uzobestPrice; // Update cost price from Uzobest
                            $existingPlan->save();
                            $updatedPlans++;

                            \Log::info('Successfully updated plan', [
                                'dPlanId' => $existingPlan->dPlanId,
                                'dGroup' => $existingPlan->dGroup,
                                'dAmount' => $existingPlan->dAmount,
                                'uzobest_plan_id' => $uzobestPlanId
                            ]);
                        } else {
                            // Log first 3 failed matches for debugging
                            if ($debugCounter <= 7) {
                                \Log::info('No matching plan found', [
                                    'searched_for' => [
                                        'nId' => $networkId,
                                        'dGroup' => $planType,
                                        'dAmount' => $dataAmount
                                    ]
                                ]);
                            }
                        }
                        // Note: We don't create new plans automatically to avoid cluttering the database
                    }
                }

                \Log::info('Data plans sync completed', [
                    'total_plans_processed' => $totalProcessed,
                    'updated_plans' => $updatedPlans,
                    'new_plans' => $newPlans
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Updated {$updatedPlans} plans from {$totalProcessed} Uzobest plans",
                    'total_processed' => $totalProcessed,
                    'updated' => $updatedPlans,
                    'networks' => array_keys($data)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plans: ' . $response->body()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching plans: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper method to get config value
     */
    private function getConfigValue($key, $default = '')
    {
        $config = ApiConfig::where('key', $key)->first();
        return $config ? $config->value : $default;
    }
}
