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
     * Helper method to get config value
     */
    private function getConfigValue($key, $default = '')
    {
        $config = ApiConfig::where('key', $key)->first();
        return $config ? $config->value : $default;
    }
}
