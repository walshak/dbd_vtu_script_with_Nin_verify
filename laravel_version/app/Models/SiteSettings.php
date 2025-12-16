<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    use HasFactory;

    protected $table = 'sitesettings';
    protected $primaryKey = 'sId';
    public $timestamps = false;

    protected $fillable = [
        'sitename',
        'siteurl',
        'apidocumentation',
        'referalupgradebonus',
        'referalairtimebonus',
        'referaldatabonus',
        'referalwalletbonus',
        'referalcablebonus',
        'referalexambonus',
        'referalmeterbonus',
        'wallettowalletcharges',
        'agentupgrade',
        'vendorupgrade',
        'accountname',
        'accountno',
        'bankname',
        'electricitycharges',
        'airtimemin',
        'airtimemax',
        'sitecolor',
        'loginstyle',
        'homestyle'
    ];

    protected $casts = [
        'referalupgradebonus' => 'decimal:2',
        'referalairtimebonus' => 'decimal:2',
        'referaldatabonus' => 'decimal:2',
        'referalwalletbonus' => 'decimal:2',
        'referalcablebonus' => 'decimal:2',
        'referalexambonus' => 'decimal:2',
        'referalmeterbonus' => 'decimal:2',
        'wallettowalletcharges' => 'decimal:2',
        'agentupgrade' => 'decimal:2',
        'vendorupgrade' => 'decimal:2',
        'electricitycharges' => 'decimal:2',
        'airtimemin' => 'integer',
        'airtimemax' => 'integer'
    ];

    /**
     * Get site settings instance
     */
    public static function getSiteSettings()
    {
        return static::first() ?? new static();
    }

    /**
     * Get specific setting value
     */
    public static function getSetting($key, $default = null)
    {
        $settings = static::getSiteSettings();
        return $settings->$key ?? $default;
    }

    /**
     * Update setting value
     */
    public static function updateSetting($key, $value)
    {
        $settings = static::getSiteSettings();
        $settings->$key = $value;
        return $settings->save();
    }

    /**
     * Get referral bonus for service
     */
    public function getReferralBonus($serviceType)
    {
        $bonusMap = [
            'airtime' => 'referalairtimebonus',
            'data' => 'referaldatabonus',
            'cable' => 'referalcablebonus',
            'electricity' => 'referalmeterbonus',
            'exam' => 'referalexambonus',
            'wallet' => 'referalwalletbonus',
            'upgrade' => 'referalupgradebonus'
        ];

        $field = $bonusMap[strtolower($serviceType)] ?? 'referalwalletbonus';
        return $this->$field ?? 0;
    }

    /**
     * Get account upgrade fee
     */
    public function getUpgradeFee($accountType)
    {
        switch (strtolower($accountType)) {
            case 'agent':
                return $this->agentupgrade ?? 0;
            case 'vendor':
                return $this->vendorupgrade ?? 0;
            default:
                return 0;
        }
    }

    /**
     * Get bank details for transfers
     */
    public function getBankDetails()
    {
        return [
            'account_name' => $this->accountname,
            'account_number' => $this->accountno,
            'bank_name' => $this->bankname
        ];
    }

    /**
     * Get airtime limits
     */
    public function getAirtimeLimits()
    {
        return [
            'min' => $this->airtimemin ?? 50,
            'max' => $this->airtimemax ?? 10000
        ];
    }

    /**
     * Get site theme settings
     */
    public function getThemeSettings()
    {
        return [
            'color' => $this->sitecolor ?? 'blue',
            'login_style' => $this->loginstyle ?? 'default',
            'home_style' => $this->homestyle ?? 'default'
        ];
    }
}
