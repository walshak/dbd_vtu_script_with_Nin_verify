<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlphaTopup extends Model
{
    use HasFactory;

    protected $table = 'alphatopupprice';
    protected $primaryKey = 'alphaId';
    public $timestamps = true;

    protected $fillable = [
        'buyingPrice',
        'sellingPrice',
        'agent',
        'vendor'
    ];

    protected $casts = [
        'buyingPrice' => 'decimal:2',
        'sellingPrice' => 'decimal:2',
        'agent' => 'decimal:2',
        'vendor' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const CREATED_AT = 'dPosted';
    const UPDATED_AT = null; // No updated_at in original table

    /**
     * Get all active alpha topup plans
     */
    public static function getActivePlans()
    {
        return static::orderBy('sellingPrice')->get();
    }

    /**
     * Get plan by selling price
     */
    public static function getBySellingPrice($price)
    {
        return static::where('sellingPrice', $price)->first();
    }

    /**
     * Get plan by buying price
     */
    public static function getByBuyingPrice($price)
    {
        return static::where('buyingPrice', $price)->first();
    }

    /**
     * Calculate user price based on account type
     */
    public function getUserPrice($accountType = 'user')
    {
        switch (strtolower($accountType)) {
            case 'agent':
                return $this->agent;
            case 'vendor':
                return $this->vendor;
            default:
                return $this->sellingPrice;
        }
    }

    /**
     * Calculate profit for transaction
     */
    public function calculateProfit($accountType = 'user')
    {
        $userPrice = $this->getUserPrice($accountType);
        return $userPrice - $this->buyingPrice;
    }

    /**
     * Get discount percentage for account type
     */
    public function getDiscountPercentage($accountType = 'user')
    {
        $userPrice = $this->getUserPrice($accountType);
        $originalPrice = $this->sellingPrice;

        if ($originalPrice == 0) {
            return 0;
        }

        $discount = (($originalPrice - $userPrice) / $originalPrice) * 100;
        return round($discount, 2);
    }

    /**
     * Check if plan offers discount for account type
     */
    public function hasDiscount($accountType = 'user')
    {
        return $this->getUserPrice($accountType) < $this->sellingPrice;
    }

    /**
     * Get formatted selling price
     */
    public function getFormattedSellingPriceAttribute()
    {
        return '₦' . number_format($this->sellingPrice, 2);
    }

    /**
     * Get formatted buying price
     */
    public function getFormattedBuyingPriceAttribute()
    {
        return '₦' . number_format($this->buyingPrice, 2);
    }

    /**
     * Get formatted agent price
     */
    public function getFormattedAgentPriceAttribute()
    {
        return '₦' . number_format($this->agent, 2);
    }

    /**
     * Get formatted vendor price
     */
    public function getFormattedVendorPriceAttribute()
    {
        return '₦' . number_format($this->vendor, 2);
    }

    /**
     * Get plan description
     */
    public function getDescriptionAttribute()
    {
        return "Alpha TopUp of ₦{$this->sellingPrice}";
    }

    /**
     * Get plan benefits or features
     */
    public function getBenefitsAttribute()
    {
        return [
            'Instant delivery',
            'No expiry date',
            'Can be used for any transaction',
            'Transferable to other users'
        ];
    }

    /**
     * Validate alpha topup amount
     */
    public static function validateAmount($amount)
    {
        // Check if amount exists in alpha topup plans
        return static::where('sellingPrice', $amount)->exists();
    }

    /**
     * Get available denominations
     */
    public static function getAvailableDenominations()
    {
        return static::orderBy('sellingPrice')
            ->pluck('sellingPrice')
            ->toArray();
    }

    /**
     * Get plan summary for display
     */
    public function getSummary($accountType = 'user')
    {
        $userPrice = $this->getUserPrice($accountType);
        $discount = $this->getDiscountPercentage($accountType);

        return [
            'plan_id' => $this->alphaId,
            'amount' => $this->sellingPrice,
            'user_price' => $userPrice,
            'discount_percentage' => $discount,
            'has_discount' => $this->hasDiscount($accountType),
            'description' => $this->description,
            'benefits' => $this->benefits
        ];
    }

    /**
     * Check if alpha topup service is available
     */
    public static function isServiceAvailable()
    {
        // Check if there are any active plans
        return static::count() > 0;
    }
}
