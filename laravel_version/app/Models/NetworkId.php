<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkId extends Model
{
    use HasFactory;

    protected $table = 'network_ids';
    protected $primaryKey = 'nId';
    public $timestamps = false;

    protected $fillable = [
        'network',
        'networkid',
        'smeId',
        'giftingId',
        'corporateId',
        'airtimeId',
        'status',
        'networkStatus',
        'vtuStatus',
        'sharesellStatus',
        'smeStatus',
        'giftingStatus',
        'corporateStatus',
        'airtimepinStatus',
        'datapinStatus'
    ];

    /**
     * Get airtime plans for this network
     */
    public function airtimePlans()
    {
        return $this->hasMany(Airtime::class, 'aNetwork', 'nId');
    }

    /**
     * Get data plans for this network
     */
    public function dataPlans()
    {
        return $this->hasMany(DataPlan::class, 'dNetwork', 'nId');
    }

    /**
     * Get network by name
     */
    public static function getByName($name)
    {
        return static::where('network', strtolower($name))->first();
    }

    /**
     * Get all active networks
     */
    public static function getAllActive()
    {
        return static::whereNotNull('network')->get();
    }

    /**
     * Get supported networks for airtime/data services
     */
    public static function getSupportedNetworks()
    {
        return static::where('networkStatus', 'On')->get();
    }

    /**
     * Get network ID for specific service type
     */
    public function getServiceId($serviceType)
    {
        switch (strtolower($serviceType)) {
            case 'vtu':
            case 'airtime':
                return $this->airtimeId;
            case 'sharesell':
            case 'share_and_sell':
                return $this->airtimeId; // Use airtimeId as fallback for sharesell
            case 'sme':
                return $this->smeId;
            case 'gifting':
                return $this->giftingId;
            case 'corporate':
                return $this->corporateId;
            default:
                return $this->airtimeId;
        }
    }

    /**
     * Check if network supports service type
     */
    public function supportsService($serviceType)
    {
        $serviceId = $this->getServiceId($serviceType);
        return !empty($serviceId) && $serviceId !== '0';
    }

    /**
     * Check if a service is enabled for this network
     */
    public function isServiceEnabled($serviceType)
    {
        switch (strtolower($serviceType)) {
            case 'general':
            case 'network':
                return $this->networkStatus === 'On';
            case 'vtu':
            case 'airtime_vtu':
                return $this->vtuStatus === 'On' && $this->networkStatus === 'On';
            case 'sharesell':
            case 'share_and_sell':
            case 'airtime_sharesell':
                return $this->sharesellStatus === 'On' && $this->networkStatus === 'On';
            case 'sme':
            case 'data_sme':
                return $this->smeStatus === 'On' && $this->networkStatus === 'On';
            case 'gifting':
            case 'data_gifting':
                return $this->giftingStatus === 'On' && $this->networkStatus === 'On';
            case 'corporate':
            case 'data_corporate':
                return $this->corporateStatus === 'On' && $this->networkStatus === 'On';
            case 'airtimepin':
            case 'recharge_card':
                return $this->airtimepinStatus === 'On' && $this->networkStatus === 'On';
            case 'datapin':
            case 'data_pin':
                return $this->datapinStatus === 'On' && $this->networkStatus === 'On';
            default:
                return $this->networkStatus === 'On';
        }
    }

    /**
     * Get available service types for this network
     */
    public function getAvailableServices()
    {
        $services = [];

        if ($this->isServiceEnabled('vtu')) {
            $services[] = 'vtu';
        }
        if ($this->isServiceEnabled('sharesell')) {
            $services[] = 'sharesell';
        }
        if ($this->isServiceEnabled('sme')) {
            $services[] = 'sme';
        }
        if ($this->isServiceEnabled('gifting')) {
            $services[] = 'gifting';
        }
        if ($this->isServiceEnabled('corporate')) {
            $services[] = 'corporate';
        }
        if ($this->isServiceEnabled('airtimepin')) {
            $services[] = 'airtimepin';
        }
        if ($this->isServiceEnabled('datapin')) {
            $services[] = 'datapin';
        }

        return $services;
    }

    /**
     * Get service status
     */
    public function getServiceStatus($serviceType)
    {
        switch (strtolower($serviceType)) {
            case 'general':
            case 'network':
                return $this->networkStatus;
            case 'vtu':
                return $this->vtuStatus;
            case 'sharesell':
                return $this->sharesellStatus;
            case 'sme':
                return $this->smeStatus;
            case 'gifting':
                return $this->giftingStatus;
            case 'corporate':
                return $this->corporateStatus;
            case 'airtimepin':
                return $this->airtimepinStatus;
            case 'datapin':
                return $this->datapinStatus;
            default:
                return 'Off';
        }
    }

    /**
     * Get formatted network name
     */
    public function getFormattedNameAttribute()
    {
        return strtoupper($this->network);
    }

    /**
     * Get network logo path
     */
    public function getLogoPathAttribute()
    {
        $logos = [
            'mtn' => '/assets/images/mtn-logo.png',
            'glo' => '/assets/images/glo-logo.png',
            'airtel' => '/assets/images/airtel-logo.png',
            '9mobile' => '/assets/images/9mobile-logo.png'
        ];

        return $logos[strtolower($this->network)] ?? '/assets/images/default-network.png';
    }

    /**
     * Get network color theme
     */
    public function getColorThemeAttribute()
    {
        $colors = [
            'mtn' => 'yellow',
            'glo' => 'green',
            'airtel' => 'red',
            '9mobile' => 'green'
        ];

        return $colors[strtolower($this->network)] ?? 'blue';
    }
}
