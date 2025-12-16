<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airtime extends Model
{
    use HasFactory;

    protected $table = 'airtimes';
    protected $primaryKey = 'aId';
    public $timestamps = false;

    protected $fillable = [
        'aNetwork',
        'aBuyDiscount',
        'aUserDiscount',
        'aAgentDiscount',
        'aVendorDiscount',
        'aType'
    ];

    protected $casts = [
        'aBuyDiscount' => 'float',
        'aUserDiscount' => 'float',
        'aAgentDiscount' => 'float',
        'aVendorDiscount' => 'float',
    ];

    /**
     * Get network details
     */
    public function network()
    {
        return $this->belongsTo(NetworkId::class, 'aNetwork', 'nId');
    }

    /**
     * Get discount rate based on user type
     */
    public function getDiscountForUserType($userType)
    {
        switch ($userType) {
            case 1: // Regular User
                return $this->aUserDiscount;
            case 2: // Agent
                return $this->aAgentDiscount;
            case 3: // Vendor
                return $this->aVendorDiscount;
            default:
                return $this->aUserDiscount;
        }
    }

    /**
     * Get discount rate for specific airtime type and user type
     */
    public function getDiscount($userType, $airtimeType = 'VTU')
    {
        return $this->getDiscountForUserType($userType);
    }

    /**
     * Calculate final amount after discount
     */
    public function calculateFinalAmount($amount, $userType)
    {
        $discountRate = $this->getDiscountForUserType($userType);
        return ($amount * $discountRate) / 100;
    }

    /**
     * Get airtime plans by network and type
     */
    public static function getByNetworkAndType($networkId, $type = 'VTU')
    {
        return static::where('aNetwork', $networkId)
                    ->where('aType', $type)
                    ->first();
    }
}
