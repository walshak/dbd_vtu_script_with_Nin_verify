<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityProvider extends Model
{
    use HasFactory;

    protected $table = 'electricity';
    protected $primaryKey = 'eId';
    public $timestamps = false;

    protected $fillable = [
        'ePlan',
        'eId',
        'ePrice',
        'eBuyingPrice',
        'eStatus',
        'cost_price',
        'selling_price',
        'profit_margin',
        'uzobest_disco_id',
    ];

    protected $casts = [
        'ePrice' => 'decimal:2',
        'eBuyingPrice' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'eStatus' => 'integer'
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Scope for active providers
     */
    public function scopeActive($query)
    {
        return $query->where('eStatus', self::STATUS_ACTIVE);
    }

    /**
     * Get all active electricity providers
     */
    public static function getActiveProviders()
    {
        return static::active()->orderBy('ePlan')->get();
    }

    /**
     * Get provider by plan name
     */
    public static function getByPlan($planName)
    {
        return static::where('ePlan', $planName)->first();
    }

    /**
     * Get provider by external ID
     */
    public static function getByExternalId($externalId)
    {
        return static::where('eProviderId', $externalId)->first();
    }

    /**
     * Calculate user price based on account type
     * Now simplified to use unified selling_price for all users
     */
    public function getUserPrice($accountType = 'user')
    {
        // Use selling_price if available, fallback to ePrice for backwards compatibility
        return $this->selling_price ?? $this->ePrice;
    }

    /**
     * Calculate profit for transaction
     */
    public function calculateProfit($amount, $accountType = 'user')
    {
        // Use stored profit_margin if available
        if ($this->profit_margin !== null) {
            $profitPerUnit = $this->profit_margin;
        } else {
            // Calculate: selling price - cost price
            $userPrice = $this->getUserPrice($accountType);
            $buyingPrice = $this->cost_price ?? $this->eBuyingPrice;
            $profitPerUnit = $userPrice - $buyingPrice;
        }

        // Calculate profit based on amount
        return ($profitPerUnit / 100) * $amount; // Amount is in currency units
    }

    /**
     * Check if provider is active
     */
    public function isActive()
    {
        return $this->eStatus == self::STATUS_ACTIVE;
    }

    /**
     * Toggle provider status
     */
    public function toggleStatus()
    {
        $this->eStatus = $this->eStatus == self::STATUS_ACTIVE ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;
        return $this->save();
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        return $this->eStatus == self::STATUS_ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->eStatus == self::STATUS_ACTIVE ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Get formatted plan name
     */
    public function getFormattedPlanAttribute()
    {
        return strtoupper($this->ePlan);
    }

    /**
     * Get provider logo
     */
    public function getLogoPathAttribute()
    {
        $logos = [
            'aedc' => '/assets/images/aedc-logo.png',
            'ekedc' => '/assets/images/ekedc-logo.png',
            'ikedc' => '/assets/images/ikedc-logo.png',
            'kedco' => '/assets/images/kedco-logo.png',
            'phed' => '/assets/images/phed-logo.png',
            'phcn' => '/assets/images/phcn-logo.png'
        ];

        $providerKey = strtolower(str_replace(' ', '', $this->ePlan));
        return $logos[$providerKey] ?? '/assets/images/electricity-default.png';
    }

    /**
     * Validate meter number format
     */
    public static function validateMeterNumber($meterNumber, $providerPlan = null)
    {
        // Remove spaces and special characters
        $meterNumber = preg_replace('/[^0-9]/', '', $meterNumber);

        // Basic validation - meter numbers are usually 11 digits
        if (strlen($meterNumber) < 10 || strlen($meterNumber) > 12) {
            return false;
        }

        // Additional provider-specific validations can be added here
        return true;
    }

    /**
     * Get service charges
     */
    public function getServiceCharges()
    {
        // Get charges from site settings
        $siteSettings = SiteSettings::getSiteSettings();
        return $siteSettings->electricitycharges ?? 50; // Default charge
    }
}
