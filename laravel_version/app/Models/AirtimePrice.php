<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirtimePrice extends Model
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
     * Get discount based on user type
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
     * Calculate final amount after discount
     */
    public function calculateFinalAmount($amount, $userType)
    {
        $discount = $this->getDiscountForUserType($userType);
        return $amount * ($discount / 100);
    }
}
