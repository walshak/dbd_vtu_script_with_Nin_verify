<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NetworkId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NetworkSettingsController extends Controller
{
    public function index(Request $request)
    {
        $network = $request->get('network', 'MTN');
        $networkData = NetworkId::where('network', strtolower($network))->first();

        // Create network record if it doesn't exist
        if (!$networkData) {
            $networkData = NetworkId::create([
                'network' => strtolower($network),
                'networkid' => $this->getDefaultNetworkId($network),
                'smeId' => '1',
                'giftingId' => '1',
                'corporateId' => '1',
                'airtimeId' => '1',
                'status' => 'On',
                'networkStatus' => 'On',
                'vtuStatus' => 'On',
                'sharesellStatus' => 'On',
                'smeStatus' => 'On',
                'giftingStatus' => 'On',
                'corporateStatus' => 'On',
                'airtimepinStatus' => 'On',
                'datapinStatus' => 'On'
            ]);
        }

        $allNetworks = NetworkId::all();

        return view('admin.network-settings.index', compact('networkData', 'network', 'allNetworks'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|integer|exists:network_ids,nId',
            'general' => 'required|in:On,Off',
            'vtuStatus' => 'required|in:On,Off',
            'sharesellStatus' => 'required|in:On,Off',
            'sme' => 'required|in:On,Off',
            'gifting' => 'required|in:On,Off',
            'corporate' => 'required|in:On,Off',
            'airtimepin' => 'required|in:On,Off',
            'datapin' => 'required|in:On,Off',
            'networkid' => 'required|integer|min:1',
            'smeId' => 'required|integer|min:1',
            'giftingId' => 'required|integer|min:1',
            'corporateId' => 'required|integer|min:1',
            'vtuId' => 'required|integer|min:1',
            'sharesellId' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $networkData = NetworkId::findOrFail($request->network);

        $networkData->update([
            'networkStatus' => $request->general,
            'vtuStatus' => $request->vtuStatus,
            'sharesellStatus' => $request->sharesellStatus,
            'smeStatus' => $request->sme,
            'giftingStatus' => $request->gifting,
            'corporateStatus' => $request->corporate,
            'airtimepinStatus' => $request->airtimepin,
            'datapinStatus' => $request->datapin,
            'networkid' => $request->networkid,
            'smeId' => $request->smeId,
            'giftingId' => $request->giftingId,
            'corporateId' => $request->corporateId,
            'vtuId' => $request->vtuId,
            'sharesellId' => $request->sharesellId,
        ]);

        return redirect()->back()
            ->with('success', strtoupper($networkData->network) . ' network settings updated successfully');
    }

    public function getNetworkStatus(Request $request)
    {
        $network = $request->get('network');

        if ($network) {
            $networkData = NetworkId::where('network', strtolower($network))->first();

            if ($networkData) {
                return response()->json([
                    'success' => true,
                    'network' => $networkData,
                    'services' => $networkData->getAvailableServices()
                ]);
            }
        }

        $allNetworks = NetworkId::all();
        return response()->json([
            'success' => true,
            'networks' => $allNetworks
        ]);
    }

    public function toggleService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required|integer|exists:network_ids,nId',
            'service' => 'required|string|in:general,vtu,sharesell,sme,gifting,corporate,airtimepin,datapin',
            'status' => 'required|in:On,Off'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input parameters'
            ], 400);
        }

        $networkData = NetworkId::findOrFail($request->network_id);

        $statusField = $this->getStatusField($request->service);
        $networkData->update([$statusField => $request->status]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->service) . ' service ' . strtolower($request->status) . ' for ' . strtoupper($networkData->network),
            'network' => $networkData
        ]);
    }

    public function bulkToggle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'networks' => 'required|array',
            'networks.*' => 'integer|exists:network_ids,nId',
            'service' => 'required|string|in:general,vtu,sharesell,sme,gifting,corporate,airtimepin,datapin',
            'status' => 'required|in:On,Off'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input parameters'
            ], 400);
        }

        $statusField = $this->getStatusField($request->service);

        NetworkId::whereIn('nId', $request->networks)
            ->update([$statusField => $request->status]);

        $networkNames = NetworkId::whereIn('nId', $request->networks)
            ->pluck('network')
            ->map(function($name) { return strtoupper($name); })
            ->implode(', ');

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->service) . ' service ' . strtolower($request->status) . ' for networks: ' . $networkNames
        ]);
    }

    private function getStatusField($service)
    {
        $fieldMap = [
            'general' => 'networkStatus',
            'vtu' => 'vtuStatus',
            'sharesell' => 'sharesellStatus',
            'sme' => 'smeStatus',
            'gifting' => 'giftingStatus',
            'corporate' => 'corporateStatus',
            'airtimepin' => 'airtimepinStatus',
            'datapin' => 'datapinStatus'
        ];

        return $fieldMap[$service] ?? 'networkStatus';
    }

    private function getDefaultNetworkId($network)
    {
        $defaultIds = [
            'MTN' => 1,
            'AIRTEL' => 2,
            'GLO' => 3,
            '9MOBILE' => 4
        ];

        return $defaultIds[strtoupper($network)] ?? 1;
    }

    public function getServiceAnalytics(Request $request)
    {
        $network = $request->get('network');
        $service = $request->get('service');

        // Get service analytics data
        $analytics = [
            'total_transactions' => 0,
            'successful_transactions' => 0,
            'failed_transactions' => 0,
            'success_rate' => 0,
            'revenue' => 0
        ];

        // This would normally query transaction tables based on network and service
        // For now, returning sample data structure

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'network' => $network,
            'service' => $service
        ]);
    }
}
