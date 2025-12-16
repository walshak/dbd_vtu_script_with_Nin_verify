<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataService;
use App\Models\NetworkId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    protected $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Show data purchase page
     */
    public function index()
    {
        $networks = NetworkId::all();
        return view('data.index', compact('networks'));
    }

    /**
     * Get data plans for a network
     */
    public function getPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'data_group' => 'required|string|in:SME,Gifting,Corporate'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters'
            ], 400);
        }

        $plans = $this->dataService->getDataPlansForUser(
            Auth::id(),
            $request->network,
            $request->data_group
        );

        return response()->json([
            'status' => 'success',
            'data' => $plans
        ]);
    }

    /**
     * Purchase data bundle
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'plan_id' => 'required|string',
            'data_group' => 'required|string|in:SME,Gifting,Corporate',
            'ported_number' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->dataService->purchaseData(
            Auth::id(),
            $request->network,
            $request->phone,
            $request->plan_id,
            $request->data_group,
            $request->ported_number ?? false
        );

        if ($result['status'] === 'success') {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    /**
     * Check if service is available
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'data_group' => 'required|string|in:SME,Gifting,Corporate'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters'
            ], 400);
        }

        $available = $this->dataService->isServiceAvailable(
            $request->network,
            $request->data_group
        );

        return response()->json([
            'status' => 'success',
            'available' => $available
        ]);
    }

    /**
     * API endpoint for mobile app
     */
    public function apiPurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'plan_id' => 'required|string',
            'data_group' => 'required|string|in:SME,Gifting,Corporate',
            'ported_number' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->dataService->purchaseData(
            Auth::id(),
            $request->network,
            $request->phone,
            $request->plan_id,
            $request->data_group,
            $request->ported_number ?? false
        );

        return response()->json($result);
    }
}
