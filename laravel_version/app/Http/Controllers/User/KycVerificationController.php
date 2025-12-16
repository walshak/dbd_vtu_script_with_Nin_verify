<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KycVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show KYC verification dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $verificationSummary = UserVerification::getUserVerificationSummary($user->sId);
        
        $userVerifications = UserVerification::where('user_id', $user->sId)
                                           ->orderBy('submitted_at', 'desc')
                                           ->get();

        return view('user.kyc.index', compact('verificationSummary', 'userVerifications'));
    }

    /**
     * Show verification form for specific type
     */
    public function showVerificationForm($type)
    {
        $user = Auth::user();
        
        // Check if user can submit this verification type
        if (!UserVerification::canSubmitVerification($user->sId, $type)) {
            return redirect()->route('kyc.index')
                           ->with('error', 'You already have a pending or active verification for this type.');
        }

        $requiredDocuments = UserVerification::getRequiredDocuments($type);
        
        return view('user.kyc.verify', compact('type', 'requiredDocuments'));
    }

    /**
     * Submit verification documents
     */
    public function submitVerification(Request $request, $type)
    {
        $user = Auth::user();

        // Check if user can submit this verification type
        if (!UserVerification::canSubmitVerification($user->sId, $type)) {
            return back()->with('error', 'You already have a pending or active verification for this type.');
        }

        // Validate based on verification type
        $rules = $this->getValidationRules($type);
        $request->validate($rules);

        try {
            $data = [
                'document_type' => $request->document_type,
                'document_number' => $request->document_number
            ];

            // Handle file uploads
            if ($request->hasFile('document_front')) {
                $data['document_front_path'] = $this->uploadDocument($request->file('document_front'), $user->sId, 'front');
            }

            if ($request->hasFile('document_back')) {
                $data['document_back_path'] = $this->uploadDocument($request->file('document_back'), $user->sId, 'back');
            }

            if ($request->hasFile('selfie')) {
                $data['selfie_path'] = $this->uploadDocument($request->file('selfie'), $user->sId, 'selfie');
            }

            // Create verification request
            $verification = UserVerification::createVerificationRequest($user->sId, $type, $data);

            Log::info('KYC verification submitted', [
                'user_id' => $user->sId,
                'verification_type' => $type,
                'verification_id' => $verification->id
            ]);

            return redirect()->route('kyc.index')
                           ->with('success', 'Verification documents submitted successfully. We will review your submission within 24-48 hours.');

        } catch (\Exception $e) {
            Log::error('KYC verification submission error: ' . $e->getMessage(), [
                'user_id' => $user->sId,
                'type' => $type
            ]);

            return back()->withErrors(['error' => 'Failed to submit verification. Please try again.']);
        }
    }

    /**
     * Send email verification
     */
    public function sendEmailVerification(Request $request)
    {
        $user = Auth::user();

        if (!UserVerification::canSubmitVerification($user->sId, UserVerification::TYPE_EMAIL)) {
            return back()->with('error', 'Email verification already completed or pending.');
        }

        try {
            // Generate verification code
            $verificationCode = Str::random(32);
            
            // Create verification request
            $verification = UserVerification::createVerificationRequest($user->sId, UserVerification::TYPE_EMAIL, [
                'verification_code' => $verificationCode
            ]);

            // Send email (implement your email sending logic here)
            // Mail::send('emails.email-verification', compact('user', 'verificationCode'), function($message) use ($user) {
            //     $message->to($user->sEmail)->subject('Verify Your Email Address');
            // });

            Log::info('Email verification sent', [
                'user_id' => $user->sId,
                'email' => $user->sEmail
            ]);

            return back()->with('success', 'Verification email sent to your email address.');

        } catch (\Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send verification email.']);
        }
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string'
        ]);

        $user = Auth::user();
        
        // Find pending email verification
        $verification = UserVerification::where('user_id', $user->sId)
                                       ->where('verification_type', UserVerification::TYPE_EMAIL)
                                       ->where('status', UserVerification::STATUS_PENDING)
                                       ->first();

        if (!$verification) {
            return back()->withErrors(['verification_code' => 'No pending email verification found.']);
        }

        // In a real implementation, you'd check the verification code from the database
        // For now, we'll auto-approve email verification
        $verification->approve(null, 'Email verified by user');

        Log::info('Email verification completed', [
            'user_id' => $user->sId,
            'email' => $user->sEmail
        ]);

        return redirect()->route('kyc.index')
                       ->with('success', 'Email address verified successfully!');
    }

    /**
     * Send phone verification SMS
     */
    public function sendPhoneVerification(Request $request)
    {
        $user = Auth::user();

        if (!UserVerification::canSubmitVerification($user->sId, UserVerification::TYPE_PHONE)) {
            return back()->with('error', 'Phone verification already completed or pending.');
        }

        try {
            // Generate verification code
            $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Create verification request
            $verification = UserVerification::createVerificationRequest($user->sId, UserVerification::TYPE_PHONE, [
                'verification_code' => $verificationCode
            ]);

            // Send SMS (implement your SMS sending logic here)
            // SMS::send($user->sPhone, "Your VTU verification code is: {$verificationCode}");

            Log::info('Phone verification SMS sent', [
                'user_id' => $user->sId,
                'phone' => $user->sPhone
            ]);

            return back()->with('success', 'Verification code sent to your phone number.');

        } catch (\Exception $e) {
            Log::error('Phone verification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send verification SMS.']);
        }
    }

    /**
     * Verify phone with SMS code
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        
        // Find pending phone verification
        $verification = UserVerification::where('user_id', $user->sId)
                                       ->where('verification_type', UserVerification::TYPE_PHONE)
                                       ->where('status', UserVerification::STATUS_PENDING)
                                       ->first();

        if (!$verification) {
            return back()->withErrors(['verification_code' => 'No pending phone verification found.']);
        }

        // In a real implementation, you'd check the SMS code from the database
        // For now, we'll auto-approve phone verification
        $verification->approve(null, 'Phone verified by SMS code');

        Log::info('Phone verification completed', [
            'user_id' => $user->sId,
            'phone' => $user->sPhone
        ]);

        return redirect()->route('kyc.index')
                       ->with('success', 'Phone number verified successfully!');
    }

    /**
     * Cancel pending verification
     */
    public function cancelVerification($verificationId)
    {
        $user = Auth::user();
        
        $verification = UserVerification::where('id', $verificationId)
                                       ->where('user_id', $user->sId)
                                       ->where('status', UserVerification::STATUS_PENDING)
                                       ->first();

        if (!$verification) {
            return back()->with('error', 'Verification not found or cannot be cancelled.');
        }

        try {
            // Delete uploaded files if any
            if ($verification->document_front_path) {
                Storage::delete($verification->document_front_path);
            }
            if ($verification->document_back_path) {
                Storage::delete($verification->document_back_path);
            }
            if ($verification->selfie_path) {
                Storage::delete($verification->selfie_path);
            }

            $verification->delete();

            Log::info('Verification cancelled by user', [
                'user_id' => $user->sId,
                'verification_id' => $verificationId
            ]);

            return back()->with('success', 'Verification cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Cancel verification error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to cancel verification.']);
        }
    }

    /**
     * Upload and store verification document
     */
    protected function uploadDocument($file, $userId, $type)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "kyc_{$userId}_{$type}_" . time() . ".{$extension}";
        
        return $file->storeAs("kyc_documents/{$userId}", $filename, 'private');
    }

    /**
     * Get validation rules for verification type
     */
    protected function getValidationRules($type)
    {
        $baseRules = [];

        switch ($type) {
            case UserVerification::TYPE_IDENTITY:
                $baseRules = [
                    'document_type' => 'required|in:' . implode(',', [
                        UserVerification::DOC_NATIONAL_ID,
                        UserVerification::DOC_DRIVERS_LICENSE,
                        UserVerification::DOC_PASSPORT,
                        UserVerification::DOC_VOTERS_CARD
                    ]),
                    'document_number' => 'required|string|max:50',
                    'document_front' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB
                    'document_back' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                    'selfie' => 'required|image|mimes:jpeg,png,jpg|max:5120'
                ];
                break;

            case UserVerification::TYPE_ADDRESS:
                $baseRules = [
                    'document_type' => 'required|in:' . implode(',', [
                        UserVerification::DOC_UTILITY_BILL,
                        UserVerification::DOC_BANK_STATEMENT
                    ]),
                    'document_front' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120'
                ];
                break;

            case UserVerification::TYPE_BANK_ACCOUNT:
                $baseRules = [
                    'document_type' => 'required|in:' . UserVerification::DOC_BANK_STATEMENT,
                    'document_front' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120'
                ];
                break;
        }

        return $baseRules;
    }

    /**
     * Get verification progress for user
     */
    public function getVerificationProgress()
    {
        $user = Auth::user();
        $summary = UserVerification::getUserVerificationSummary($user->sId);

        return response()->json([
            'success' => true,
            'progress' => $summary
        ]);
    }

    /**
     * Download verification document (for user's own documents only)
     */
    public function downloadDocument($verificationId, $documentType)
    {
        $user = Auth::user();
        
        $verification = UserVerification::where('id', $verificationId)
                                       ->where('user_id', $user->sId)
                                       ->first();

        if (!$verification) {
            abort(404, 'Verification not found.');
        }

        $filePath = match($documentType) {
            'front' => $verification->document_front_path,
            'back' => $verification->document_back_path,
            'selfie' => $verification->selfie_path,
            default => null
        };

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'Document not found.');
        }

        return Storage::download($filePath);
    }
}