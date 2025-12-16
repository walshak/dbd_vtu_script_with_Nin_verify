<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserVerification extends Model
{
    use HasFactory;

    protected $table = 'kyc_verification';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nin',
        'bvn',
        'document_type',
        'document_path',
        'verification_status',
        'verification_response',
        'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Verification types
     */
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';
    const TYPE_IDENTITY = 'identity';
    const TYPE_ADDRESS = 'address';
    const TYPE_BANK_ACCOUNT = 'bank_account';

    /**
     * Document types for identity verification
     */
    const DOC_NATIONAL_ID = 'national_id';
    const DOC_DRIVERS_LICENSE = 'drivers_license';
    const DOC_PASSPORT = 'passport';
    const DOC_VOTERS_CARD = 'voters_card';
    const DOC_UTILITY_BILL = 'utility_bill';
    const DOC_BANK_STATEMENT = 'bank_statement';

    /**
     * Verification status
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    /**
     * Get the user that owns this verification
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the admin who verified this submission
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    /**
     * Create new verification request
     */
    public static function createVerificationRequest($userId, $type, $data = [])
    {
        return static::create([
            'user_id' => $userId,
            'verification_type' => $type,
            'document_type' => $data['document_type'] ?? null,
            'document_number' => $data['document_number'] ?? null,
            'document_front_path' => $data['document_front_path'] ?? null,
            'document_back_path' => $data['document_back_path'] ?? null,
            'selfie_path' => $data['selfie_path'] ?? null,
            'status' => self::STATUS_PENDING,
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Approve verification
     */
    public function approve($adminId, $notes = null)
    {
        $this->status = self::STATUS_APPROVED;
        $this->verified_at = now();
        $this->verified_by = $adminId;
        $this->rejection_reason = null;
        $this->updated_at = now();

        $this->save();

        // Update user verification status
        $this->updateUserVerificationLevel();

        return $this;
    }

    /**
     * Reject verification
     */
    public function reject($adminId, $reason)
    {
        $this->status = self::STATUS_REJECTED;
        $this->verified_by = $adminId;
        $this->rejection_reason = $reason;
        $this->updated_at = now();

        $this->save();

        return $this;
    }

    /**
     * Check if verification is expired
     */
    public function isExpired()
    {
        if (!$this->verified_at) {
            return false;
        }

        // Different expiry periods for different verification types
        $expiryMonths = match($this->verification_type) {
            self::TYPE_IDENTITY => 24, // 2 years
            self::TYPE_ADDRESS => 6,   // 6 months
            self::TYPE_BANK_ACCOUNT => 12, // 1 year
            default => 12
        };

        return $this->verified_at->addMonths($expiryMonths) < now();
    }

    /**
     * Get verification status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_EXPIRED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get verification status text
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Verified',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_EXPIRED => 'Expired',
            default => 'Unknown'
        };
    }

    /**
     * Get document type display name
     */
    public function getDocumentTypeDisplayAttribute()
    {
        return match($this->document_type) {
            self::DOC_NATIONAL_ID => 'National ID Card',
            self::DOC_DRIVERS_LICENSE => "Driver's License",
            self::DOC_PASSPORT => 'International Passport',
            self::DOC_VOTERS_CARD => "Voter's Card",
            self::DOC_UTILITY_BILL => 'Utility Bill',
            self::DOC_BANK_STATEMENT => 'Bank Statement',
            default => ucfirst(str_replace('_', ' ', $this->document_type))
        };
    }

    /**
     * Get verification type display name
     */
    public function getVerificationTypeDisplayAttribute()
    {
        return match($this->verification_type) {
            self::TYPE_EMAIL => 'Email Verification',
            self::TYPE_PHONE => 'Phone Verification',
            self::TYPE_IDENTITY => 'Identity Verification',
            self::TYPE_ADDRESS => 'Address Verification',
            self::TYPE_BANK_ACCOUNT => 'Bank Account Verification',
            default => ucfirst(str_replace('_', ' ', $this->verification_type))
        };
    }

    /**
     * Update user verification level based on completed verifications
     */
    protected function updateUserVerificationLevel()
    {
        $user = $this->user;
        if (!$user) return;

        $verifications = static::where('user_id', $user->id)
                              ->where('status', self::STATUS_APPROVED)
                              ->whereDate('verified_at', '>', now()->subMonths(24))
                              ->pluck('verification_type')
                              ->toArray();

        // Calculate verification level
        $level = 0;

        if (in_array(self::TYPE_EMAIL, $verifications)) $level += 1;
        if (in_array(self::TYPE_PHONE, $verifications)) $level += 1;
        if (in_array(self::TYPE_IDENTITY, $verifications)) $level += 2;
        if (in_array(self::TYPE_ADDRESS, $verifications)) $level += 1;
        if (in_array(self::TYPE_BANK_ACCOUNT, $verifications)) $level += 1;

        // Update user verification level (add field to users table if needed)
        // $user->verification_level = $level;
        // $user->save();
    }

    /**
     * Get pending verifications for admin
     */
    public static function getPendingVerifications($limit = 50)
    {
        return static::with(['user'])
                    ->where('status', self::STATUS_PENDING)
                    ->orderBy('submitted_at', 'asc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get user verification summary
     */
    public static function getUserVerificationSummary($userId)
    {
        $verifications = static::where('user_id', $userId)->get();

        $summary = [
            'total_submissions' => $verifications->count(),
            'approved' => $verifications->where('status', self::STATUS_APPROVED)->count(),
            'pending' => $verifications->where('status', self::STATUS_PENDING)->count(),
            'rejected' => $verifications->where('status', self::STATUS_REJECTED)->count(),
            'verification_level' => 0,
            'completed_types' => [],
            'required_types' => [
                self::TYPE_EMAIL,
                self::TYPE_PHONE,
                self::TYPE_IDENTITY
            ]
        ];

        // Get completed verification types
        $approved = $verifications->where('status', self::STATUS_APPROVED);
        foreach ($approved as $verification) {
            if (!$verification->isExpired()) {
                $summary['completed_types'][] = $verification->verification_type;
                $summary['verification_level'] += match($verification->verification_type) {
                    self::TYPE_EMAIL => 1,
                    self::TYPE_PHONE => 1,
                    self::TYPE_IDENTITY => 2,
                    self::TYPE_ADDRESS => 1,
                    self::TYPE_BANK_ACCOUNT => 1,
                    default => 0
                };
            }
        }

        $summary['completed_types'] = array_unique($summary['completed_types']);
        $summary['is_fully_verified'] = count(array_intersect($summary['completed_types'], $summary['required_types'])) === count($summary['required_types']);

        return $summary;
    }

    /**
     * Get verification statistics for admin dashboard
     */
    public static function getVerificationStats($days = 30)
    {
        $since = Carbon::now()->subDays($days);

        $stats = [
            'total_submissions' => static::where('submitted_at', '>=', $since)->count(),
            'pending_reviews' => static::where('status', self::STATUS_PENDING)->count(),
            'approved_today' => static::where('status', self::STATUS_APPROVED)
                                     ->whereDate('verified_at', today())
                                     ->count(),
            'rejected_today' => static::where('status', self::STATUS_REJECTED)
                                     ->whereDate('updated_at', today())
                                     ->count(),
            'approval_rate' => 0,
            'avg_review_time' => 0,
            'by_type' => []
        ];

        // Calculate approval rate
        $totalDecisions = static::whereIn('status', [self::STATUS_APPROVED, self::STATUS_REJECTED])
                               ->where('updated_at', '>=', $since)
                               ->count();

        if ($totalDecisions > 0) {
            $approved = static::where('status', self::STATUS_APPROVED)
                             ->where('verified_at', '>=', $since)
                             ->count();
            $stats['approval_rate'] = round(($approved / $totalDecisions) * 100, 2);
        }

        // Calculate average review time (in hours)
        $reviewedSubmissions = static::whereIn('status', [self::STATUS_APPROVED, self::STATUS_REJECTED])
                                    ->whereNotNull(['submitted_at', 'updated_at'])
                                    ->where('updated_at', '>=', $since)
                                    ->get();

        if ($reviewedSubmissions->count() > 0) {
            $totalHours = 0;
            foreach ($reviewedSubmissions as $submission) {
                $totalHours += $submission->submitted_at->diffInHours($submission->updated_at);
            }
            $stats['avg_review_time'] = round($totalHours / $reviewedSubmissions->count(), 2);
        }

        // Statistics by verification type
        $types = [self::TYPE_EMAIL, self::TYPE_PHONE, self::TYPE_IDENTITY, self::TYPE_ADDRESS, self::TYPE_BANK_ACCOUNT];

        foreach ($types as $type) {
            $typeSubmissions = static::where('verification_type', $type)
                                    ->where('submitted_at', '>=', $since);

            $stats['by_type'][$type] = [
                'name' => ucfirst(str_replace('_', ' ', $type)),
                'submissions' => $typeSubmissions->count(),
                'approved' => $typeSubmissions->where('status', self::STATUS_APPROVED)->count(),
                'pending' => $typeSubmissions->where('status', self::STATUS_PENDING)->count(),
                'rejected' => $typeSubmissions->where('status', self::STATUS_REJECTED)->count()
            ];
        }

        return $stats;
    }

    /**
     * Check if user can submit verification for type
     */
    public static function canSubmitVerification($userId, $type)
    {
        // Check if there's already a pending or approved verification
        $existing = static::where('user_id', $userId)
                         ->where('verification_type', $type)
                         ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED])
                         ->first();

        if ($existing) {
            // If approved, check if expired
            if ($existing->status === self::STATUS_APPROVED) {
                return $existing->isExpired();
            }
            // If pending, cannot submit another
            return false;
        }

        return true;
    }

    /**
     * Get required documents for verification type
     */
    public static function getRequiredDocuments($type)
    {
        return match($type) {
            self::TYPE_IDENTITY => [
                'documents' => [self::DOC_NATIONAL_ID, self::DOC_DRIVERS_LICENSE, self::DOC_PASSPORT, self::DOC_VOTERS_CARD],
                'required_files' => ['document_front', 'document_back', 'selfie'],
                'description' => 'Upload a clear photo of your government-issued ID and a selfie'
            ],
            self::TYPE_ADDRESS => [
                'documents' => [self::DOC_UTILITY_BILL, self::DOC_BANK_STATEMENT],
                'required_files' => ['document_front'],
                'description' => 'Upload a recent utility bill or bank statement showing your address'
            ],
            self::TYPE_BANK_ACCOUNT => [
                'documents' => [self::DOC_BANK_STATEMENT],
                'required_files' => ['document_front'],
                'description' => 'Upload a recent bank statement showing your account details'
            ],
            default => [
                'documents' => [],
                'required_files' => [],
                'description' => 'No documents required for this verification type'
            ]
        };
    }
}
