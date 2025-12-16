<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ExamPin;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ExamPinService;

class ExamPinController extends Controller
{
    protected $examPinService;

    public function __construct(ExamPinService $examPinService)
    {
        $this->middleware('auth');
        $this->examPinService = $examPinService;
    }

    /**
     * Show exam pin purchase page
     */
    public function index()
    {
        $examProviders = ExamPin::getActiveExamProviders();
        return view('exam-pins.index', compact('examProviders'));
    }

    /**
     * Get exam providers
     */
    public function getProviders()
    {
        $providers = ExamPin::getActiveExamProviders();

        return response()->json([
            'status' => 'success',
            'data' => $providers
        ]);
    }

    /**
     * Get available exams (alias for getProviders for API compatibility)
     */
    public function getAvailableExams()
    {
        return $this->getProviders();
    }

    /**
     * Get exam pin pricing
     */
    public function getPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'quantity' => 'required|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $examProvider = ExamPin::getByPlan($request->provider);

        if (!$examProvider || !$examProvider->isActive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exam provider not found or inactive'
            ], 400);
        }

        $user = Auth::user();
        $unitPrice = $examProvider->getUserPrice($user->sType);
        $totalAmount = $unitPrice * $request->quantity;

        return response()->json([
            'status' => 'success',
            'data' => [
                'provider' => $request->provider,
                'unit_price' => $unitPrice,
                'quantity' => $request->quantity,
                'total_amount' => $totalAmount,
                'description' => $examProvider->description
            ]
        ]);
    }

    /**
     * Purchase exam pin
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'quantity' => 'required|integer|min:1|max:50',
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'transaction_pin' => 'required|string|size:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Verify transaction PIN
        $user = Auth::user();
        if (!$this->verifyTransactionPin($user, $request->transaction_pin)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid transaction PIN'
            ], 400);
        }

        // Use the ExamPinService to handle the purchase
        $result = $this->examPinService->purchaseExamPin(
            Auth::id(),
            $request->provider,
            $request->phone,
            $request->quantity
        );

        return response()->json($result, $result['status'] === 'success' ? 200 : 400);
    }

    /**
     * Get exam pin history
     */
    public function history()
    {
        $history = $this->examPinService->getExamPinHistory(Auth::id());

        return view('user.exam-pin-history', [
            'transactions' => $history
        ]);
    }

    /**
     * Verify transaction PIN
     */
    private function verifyTransactionPin($user, $pin)
    {
        return $user->sPin === $pin;
    }
}
