<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPin extends Model
{
    use HasFactory;

    protected $table = 'datapinprice';
    protected $primaryKey = 'aId';
    public $timestamps = false;

    protected $fillable = [
        'aNetwork',
        'aPlanId',
        'aPlanName',
        'aUserDiscount',
        'aAgentDiscount',
        'aVendorDiscount',
        'aAmount'
    ];

    protected $casts = [
        'aUserDiscount' => 'decimal:2',
        'aAgentDiscount' => 'decimal:2',
        'aVendorDiscount' => 'decimal:2',
        'aAmount' => 'decimal:2'
    ];

    /**
     * Get network relationship
     */
    public function network()
    {
        return $this->belongsTo(NetworkId::class, 'aNetwork', 'nId');
    }

    /**
     * Get all data pin plans
     */
    public static function getAllPlans()
    {
        return static::with('network')->get();
    }

    /**
     * Get plans by network
     */
    public static function getByNetwork($networkId)
    {
        return static::where('aNetwork', $networkId)->get();
    }

    /**
     * Get specific plan by network and plan ID
     */
    public static function getByNetworkAndPlan($networkId, $planId)
    {
        return static::where('aNetwork', $networkId)
                    ->where('aPlanId', $planId)
                    ->first();
    }

    /**
     * Get available plans for a network
     */
    public static function getAvailablePlans($networkId)
    {
        return static::where('aNetwork', $networkId)
                    ->select('aPlanId', 'aPlanName', 'aAmount')
                    ->get()
                    ->map(function ($plan) {
                        return [
                            'plan_id' => $plan->aPlanId,
                            'plan_name' => $plan->aPlanName,
                            'amount' => $plan->aAmount
                        ];
                    });
    }

    /**
     * Check if data pin is available for network
     */
    public function isAvailable()
    {
        return true; // Default to true, can be extended with status checking
    }

    /**
     * Calculate amount to pay based on user type
     */
    public function calculateAmountToPay($userType)
    {
        $discount = 0;

        switch (strtolower($userType)) {
            case 'user':
                $discount = $this->aUserDiscount ?? 0;
                break;
            case 'agent':
                $discount = $this->aAgentDiscount ?? 0;
                break;
            case 'vendor':
                $discount = $this->aVendorDiscount ?? 0;
                break;
        }

        return $this->aAmount - ($this->aAmount * ($discount / 100));
    }

    /**
     * Calculate profit based on user type
     */
    public function calculateProfit($userType)
    {
        $amountToPay = $this->calculateAmountToPay($userType);
        return $this->aAmount - $amountToPay;
    }

    /**
     * Get data pin price by network and plan
     */
    public static function getPrice($networkId, $planId)
    {
        $plan = static::getByNetworkAndPlan($networkId, $planId);
        return $plan ? $plan->aAmount : 0;
    }

    /**
     * Get discount information for user type
     */
    public function getDiscountInfo($userType)
    {
        $discount = 0;

        switch (strtolower($userType)) {
            case 'user':
                $discount = $this->aUserDiscount ?? 0;
                break;
            case 'agent':
                $discount = $this->aAgentDiscount ?? 0;
                break;
            case 'vendor':
                $discount = $this->aVendorDiscount ?? 0;
                break;
        }

        return [
            'original_amount' => $this->aAmount,
            'discount_percentage' => $discount,
            'discount_amount' => $this->aAmount * ($discount / 100),
            'final_amount' => $this->calculateAmountToPay($userType)
        ];
    }
}
