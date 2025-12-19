<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CablePlan extends Model
{
    use HasFactory;

    protected $table = 'cable_plans';
    protected $primaryKey = 'cpId';

    /**
     * Get the route key name for Laravel.
     */
    public function getRouteKeyName()
    {
        return 'cpId';
    }

    protected $fillable = [
        'name',
        'price',
        'userprice',
        'agentprice',
        'vendorprice',
        'planid',
        'type',
        'cableprovider',
        'day',
        'status',
        'cost_price',
        'selling_price',
        'profit_margin',
        'uzobest_cable_id',
        'uzobest_plan_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'userprice' => 'decimal:2',
        'agentprice' => 'decimal:2',
        'vendorprice' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'cableprovider' => 'integer',
        'day' => 'integer'
    ];

    /**
     * Get the cable provider for this plan
     */
    public function provider()
    {
        return $this->belongsTo(CableId::class, 'cableprovider', 'cId');
    }

    /**
     * Get cable plan by provider and plan ID
     */
    public static function getCablePlan($provider, $planId)
    {
        // If provider is a string (provider name), convert to ID
        if (!is_numeric($provider)) {
            $cableProvider = \App\Models\CableId::where('provider', strtolower($provider))->first();
            $provider = $cableProvider ? $cableProvider->cId : $provider;
        }

        return self::where('cableprovider', $provider)
                   ->where('cpId', $planId)
                   ->first();
    }

    /**
     * Get cable plans by provider
     */
    public static function getCablePlansByProvider($provider)
    {
        return self::where('cableprovider', $provider)
                   ->where('status', 'active')
                   ->get();
    }

    /**
     * Get cable plans by decoder/provider name (for cable TV service)
     */
    public static function getByDecoder($decoderName)
    {
        // First find the provider ID from cable_ids table by provider name
        $cableProvider = \App\Models\CableId::where('provider', strtolower($decoderName))->first();

        if (!$cableProvider) {
            return collect([]);
        }

        return self::where('cableprovider', $cableProvider->cId)
                   ->where('status', 'active')
                   ->get();
    }

    /**
     * Get price for user type
     * Now simplified to use unified selling_price for all users
     */
    public function getPriceForUserType($userType = null)
    {
        // Use selling_price if available, fallback to userprice for backwards compatibility
        return $this->selling_price ?? $this->userprice;
    }

    /**
     * Calculate final amount after any discounts (currently just returns selling price)
     * Added for consistency with DataPlan model
     */
    public function calculateFinalAmount($userType = null)
    {
        return $this->getPriceForUserType($userType);
    }

    /**
     * Calculate profit from transaction
     * Uses cost_price (from Uzobest/manual entry) vs selling_price
     */
    public function calculateProfit($finalAmount = null)
    {
        // Calculate: selling price - cost price
        $cost = floatval($this->cost_price ?? $this->price ?? 0);
        $selling = floatval($finalAmount ?? $this->selling_price ?? $this->userprice ?? 0);
        return max(0, $selling - $cost);
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific provider
     */
    public function scopeProvider($query, $providerId)
    {
        return $query->where('cableprovider', $providerId);
    }

    /**
     * Get formatted plan name with provider and duration
     */
    public function getFormattedNameAttribute()
    {
        $providerName = $this->provider ? $this->provider->provider : 'Unknown';
        return "{$this->name} ({$providerName}) ({$this->day} Days)";
    }

    /**
     * Get plan statistics
     */
    public static function getStatistics()
    {
        return [
            'total_plans' => self::count(),
            'active_plans' => self::where('status', 'active')->count(),
            'inactive_plans' => self::where('status', 'inactive')->count(),
            'providers_count' => self::distinct('cableprovider')->count(),
            'avg_user_price' => self::avg('userprice'),
            'min_price' => self::min('userprice'),
            'max_price' => self::max('userprice')
        ];
    }
}
