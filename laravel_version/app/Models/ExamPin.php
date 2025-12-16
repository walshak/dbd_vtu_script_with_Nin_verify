<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPin extends Model
{
    use HasFactory;

    protected $table = 'exampin';
    protected $primaryKey = 'eId';
    public $timestamps = false;

    protected $fillable = [
        'ePlan',
        'eId',
        'ePrice',
        'eBuyingPrice',
        'eStatus'
    ];

    protected $casts = [
        'ePrice' => 'decimal:2',
        'eBuyingPrice' => 'decimal:2',
        'eStatus' => 'integer'
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Scope for active exam providers
     */
    public function scopeActive($query)
    {
        return $query->where('eStatus', self::STATUS_ACTIVE);
    }

    /**
     * Get all active exam providers
     */
    public static function getActiveExamProviders()
    {
        return static::active()->orderBy('ePlan')->get();
    }

    /**
     * Get exam provider by plan name
     */
    public static function getByPlan($planName)
    {
        return static::where('ePlan', $planName)->first();
    }

    /**
     * Get exam provider by external ID
     */
    public static function getByExternalId($externalId)
    {
        return static::where('eProviderId', $externalId)->first();
    }

    /**
     * Get all active exam types (alias for getActiveExamProviders)
     */
    public static function getActiveExamTypes()
    {
        return static::getActiveExamProviders();
    }

    /**
     * Get exam provider by exam type (alias for getByPlan)
     */
    public static function getByExamType($examType)
    {
        return static::getByPlan($examType);
    }

    /**
     * Calculate user price based on account type
     */
    public function getUserPrice($accountType = 'user')
    {
        $basePrice = $this->ePrice;

        // Apply discounts based on account type
        switch (strtolower($accountType)) {
            case 'agent':
                return $basePrice * 0.99; // 1% discount
            case 'vendor':
                return $basePrice * 0.98; // 2% discount
            default:
                return $basePrice;
        }
    }

    /**
     * Calculate profit for transaction
     */
    public function calculateProfit($accountType = 'user')
    {
        $userPrice = $this->getUserPrice($accountType);
        $buyingPrice = $this->eBuyingPrice;

        return $userPrice - $buyingPrice;
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
     * Get exam board logo
     */
    public function getLogoPathAttribute()
    {
        $logos = [
            'waec' => '/assets/images/waec-logo.png',
            'neco' => '/assets/images/neco-logo.png',
            'jamb' => '/assets/images/jamb-logo.png',
            'nabteb' => '/assets/images/nabteb-logo.png',
            'nbte' => '/assets/images/nbte-logo.png'
        ];

        $examKey = strtolower(str_replace(' ', '', $this->ePlan));
        return $logos[$examKey] ?? '/assets/images/exam-default.png';
    }

    /**
     * Get exam categories
     */
    public static function getExamCategories()
    {
        return [
            'waec' => 'West African Examinations Council',
            'neco' => 'National Examinations Council',
            'jamb' => 'Joint Admissions and Matriculation Board',
            'nabteb' => 'National Business and Technical Examinations Board',
            'nbte' => 'National Board for Technical Education'
        ];
    }

    /**
     * Get exam description
     */
    public function getDescriptionAttribute()
    {
        $categories = self::getExamCategories();
        $examKey = strtolower(str_replace(' ', '', $this->ePlan));

        return $categories[$examKey] ?? 'Educational Examination Pin';
    }

    /**
     * Check if exam supports quantity purchase
     */
    public function supportsQuantityPurchase()
    {
        // Most exam pins support quantity purchases
        return true;
    }

    /**
     * Get maximum quantity allowed per transaction
     */
    public function getMaxQuantityPerTransaction()
    {
        // Default maximum quantity
        return 50;
    }

    /**
     * Validate quantity for purchase
     */
    public function validateQuantity($quantity)
    {
        if ($quantity < 1) {
            return false;
        }

        if ($quantity > $this->getMaxQuantityPerTransaction()) {
            return false;
        }

        return true;
    }
}
