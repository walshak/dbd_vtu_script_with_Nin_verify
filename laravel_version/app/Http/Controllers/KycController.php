<?php

namespace App\Http\Controllers;

use App\Services\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    private $kycService;

    public function __construct(KycService $kycService)
    {
        $this->middleware('auth');
        $this->kycService = $kycService;
    }

    /**
     * Show KYC verification page
     */
    public function showVerificationForm()
    {
        $user = Auth::user();

        // Redirect if already verified
        if ($user->kyc_status === 'verified') {
            return redirect()->route('user.fund-wallet')
                ->with('info', 'Your KYC is already verified.');
        }

        return view('kyc-verification');
    }

    /**
     * Verify NIN
     */
    public function verifyNin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nin' => [
                'required',
                'digits:11',
                'unique:users,nin',
                'regex:/^[0-9]{11}$/'
            ]
        ], [
            'nin.required' => 'NIN is required',
            'nin.digits' => 'NIN must be exactly 11 digits',
            'nin.unique' => 'This NIN is already registered',
            'nin.regex' => 'NIN must contain only numbers'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Verify NIN with Monnify
        $result = $this->kycService->verifyNin($request->nin);

        if ($result['success']) {
            // Save NIN to user profile
            $saved = $this->kycService->saveKycData(Auth::user(), [
                'nin' => $request->nin,
                'method' => 'nin'
            ]);

            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'NIN verified successfully! Redirecting to fund wallet...'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification successful but failed to save data. Please try again.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Verify BVN
     */
    public function verifyBvn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bvn' => [
                'required',
                'digits:11',
                'unique:users,bvn',
                'regex:/^[0-9]{11}$/'
            ],
            'dob' => 'required|date|before:today'
        ], [
            'bvn.required' => 'BVN is required',
            'bvn.digits' => 'BVN must be exactly 11 digits',
            'bvn.unique' => 'This BVN is already registered',
            'bvn.regex' => 'BVN must contain only numbers',
            'dob.required' => 'Date of birth is required',
            'dob.date' => 'Please provide a valid date',
            'dob.before' => 'Date of birth must be in the past'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = Auth::user();
        $fullName = trim($user->first_name . ' ' . $user->last_name);

        // Verify BVN with Monnify
        $result = $this->kycService->verifyBvn(
            $request->bvn,
            $fullName,
            $request->dob,
            $user->phone ?? ''
        );

        if ($result['success']) {
            // Save BVN to user profile
            $saved = $this->kycService->saveKycData($user, [
                'bvn' => $request->bvn,
                'date_of_birth' => $request->dob,
                'method' => 'bvn'
            ]);

            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'BVN verified successfully! Redirecting to fund wallet...'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification successful but failed to save data. Please try again.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Get KYC status
     */
    public function getStatus()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'kyc_status' => $user->kyc_status,
                'kyc_method' => $user->kyc_method,
                'has_nin' => !empty($user->nin),
                'has_bvn' => !empty($user->bvn),
                'verified_at' => $user->kyc_verified_at ? $user->kyc_verified_at->format('Y-m-d H:i:s') : null
            ]
        ]);
    }
}
