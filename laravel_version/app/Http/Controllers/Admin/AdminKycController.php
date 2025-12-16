<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminKycController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show KYC management dashboard
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $search = $request->get('search');

        $query = UserVerification::with('user');

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by type
        if ($type !== 'all') {
            $query->where('verification_type', $type);
        }

        // Search by user details
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('sFirstname', 'like', "%{$search}%")
                  ->orWhere('sLastname', 'like', "%{$search}%")
                  ->orWhere('sEmail', 'like', "%{$search}%")
                  ->orWhere('sPhone', 'like', "%{$search}%");
            });
        }

        $verifications = $query->orderBy('submitted_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => UserVerification::count(),
            'pending' => UserVerification::where('status', UserVerification::STATUS_PENDING)->count(),
            'approved' => UserVerification::where('status', UserVerification::STATUS_APPROVED)->count(),
            'rejected' => UserVerification::where('status', UserVerification::STATUS_REJECTED)->count(),
            'expired' => UserVerification::where('status', UserVerification::STATUS_EXPIRED)->count(),
        ];

        // Get verification type stats
        $typeStats = UserVerification::selectRaw('verification_type, status, COUNT(*) as count')
                                   ->groupBy('verification_type', 'status')
                                   ->get()
                                   ->groupBy('verification_type');

        return view('admin.kyc.index', compact('verifications', 'stats', 'typeStats', 'status', 'type', 'search'));
    }

    /**
     * Show verification details for review
     */
    public function show($id)
    {
        $verification = UserVerification::with('user')->findOrFail($id);
        
        // Get user's verification history
        $userVerificationHistory = UserVerification::where('user_id', $verification->user_id)
                                                  ->where('id', '!=', $id)
                                                  ->orderBy('submitted_at', 'desc')
                                                  ->get();

        // Get user's complete verification summary
        $userVerificationSummary = UserVerification::getUserVerificationSummary($verification->user_id);

        return view('admin.kyc.show', compact('verification', 'userVerificationHistory', 'userVerificationSummary'));
    }

    /**
     * Approve verification
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $verification = UserVerification::findOrFail($id);
        $admin = Auth::guard('admin')->user();

        if ($verification->status !== UserVerification::STATUS_PENDING) {
            return back()->with('error', 'Only pending verifications can be approved.');
        }

        try {
            $verification->approve($admin->id, $request->admin_notes);

            Log::info('Verification approved by admin', [
                'verification_id' => $id,
                'admin_id' => $admin->id,
                'user_id' => $verification->user_id,
                'verification_type' => $verification->verification_type
            ]);

            return redirect()->route('admin.kyc.index')
                           ->with('success', 'Verification approved successfully.');

        } catch (\Exception $e) {
            Log::error('KYC approval error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to approve verification.']);
        }
    }

    /**
     * Reject verification
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $verification = UserVerification::findOrFail($id);
        $admin = Auth::guard('admin')->user();

        if ($verification->status !== UserVerification::STATUS_PENDING) {
            return back()->with('error', 'Only pending verifications can be rejected.');
        }

        try {
            $verification->reject($admin->id, $request->rejection_reason);

            Log::info('Verification rejected by admin', [
                'verification_id' => $id,
                'admin_id' => $admin->id,
                'user_id' => $verification->user_id,
                'verification_type' => $verification->verification_type,
                'reason' => $request->rejection_reason
            ]);

            return redirect()->route('admin.kyc.index')
                           ->with('success', 'Verification rejected successfully.');

        } catch (\Exception $e) {
            Log::error('KYC rejection error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to reject verification.']);
        }
    }

    /**
     * Request additional information
     */
    public function requestInfo(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        $verification = UserVerification::findOrFail($id);
        $admin = Auth::guard('admin')->user();

        if ($verification->status !== UserVerification::STATUS_PENDING) {
            return back()->with('error', 'Can only request info for pending verifications.');
        }

        try {
            $verification->update([
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now()
            ]);

            // Send notification to user (implement notification system)
            // Notification::send($verification->user, new KycInfoRequested($verification));

            Log::info('Additional info requested for verification', [
                'verification_id' => $id,
                'admin_id' => $admin->id,
                'user_id' => $verification->user_id
            ]);

            return back()->with('success', 'Additional information requested successfully.');

        } catch (\Exception $e) {
            Log::error('KYC info request error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to request additional information.']);
        }
    }

    /**
     * Download verification document
     */
    public function downloadDocument($verificationId, $documentType)
    {
        $verification = UserVerification::findOrFail($verificationId);

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

    /**
     * Get KYC statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'overview' => [
                'total_verifications' => UserVerification::count(),
                'pending_review' => UserVerification::where('status', UserVerification::STATUS_PENDING)->count(),
                'approved_today' => UserVerification::where('status', UserVerification::STATUS_APPROVED)
                                                  ->whereDate('approved_at', today())->count(),
                'rejected_today' => UserVerification::where('status', UserVerification::STATUS_REJECTED)
                                                  ->whereDate('reviewed_at', today())->count(),
            ],
            'by_type' => UserVerification::selectRaw('verification_type, COUNT(*) as count')
                                       ->groupBy('verification_type')
                                       ->pluck('count', 'verification_type'),
            'by_status' => UserVerification::selectRaw('status, COUNT(*) as count')
                                         ->groupBy('status')
                                         ->pluck('count', 'status'),
            'weekly_submissions' => UserVerification::selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
                                                  ->where('submitted_at', '>=', now()->subDays(7))
                                                  ->groupBy('date')
                                                  ->orderBy('date')
                                                  ->pluck('count', 'date'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Bulk approve verifications
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'verification_ids' => 'required|array',
            'verification_ids.*' => 'integer|exists:user_verifications,id',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $admin = Auth::guard('admin')->user();
        $approved = 0;
        $errors = [];

        foreach ($request->verification_ids as $id) {
            try {
                $verification = UserVerification::where('id', $id)
                                               ->where('status', UserVerification::STATUS_PENDING)
                                               ->first();

                if ($verification) {
                    $verification->approve($admin->id, $request->admin_notes);
                    $approved++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to approve verification ID: {$id}";
                Log::error("Bulk approve error for verification {$id}: " . $e->getMessage());
            }
        }

        if ($approved > 0) {
            Log::info('Bulk verification approval', [
                'admin_id' => $admin->id,
                'approved_count' => $approved,
                'total_requested' => count($request->verification_ids)
            ]);
        }

        $message = "{$approved} verifications approved successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Bulk reject verifications
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'verification_ids' => 'required|array',
            'verification_ids.*' => 'integer|exists:user_verifications,id',
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $admin = Auth::guard('admin')->user();
        $rejected = 0;
        $errors = [];

        foreach ($request->verification_ids as $id) {
            try {
                $verification = UserVerification::where('id', $id)
                                               ->where('status', UserVerification::STATUS_PENDING)
                                               ->first();

                if ($verification) {
                    $verification->reject($admin->id, $request->rejection_reason);
                    $rejected++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to reject verification ID: {$id}";
                Log::error("Bulk reject error for verification {$id}: " . $e->getMessage());
            }
        }

        if ($rejected > 0) {
            Log::info('Bulk verification rejection', [
                'admin_id' => $admin->id,
                'rejected_count' => $rejected,
                'total_requested' => count($request->verification_ids)
            ]);
        }

        $message = "{$rejected} verifications rejected successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Export verifications data
     */
    public function export(Request $request)
    {
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = UserVerification::with('user');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('verification_type', $type);
        }

        if ($dateFrom) {
            $query->whereDate('submitted_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('submitted_at', '<=', $dateTo);
        }

        $verifications = $query->orderBy('submitted_at', 'desc')->get();

        $csvData = "ID,User Name,Email,Phone,Verification Type,Status,Submitted At,Reviewed At,Admin Notes\n";

        foreach ($verifications as $verification) {
            $userName = $verification->user ? 
                       $verification->user->sFirstname . ' ' . $verification->user->sLastname : 
                       'Unknown User';
            
            $csvData .= implode(',', [
                $verification->id,
                '"' . $userName . '"',
                $verification->user ? $verification->user->sEmail : '',
                $verification->user ? $verification->user->sPhone : '',
                $verification->verification_type,
                $verification->status,
                $verification->submitted_at,
                $verification->reviewed_at ?? '',
                '"' . str_replace('"', '""', $verification->admin_notes ?? '') . '"'
            ]) . "\n";
        }

        $filename = 'kyc_verifications_' . date('Y-m-d_H-i-s') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Mark expired verifications
     */
    public function markExpired()
    {
        try {
            $expiredCount = UserVerification::markExpiredVerifications();

            Log::info('Expired verifications marked', [
                'count' => $expiredCount,
                'admin_id' => Auth::guard('admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$expiredCount} expired verifications marked.",
                'count' => $expiredCount
            ]);

        } catch (\Exception $e) {
            Log::error('Mark expired verifications error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark expired verifications.'
            ], 500);
        }
    }
}