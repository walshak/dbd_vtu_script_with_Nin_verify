<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserType;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'state',
        'user_type',
        'reg_status',
        'ver_code',
        'transaction_pin',
        'pin_status',
        'wallet_balance',
        'referral_wallet',
        'bank_account',
        'rolex_bank',
        'sterling_bank',
        'fidelity_bank',
        'bank_name',
        'api_key',
        'referral_code',
        'referred_by',
        'last_activity',
        'virtual_accounts',
        'monnify_reference'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'transaction_pin',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity' => 'datetime',
            'wallet_balance' => 'float',
            'referral_wallet' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Override Laravel's default username field for phone login
     */
    public function getAuthIdentifierName()
    {
        return 'phone';
    }

    /**
     * Get the username for authentication.
     */
    public function getAuthIdentifier()
    {
        return $this->phone;
    }

    /**
     * Generate API key for user
     */
    public static function generateApiKey($userId)
    {
        $randomString = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 50);
        return $randomString . time() . $userId;
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get referral code from phone or use existing referral_code
     */
    public function getReferralCodeAttribute()
    {
        return $this->attributes['referral_code'] ?? substr($this->phone, -6);
    }

    /**
     * Account type constants - matching existing system
     */
    const TYPE_USER = 1;
    const TYPE_AGENT = 2;
    const TYPE_VENDOR = 3;

    /**
     * Registration status constants - matching existing system
     */
    const REG_STATUS_ACTIVE = 0;
    const REG_STATUS_PENDING = 1;
    const REG_STATUS_BLOCKED = 2;
    const REG_STATUS_UNVERIFIED = 3;

    /**
     * Get account type name
     */
    public function getAccountTypeNameAttribute()
    {
        // Handle both string and integer values
        if (is_string($this->user_type)) {
            return match(strtolower($this->user_type)) {
                'user' => 'User',
                'agent' => 'Agent', 
                'vendor' => 'Vendor',
                default => ucfirst($this->user_type)
            };
        }
        
        return match($this->user_type) {
            self::TYPE_USER => 'User',
            self::TYPE_AGENT => 'Agent',
            self::TYPE_VENDOR => 'Vendor',
            default => 'User'
        };
    }

    /**
     * Get registration status name
     */
    public function getRegistrationStatusNameAttribute()
    {
        // Handle both string and integer values
        if (is_string($this->reg_status)) {
            return match(strtolower($this->reg_status)) {
                'active' => 'Active',
                'pending' => 'Pending',
                'blocked' => 'Blocked',
                'unverified' => 'Unverified',
                default => ucfirst($this->reg_status)
            };
        }
        
        return match($this->reg_status) {
            self::REG_STATUS_ACTIVE => 'Active',
            self::REG_STATUS_PENDING => 'Pending',
            self::REG_STATUS_BLOCKED => 'Blocked',
            self::REG_STATUS_UNVERIFIED => 'Unverified',
            default => 'Active'
        };
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        if (is_string($this->reg_status)) {
            return strtolower($this->reg_status) === 'active';
        }
        return $this->reg_status === self::REG_STATUS_ACTIVE;
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked()
    {
        if (is_string($this->reg_status)) {
            return strtolower($this->reg_status) === 'blocked';
        }
        return $this->reg_status === self::REG_STATUS_BLOCKED;
    }

    /**
     * Check if user can upgrade to agent
     */
    public function canUpgradeToAgent()
    {
        return $this->user_type === self::TYPE_USER && $this->isActive();
    }

    /**
     * Check if user can upgrade to vendor
     */
    public function canUpgradeToVendor()
    {
        return in_array($this->user_type, [self::TYPE_USER, self::TYPE_AGENT]) && $this->isActive();
    }

    /**
     * Get upgrade cost for account type
     */
    public function getUpgradeCost($targetType)
    {
        $siteSettings = \App\Models\SiteSettings::first();

        return match($targetType) {
            self::TYPE_AGENT => $siteSettings?->agentupgrade ?? 500,
            self::TYPE_VENDOR => $siteSettings?->vendorupgrade ?? 1000,
            default => 0
        };
    }

    /**
     * Upgrade user account type
     */
    public function upgradeAccountType($targetType, $transactionPin = null)
    {
        // Validate PIN if required
        if ($this->pin_status == 1 && $transactionPin) {
            if ($this->transaction_pin != $transactionPin) {
                return ['status' => 'error', 'message' => 'Invalid transaction PIN'];
            }
        }

        // Check if upgrade is allowed
        if ($targetType == self::TYPE_AGENT && !$this->canUpgradeToAgent()) {
            return ['status' => 'error', 'message' => 'Cannot upgrade to Agent'];
        }

        if ($targetType == self::TYPE_VENDOR && !$this->canUpgradeToVendor()) {
            return ['status' => 'error', 'message' => 'Cannot upgrade to Vendor'];
        }

        // Check wallet balance
        $upgradeCost = $this->getUpgradeCost($targetType);
        if ($this->wallet_balance < $upgradeCost) {
            return ['status' => 'error', 'message' => 'Insufficient wallet balance'];
        }

        // Debit wallet and upgrade
        $this->wallet_balance -= $upgradeCost;
        $this->user_type = $targetType;
        $this->save();

        // Record transaction
        $this->recordUpgradeTransaction($targetType, $upgradeCost);

        return ['status' => 'success', 'message' => 'Account upgraded successfully'];
    }

    /**
     * Record upgrade transaction
     */
    private function recordUpgradeTransaction($targetType, $amount)
    {
        $typeName = match($targetType) {
            self::TYPE_AGENT => 'Agent',
            self::TYPE_VENDOR => 'Vendor',
            default => 'Unknown'
        };

        \App\Models\Transaction::create([
            'user_id' => $this->id,
            'reference' => 'UP' . time() . $this->id,
            'service_name' => 'Account Upgrade',
            'description' => "Upgrade to {$typeName} Account",
            'amount' => $amount,
            'old_balance' => $this->wallet_balance + $amount,
            'new_balance' => $this->wallet_balance,
            'status' => 'completed',
            'created_at' => now(),
        ]);
    }

    /**
     * Get discount rate based on user type
     */
    public function getDiscountRate($serviceType = 'general')
    {
        return match($this->user_type) {
            self::TYPE_USER => 1.0,
            self::TYPE_AGENT => 0.98, // 2% discount
            self::TYPE_VENDOR => 0.97, // 3% discount
            default => 1.0
        };
    }

    /**
     * Calculate service price with user discount
     */
    public function calculateServicePrice($basePrice, $serviceType = 'general')
    {
        return $basePrice * $this->getDiscountRate($serviceType);
    }

    /**
     * Get transactions belonging to this user
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'sId', 'id');
    }

    /**
     * Get user verifications
     */
    public function verifications()
    {
        return $this->hasMany(\App\Models\UserVerification::class, 'user_id', 'id');
    }

    /**
     * Get user devices
     */
    public function devices()
    {
        return $this->hasMany(\App\Models\UserDevice::class, 'user_id', 'id');
    }

    /**
     * Get referrals made by this user
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by', 'phone');
    }

    /**
     * Get the user who referred this user
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by', 'phone');
    }

    /**
     * Get total referral earnings
     */
    public function getTotalReferralEarnings()
    {
        return $this->referral_wallet;
    }

    /**
     * Credit referral bonus
     */
    public function creditReferralBonus($amount, $service, $transactionRef)
    {
        $this->referral_wallet += $amount;
        $this->save();

        // Record referral bonus
        \App\Models\ReferralBonus::create([
            'referrer_id' => $this->id,
            'service_type' => $service,
            'bonus_amount' => $amount,
            'transaction_ref' => $transactionRef,
            'paid' => true,
        ]);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('reg_status', self::REG_STATUS_ACTIVE);
    }
}
