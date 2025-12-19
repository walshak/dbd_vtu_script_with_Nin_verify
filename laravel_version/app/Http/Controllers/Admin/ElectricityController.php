<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElectricityProvider;
use App\Models\Transaction;
use App\Models\SiteSettings;
use App\Models\ServiceSyncStatus;
use App\Services\ElectricityService;
use App\Services\UzobestSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ElectricityController extends Controller
{
    protected $electricityService;
    protected $syncService;

    public function __construct(ElectricityService $electricityService, UzobestSyncService $syncService)
    {
        $this->electricityService = $electricityService;
        $this->syncService = $syncService;
    }

    /**
     * Display electricity management dashboard
     * Auto-syncs providers if needed
     */
    public function index()
    {
        try {
            // Get sync status
            $syncStatus = ServiceSyncStatus::getStatus('electricity');

            // Auto-sync if needed
            if (!$syncStatus || $syncStatus->needsSync()) {
                try {
                    $this->performAutoSync();
                    $syncStatus = ServiceSyncStatus::getStatus('electricity');
                } catch (\Exception $e) {
                    Log::error('Auto-sync failed on electricity page load', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $providers = ElectricityProvider::orderBy('ePlan')->get();

            // Get statistics
            $statistics = $this->getElectricityStatistics();

            // Get settings
            $settings = $this->getElectricitySettings();

            return view('admin.electricity.index', compact('providers', 'statistics', 'settings', 'syncStatus'));
        } catch (\Exception $e) {
            Log::error('Admin Electricity Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load electricity management dashboard');
        }
    }

    /**
     * Perform automatic sync (called on page load)
     */
    private function performAutoSync()
    {
        $discoMapping = $this->syncService->getDiscoProviderMapping();

        $added = 0;
        $updated = 0;
        $errors = [];

        foreach ($discoMapping as $discoName => $discoId) {
            try {
                $provider = ElectricityProvider::where('ePlan', $discoName)->first();

                $providerData = [
                    'ePlan' => $discoName,
                    'eProviderId' => strtolower($discoName),
                    'uzobest_disco_id' => $discoId,
                    'eBuyingPrice' => 45.00,
                    'ePrice' => 50.00,
                    'cost_price' => 45.00,
                    'selling_price' => 50.00,
                    'profit_margin' => 5.00,
                    'eStatus' => 1
                ];

                if ($provider) {
                    // Always update provider IDs and status
                    $provider->update([
                        'eProviderId' => strtolower($discoName),
                        'uzobest_disco_id' => $discoId,
                        'eStatus' => 1
                    ]);
                    $updated++;
                } else {
                    ElectricityProvider::create($providerData);
                    $added++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to sync {$discoName}: " . $e->getMessage();
            }
        }

        // Record sync status
        ServiceSyncStatus::recordSync(
            'electricity',
            $added + $updated,
            $added,
            $updated,
            count($errors),
            !empty($errors) ? implode('; ', array_slice($errors, 0, 3)) : null
        );
    }

    /**
     * Store new electricity provider
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ePlan' => 'required|string|max:100|unique:electricity,ePlan',
                'eProviderId' => 'nullable|string|max:50',
                'eBuyingPrice' => 'required|numeric|min:0',
                'ePrice' => 'required|numeric|min:0|gt:eBuyingPrice',
                'eStatus' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $provider = ElectricityProvider::create([
                'ePlan' => trim($request->ePlan),
                'eProviderId' => $request->eProviderId ?? strtolower(str_replace(' ', '', $request->ePlan)),
                'eBuyingPrice' => $request->eBuyingPrice,
                'ePrice' => $request->ePrice,
                'eStatus' => $request->has('eStatus') ? 1 : 0
            ]);

            // Log activity
            Log::info('New electricity provider added', [
                'provider_id' => $provider->eId,
                'provider_name' => $provider->ePlan
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Electricity provider added successfully',
                'data' => [
                    'provider' => $provider,
                    'redirect' => route('admin.electricity.index')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Add Electricity Provider Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add electricity provider'
            ], 500);
        }
    }

    /**
     * Update electricity provider
     */
    public function update(Request $request, ElectricityProvider $provider)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ePlan' => 'required|string|max:100|unique:electricity,ePlan,' . $provider->eId . ',eId',
                'eProviderId' => 'nullable|string|max:50',
                'eBuyingPrice' => 'required|numeric|min:0',
                'ePrice' => 'required|numeric|min:0|gt:eBuyingPrice',
                'eStatus' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldData = $provider->toArray();

            $provider->update([
                'ePlan' => trim($request->ePlan),
                'eProviderId' => $request->eProviderId ?? strtolower(str_replace(' ', '', $request->ePlan)),
                'eBuyingPrice' => $request->eBuyingPrice,
                'ePrice' => $request->ePrice,
                'eStatus' => $request->has('eStatus') ? 1 : 0
            ]);

            // Log activity
            Log::info('Electricity provider updated', [
                'provider_id' => $provider->eId,
                'old_data' => $oldData,
                'new_data' => $provider->toArray()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Electricity provider updated successfully',
                'data' => [
                    'provider' => $provider->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Update Electricity Provider Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update electricity provider'
            ], 500);
        }
    }

    /**
     * Toggle provider status
     */
    public function toggleStatus($id)
    {
        try {
            $provider = ElectricityProvider::findOrFail($id);
            $oldStatus = $provider->eStatus;

            $provider->toggleStatus();

            // Log activity
            Log::info('Electricity provider status toggled', [
                'provider_id' => $provider->eId,
                'provider_name' => $provider->ePlan,
                'old_status' => $oldStatus,
                'new_status' => $provider->eStatus
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Provider status updated successfully',
                'data' => [
                    'new_status' => $provider->eStatus,
                    'status_display' => $provider->status_display,
                    'status_badge_class' => $provider->status_badge_class
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle Electricity Provider Status Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update provider status'
            ], 500);
        }
    }

    /**
     * Delete electricity provider
     */
    public function destroy($id)
    {
        try {
            $provider = ElectricityProvider::findOrFail($id);

            // Check if provider has transactions
            $transactionCount = Transaction::where('servicename', 'Electricity Bill')
                ->where('servicedesc', 'like', '%' . $provider->ePlan . '%')
                ->count();

            if ($transactionCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete provider with existing transactions. Disable instead.'
                ], 400);
            }

            $providerData = $provider->toArray();
            $provider->delete();

            // Log activity
            Log::info('Electricity provider deleted', [
                'provider_data' => $providerData
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Electricity provider deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Electricity Provider Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete electricity provider'
            ], 500);
        }
    }

    /**
     * Get electricity transactions with filters
     */
    public function getTransactions(Request $request)
    {
        try {
            $query = Transaction::where('servicename', 'Electricity Bill')
                ->with('user:id,name,phone')
                ->orderBy('date', 'desc');

            // Apply filters
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->provider) {
                $query->where('servicedesc', 'like', '%' . $request->provider . '%');
            }

            if ($request->date) {
                $query->whereDate('date', $request->date);
            }

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('transref', 'like', '%' . $request->search . '%')
                        ->orWhere('servicedesc', 'like', '%' . $request->search . '%');
                });
            }

            $transactions = $query->paginate($request->length ?? 25);

            $data = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'user' => [
                        'name' => $transaction->user ? $transaction->user->name : 'Unknown',
                        'phone' => $transaction->user ? $transaction->user->phone : 'N/A'
                    ],
                    'provider' => $this->extractProviderFromDescription($transaction->servicedesc),
                    'meter_number' => $this->extractMeterFromDescription($transaction->servicedesc),
                    'amount' => $transaction->amount,
                    'profit' => $transaction->profit ?? 0,
                    'status' => $transaction->status,
                    'status_text' => $transaction->status_text,
                    'date' => $transaction->formatted_date,
                    'description' => $transaction->servicedesc
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'total_pages' => $transactions->lastPage(),
                    'total_records' => $transactions->total(),
                    'per_page' => $transactions->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get Electricity Transactions Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve transactions'
            ], 500);
        }
    }

    /**
     * Update electricity settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electricity_charges' => 'required|numeric|min:0',
                'minimum_amount' => 'required|numeric|min:100',
                'maximum_amount' => 'required|numeric|min:1000|gt:minimum_amount',
                'agent_discount' => 'required|numeric|min:0|max:100',
                'vendor_discount' => 'required|numeric|min:0|max:100',
                'maintenance_message' => 'nullable|string|max:500',
                'service_enabled' => 'boolean',
                'maintenance_mode' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update settings
            $settings = [
                'electricitycharges' => $request->electricity_charges,
                'electricity_minimum_amount' => $request->minimum_amount,
                'electricity_maximum_amount' => $request->maximum_amount,
                'electricity_agent_discount' => $request->agent_discount,
                'electricity_vendor_discount' => $request->vendor_discount,
                'electricity_maintenance_message' => $request->maintenance_message,
                'electricity_service_enabled' => $request->has('service_enabled'),
                'electricity_maintenance_mode' => $request->has('maintenance_mode')
            ];

            foreach ($settings as $key => $value) {
                SiteSettings::updateSetting($key, $value);
            }

            // Log activity
            Log::info('Electricity settings updated', [
                'settings' => $settings
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Electricity settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Update Electricity Settings Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update electricity settings'
            ], 500);
        }
    }

    /**
     * Update API configuration
     */
    public function updateApiConfig(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'electricity_api_url' => 'required|url',
                'electricity_api_key' => 'required|string',
                'electricity_validation_url' => 'required|url',
                'electricity_auth_type' => 'required|in:basic,token'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update API configuration
            $configs = [
                'electricity_api_url' => $request->electricity_api_url,
                'electricity_api_key' => $request->electricity_api_key,
                'electricity_validation_url' => $request->electricity_validation_url,
                'electricity_auth_type' => $request->electricity_auth_type
            ];

            foreach ($configs as $key => $value) {
                ApiConfig::updateConfig($key, $value);
            }

            // Log activity
            Log::info('Electricity API configuration updated', [
                'configs' => array_keys($configs) // Don't log sensitive data
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'API configuration updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Update Electricity API Config Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update API configuration'
            ], 500);
        }
    }

    /**
     * Test API validation
     */
    public function testValidation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'meter_number' => 'required|string',
                'provider' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->electricityService->validateMeterNumber(
                $request->provider,
                $request->meter_number,
                'prepaid'
            );

            return response()->json([
                'status' => 'success',
                'message' => 'API test completed',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Test Electricity Validation API Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'API test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync providers from API
     */
    public function syncProviders()
    {
        try {
            // This would typically call an external API to get available providers
            // For now, we'll add common Nigerian electricity providers
            $commonProviders = [
                ['ePlan' => 'AEDC', 'eProviderId' => 'aedc', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
                ['ePlan' => 'EKEDC', 'eProviderId' => 'ekedc', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
                ['ePlan' => 'IKEDC', 'eProviderId' => 'ikedc', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
                ['ePlan' => 'KEDCO', 'eProviderId' => 'kedco', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
                ['ePlan' => 'PHED', 'eProviderId' => 'phed', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
                ['ePlan' => 'JED', 'eProviderId' => 'jed', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
                ['ePlan' => 'IBEDC', 'eProviderId' => 'ibedc', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
                ['ePlan' => 'EEDC', 'eProviderId' => 'eedc', 'eBuyingPrice' => 45.00, 'ePrice' => 50.00],
            ];

            $added = 0;
            $updated = 0;

            foreach ($commonProviders as $providerData) {
                $provider = ElectricityProvider::where('ePlan', $providerData['ePlan'])->first();

                if ($provider) {
                    // Update existing provider if prices are different
                    if (
                        $provider->eBuyingPrice != $providerData['eBuyingPrice'] ||
                        $provider->ePrice != $providerData['ePrice']
                    ) {
                        $provider->update($providerData);
                        $updated++;
                    }
                } else {
                    // Create new provider
                    ElectricityProvider::create(array_merge($providerData, ['eStatus' => 1]));
                    $added++;
                }
            }

            // Log activity
            Log::info('Electricity providers synced', [
                'added' => $added,
                'updated' => $updated
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Sync completed. Added: {$added}, Updated: {$updated}",
                'data' => [
                    'added' => $added,
                    'updated' => $updated
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Sync Electricity Providers Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to sync providers'
            ], 500);
        }
    }

    /**
     * Sync electricity providers from Uzobest API
     * Uses the standard Nigerian disco providers supported by Uzobest
     *
     * @param UzobestSyncService $syncService
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncFromUzobest(UzobestSyncService $syncService)
    {
        try {
            // Get Uzobest disco provider mapping
            $discoMapping = $syncService->getDiscoProviderMapping();

            $added = 0;
            $updated = 0;
            $errors = [];

            foreach ($discoMapping as $discoName => $discoId) {
                try {
                    $provider = ElectricityProvider::where('ePlan', $discoName)->first();

                    $providerData = [
                        'ePlan' => $discoName,
                        'eProviderId' => strtolower($discoName),
                        'eBuyingPrice' => 45.00, // Base price - adjust based on your pricing
                        'ePrice' => 50.00, // Selling price with markup
                        'eStatus' => 1
                    ];

                    if ($provider) {
                        // Update existing provider
                        $provider->update($providerData);
                        $updated++;
                    } else {
                        // Create new provider
                        ElectricityProvider::create($providerData);
                        $added++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to sync {$discoName}: " . $e->getMessage();
                    Log::error("Electricity provider sync error", [
                        'provider' => $discoName,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Electricity providers synced from Uzobest', [
                'added' => $added,
                'updated' => $updated,
                'errors' => count($errors)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Sync completed. Added: {$added}, Updated: {$updated}",
                'data' => [
                    'added' => $added,
                    'updated' => $updated,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Uzobest electricity sync exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Exception during sync: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate meter number via Uzobest API
     * Endpoint: GET /api/validatemeter
     *
     * @param Request $request
     * @param UzobestSyncService $syncService
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateMeter(Request $request, UzobestSyncService $syncService)
    {
        $validator = Validator::make($request->all(), [
            'meter_number' => 'required|string|min:10|max:15',
            'provider' => 'required|string',
            'meter_type' => 'required|string|in:prepaid,postpaid,PREPAID,POSTPAID'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = $errors->first();

            return response()->json([
                'status' => 'error',
                'message' => $firstError ?: 'Validation failed',
                'errors' => $errors->toArray(),
                'data' => [],
                'debug' => [
                    'received' => $request->all()
                ]
            ], 422);
        }

        try {
            // Get electricity provider by name
            $providerName = strtoupper($request->provider);
            $provider = ElectricityProvider::where('ePlan', $providerName)->first();

            if (!$provider) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid electricity provider',
                    'data' => []
                ], 400);
            }

            // Get Uzobest provider ID from mapping or use the one stored in database
            $uzobestProviderId = $provider->uzobest_disco_id;

            if (!$uzobestProviderId) {
                $discoMapping = $syncService->getDiscoProviderMapping();
                $uzobestProviderId = $discoMapping[$providerName] ?? null;

                if (!$uzobestProviderId) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Provider not configured for Uzobest API',
                        'data' => []
                    ], 400);
                }
            }

            // Get meter type ID
            $meterTypeMapping = $syncService->getMeterTypeMapping();
            $meterTypeId = $meterTypeMapping[strtoupper($request->meter_type)] ?? 1;

            // Validate meter via Uzobest
            $result = $syncService->validateMeter(
                $request->meter_number,
                $uzobestProviderId,
                $meterTypeId
            );

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Meter number validated successfully',
                    'customer_name' => $result['customer_name'] ?? 'N/A',
                    'address' => $result['address'] ?? 'N/A',
                    'data' => $result['data'] ?? []
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Invalid meter number',
                    'data' => []
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Meter validation error', [
                'meter' => $request->meter_number,
                'provider' => $request->provider,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to validate meter number: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get electricity statistics
     */
    private function getElectricityStatistics()
    {
        $today = Carbon::today();

        return [
            'active_providers' => ElectricityProvider::active()->count(),
            'today_transactions' => Transaction::where('servicename', 'Electricity Bill')
                ->whereDate('date', $today)
                ->count(),
            'today_revenue' => Transaction::where('servicename', 'Electricity Bill')
                ->whereDate('date', $today)
                ->where('status', 0)
                ->sum('amount'),
            'total_profit' => Transaction::where('servicename', 'Electricity Bill')
                ->where('status', 0)
                ->sum('profit')
        ];
    }

    /**
     * Get electricity settings
     */
    private function getElectricitySettings()
    {
        $siteSettings = SiteSettings::getSiteSettings();

        return [
            'electricity_charges' => $siteSettings->electricitycharges ?? 50,
            'minimum_amount' => $siteSettings->electricity_minimum_amount ?? 1000,
            'maximum_amount' => $siteSettings->electricity_maximum_amount ?? 50000,
            'agent_discount' => $siteSettings->electricity_agent_discount ?? 1,
            'vendor_discount' => $siteSettings->electricity_vendor_discount ?? 2,
            'maintenance_message' => $siteSettings->electricity_maintenance_message ?? 'Electricity service is temporarily unavailable.',
            'service_enabled' => $siteSettings->electricity_service_enabled ?? true,
            'maintenance_mode' => $siteSettings->electricity_maintenance_mode ?? false
        ];
    }

    /**
     * Extract provider from transaction description
     */
    private function extractProviderFromDescription($description)
    {
        $providers = ['AEDC', 'EKEDC', 'IKEDC', 'KEDCO', 'PHED', 'JED', 'IBEDC', 'EEDC'];

        foreach ($providers as $provider) {
            if (stripos($description, $provider) !== false) {
                return $provider;
            }
        }

        return 'Unknown';
    }

    /**
     * Extract meter number from transaction description
     */
    private function extractMeterFromDescription($description)
    {
        preg_match('/meter number:\s*(\d+)/i', $description, $matches);
        return $matches[1] ?? 'N/A';
    }
}
