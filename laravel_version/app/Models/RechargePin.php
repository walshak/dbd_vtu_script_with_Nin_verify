<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargePin extends Model
{
    use HasFactory;

    protected $table = 'airtimepinprice';
    protected $primaryKey = 'aId';
    public $timestamps = false;

    protected $fillable = [
        'aNetwork',
        'aUserDiscount',
        'aAgentDiscount',
        'aVendorDiscount'
    ];

    protected $casts = [
        'aUserDiscount' => 'decimal:2',
        'aAgentDiscount' => 'decimal:2',
        'aVendorDiscount' => 'decimal:2'
    ];

    /**
     * Get network relationship
     */
    public function network()
    {
        return $this->belongsTo(NetworkId::class, 'aNetwork', 'nId');
    }

    /**
     * Get all recharge pin discounts
     */
    public static function getAllDiscounts()
    {
        return static::with('network')->get();
    }

    /**
     * Get discount by network
     */
    public static function getByNetwork($networkId)
    {
        return static::where('aNetwork', $networkId)->first();
    }

    /**
     * Calculate user price based on account type
     */
    public function getUserDiscount($accountType = 'user')
    {
        switch (strtolower($accountType)) {
            case 'agent':
                return $this->aAgentDiscount;
            case 'vendor':
                return $this->aVendorDiscount;
            default:
                return $this->aUserDiscount;
        }
    }

    /**
     * Calculate amount to pay for recharge pin
     */
    public function calculateAmountToPay($originalAmount, $accountType = 'user')
    {
        $discount = $this->getUserDiscount($accountType);
        return ($originalAmount * $discount) / 100;
    }

    /**
     * Get available denominations for recharge pins
     */
    public static function getAvailableDenominations()
    {
        return [100, 200, 400, 500, 750, 1000, 1500, 2000, 2500, 3000, 4000, 5000];
    }

    /**
     * Validate recharge pin amount
     */
    public static function validateAmount($amount)
    {
        $denominations = self::getAvailableDenominations();
        return in_array($amount, $denominations);
    }

    /**
     * Get network name
     */
    public function getNetworkNameAttribute()
    {
        return $this->network ? $this->network->network : 'Unknown';
    }

    /**
     * Get formatted discount for account type
     */
    public function getFormattedDiscount($accountType = 'user')
    {
        $discount = $this->getUserDiscount($accountType);
        return number_format($discount, 1) . '%';
    }

    /**
     * Check if recharge pin is available for network
     */
    public static function isAvailableForNetwork($networkId)
    {
        return static::where('aNetwork', $networkId)->exists();
    }

    /**
     * Get recharge pin pricing summary
     */
    public function getPricingSummary($amount, $accountType = 'user')
    {
        $amountToPay = $this->calculateAmountToPay($amount, $accountType);
        $discount = $this->getUserDiscount($accountType);
        $savings = $amount - $amountToPay;

        return [
            'original_amount' => $amount,
            'amount_to_pay' => $amountToPay,
            'discount_percentage' => $discount,
            'savings' => $savings,
            'network' => $this->network_name
        ];
    }

    /**
     * Generate recharge pin tokens (mock implementation)
     */
    public static function generatePinTokens($networkId, $amount, $quantity = 1)
    {
        $tokens = [];

        for ($i = 0; $i < $quantity; $i++) {
            $tokens[] = [
                'pin' => self::generatePin(),
                'serial' => self::generateSerial(),
                'load_pin' => self::getLoadPin($networkId),
                'amount' => $amount,
                'network' => $networkId
            ];
        }

        return $tokens;
    }

    /**
     * Generate random PIN
     */
    private static function generatePin()
    {
        return sprintf('%04d%04d%04d%04d',
            rand(1000, 9999),
            rand(1000, 9999),
            rand(1000, 9999),
            rand(1000, 9999)
        );
    }

    /**
     * Generate random serial number
     */
    private static function generateSerial()
    {
        return sprintf('%010d_%02d',
            rand(1000000000, 9999999999),
            rand(10, 99)
        );
    }

    /**
     * Get load PIN code for network
     */
    private static function getLoadPin($networkId)
    {
        $loadPins = [
            '1' => '*555*PIN#', // MTN
            '2' => '*126*PIN#', // GLO
            '3' => '*126*PIN#', // Airtel
            '4' => '*222*PIN#'  // 9mobile
        ];

        return $loadPins[$networkId] ?? '*555*PIN#';
    }
}
