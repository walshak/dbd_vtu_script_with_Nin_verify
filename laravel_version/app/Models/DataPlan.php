<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPlan extends Model
{
    use HasFactory;

    protected $table = 'data_plans';
    protected $primaryKey = 'dId';
    public $timestamps = false;

    protected $fillable = [
        'nId',
        'dPlan',
        'dGroup',
        'userPrice',
        'agentPrice',
        'apiPrice',
        'dAmount',
        'dValidity',
        'dPlanId',
        'cost_price',
        'selling_price',
        'profit_margin',
        'uzobest_plan_id',
    ];

    protected $casts = [
        'userPrice' => 'float',
        'agentPrice' => 'float',
        'apiPrice' => 'float',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'profit_margin' => 'float',
    ];

    /**
     * Get network details
     */
    public function network()
    {
        return $this->belongsTo(NetworkId::class, 'nId', 'nId');
    }

    /**
     * Get price based on user type
     * Now simplified to use unified selling_price for all users
     */
    public function getPriceForUserType($userType = null)
    {
        // Use selling_price if available, fallback to userPrice for backwards compatibility
        return $this->selling_price ?? $this->userPrice;
    }

    /**
     * Get data plans by network and group
     */
    public static function getByNetworkAndGroup($networkId, $group = 'SME')
    {
        return static::where('nId', $networkId)
            ->where('dGroup', $group)
            ->orderBy('userPrice', 'asc')
            ->get();
    }

    /**
     * Get plan by network and plan ID
     */
    public static function getByNetworkAndPlanId($networkId, $planId)
    {
        return static::where('nId', $networkId)
            ->where('dPlanId', $planId)
            ->first();
    }

    /**
     * Format plan display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->dPlan . ' (' . $this->dValidity . ')';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->dAmount, 0) . 'MB';
    }

    /**
     * Calculate final amount after discount
     */
    public function calculateFinalAmount($userType)
    {
        return $this->getPriceForUserType($userType);
    }

    /**
     * Calculate profit from transaction
     * Uses cost_price (from Uzobest) vs selling_price
     */
    public function calculateProfit($finalAmount = null)
    {
        // Use stored profit_margin if available, otherwise calculate
        if ($this->profit_margin !== null) {
            return $this->profit_margin;
        }

        // Calculate: selling price - cost price
        $cost = $this->cost_price ?? $this->dAmount ?? $this->apiPrice;
        $selling = $finalAmount ?? $this->selling_price ?? $this->userPrice;
        return max(0, $selling - $cost);
    }

    /**
     * Get plan description for transactions
     */
    public function getsPlanAttribute()
    {
        return $this->dPlan;
    }

    /**
     * Get plan ID for API calls
     */
    public function getPlanIdAttribute()
    {
        return $this->dPlanId;
    }
}
