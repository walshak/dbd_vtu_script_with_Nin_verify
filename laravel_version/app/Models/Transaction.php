<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'tId';
    public $timestamps = false;

    protected $fillable = [
        'sId',
        'transref',
        'servicename',
        'servicedesc',
        'amount',
        'status',
        'oldbal',
        'newbal',
        'profit',
        'date'
    ];

    protected $casts = [
        'amount' => 'string',
        'oldbal' => 'string',
        'newbal' => 'string',
        'profit' => 'float',
        'status' => 'integer',
        'date' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'sId', 'id');
    }

    /**
     * Get transactions by user
     */
    public static function getByUser($userId, $limit = 50)
    {
        return static::where('sId', $userId)
                    ->orderBy('date', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get transactions by service
     */
    public static function getByService($servicename, $limit = 50)
    {
        return static::where('servicename', $servicename)
                    ->orderBy('date', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get transactions by status
     */
    public static function getByStatus($status, $limit = 50)
    {
        return static::where('status', $status)
                    ->orderBy('date', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Generate unique transaction reference compatible with old system
     */
    public static function generateReference($type = '')
    {
        $timestamp = time();
        $random = rand(10000, 99999);

        // If it's Monnify format for wallet topup
        if ($type === 'wallet_funding') {
            return 'MNFY|' . date('m') . '|' . date('Ymd') . date('His') . '|' . sprintf('%06d', $random);
        }

        // Standard format
        return $random . $timestamp;
    }

    /**
     * Record new transaction (compatible with old system)
     */
    public static function recordTransaction($data)
    {
        $transaction = new static();
        $transaction->sId = $data['user_id'];
        $transaction->transref = $data['reference'] ?? static::generateReference($data['type'] ?? '');
        $transaction->servicename = $data['servicename'];
        $transaction->servicedesc = $data['description'];
        $transaction->amount = (string) $data['amount'];
        $transaction->status = $data['status'] ?? 0; // 0 = success, 1 = failed in old system
        $transaction->oldbal = (string) ($data['old_balance'] ?? '0');
        $transaction->newbal = (string) ($data['new_balance'] ?? '0');
        $transaction->profit = $data['profit'] ?? 0;
        $transaction->date = now();

        $transaction->save();
        return $transaction;
    }

    /**
     * Update transaction status (compatible with old system)
     */
    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save();
        return $this;
    }

    /**
     * Get formatted transaction type for display
     */
    public function getFormattedServiceAttribute()
    {
        return ucfirst(str_replace(['_', '-'], ' ', $this->servicename));
    }

    /**
     * Get status text (compatible with old system logic)
     */
    public function getStatusTextAttribute()
    {
        return $this->status == 0 ? 'Success' : 'Failed';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->status == 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    }

    /**
     * VTU Service types (matching old system exactly)
     */
    const SERVICE_AIRTIME = 'Airtime';
    const SERVICE_DATA = 'Data';
    const SERVICE_CABLE_TV = 'Cable Tv';
    const SERVICE_ELECTRICITY = 'Electricity';
    const SERVICE_EXAM_PIN = 'Exam Pin';
    const SERVICE_RECHARGE_PIN = 'Recharge Pin';
    const SERVICE_DATA_PIN = 'Data Pin';
    const SERVICE_ALPHA_TOPUP = 'Alpha Topup';
    const SERVICE_WALLET_TOPUP = 'Wallet Topup';
    const SERVICE_WALLET_CREDIT = 'Wallet Credit';
    const SERVICE_WALLET_TRANSFER = 'Wallet Transfer';
    const SERVICE_TRANSACTION_REVERSAL = 'Transaction Reversal';

    /**
     * Transaction Type aliases (for backward compatibility with controller code)
     */
    const TYPE_AIRTIME = self::SERVICE_AIRTIME;
    const TYPE_DATA = self::SERVICE_DATA;
    const TYPE_CABLE_TV = self::SERVICE_CABLE_TV;
    const TYPE_ELECTRICITY = self::SERVICE_ELECTRICITY;
    const TYPE_EXAM_PIN = self::SERVICE_EXAM_PIN;
    const TYPE_RECHARGE_PIN = self::SERVICE_RECHARGE_PIN;
    const TYPE_DATA_PIN = self::SERVICE_DATA_PIN;
    const TYPE_ALPHA_TOPUP = self::SERVICE_ALPHA_TOPUP;
    const TYPE_WALLET_TOPUP = self::SERVICE_WALLET_TOPUP;
    const TYPE_WALLET_CREDIT = self::SERVICE_WALLET_CREDIT;
    const TYPE_WALLET_TRANSFER = self::SERVICE_WALLET_TRANSFER;

    /**
     * Network provider constants (matching old system)
     */
    const NETWORK_MTN = 'MTN';
    const NETWORK_GLO = 'GLO';
    const NETWORK_AIRTEL = 'AIRTEL';
    const NETWORK_9MOBILE = '9MOBILE';

    /**
     * Cable TV provider constants (matching old system)
     */
    const CABLE_DSTV = 'DSTV';
    const CABLE_GOTV = 'GOTV';
    const CABLE_STARTIMES = 'STARTIMES';

    /**
     * Electricity provider constants (matching old system)
     */
    const ELECTRICITY_AEDC = 'AEDC';
    const ELECTRICITY_EKEDC = 'EKEDC';
    const ELECTRICITY_IKEDC = 'IKEDC';
    const ELECTRICITY_KEDCO = 'KEDCO';

    /**
     * Transaction status constants (matching old system)
     */
    const STATUS_SUCCESS = 0;
    const STATUS_FAILED = 1;

    /**
     * Get sales analytics for admin dashboard (compatible with old system)
     */
    public static function getSalesAnalytics($dateFrom = null, $dateTo = null, $serviceType = null)
    {
        $query = static::query();

        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        if ($serviceType && $serviceType !== 'all') {
            $query->where('servicename', $serviceType);
        }

        $transactions = $query->get();

        $analytics = [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', self::STATUS_SUCCESS)->count(),
            'failed_transactions' => $transactions->where('status', self::STATUS_FAILED)->count(),
            'total_revenue' => $transactions->where('status', self::STATUS_SUCCESS)->sum(function($t) {
                return floatval($t->amount);
            }),
            'total_profit' => $transactions->where('status', self::STATUS_SUCCESS)->sum('profit'),
            'service_breakdown' => []
        ];

        // Service breakdown
        $serviceTypes = [
            self::SERVICE_AIRTIME,
            self::SERVICE_DATA,
            self::SERVICE_CABLE_TV,
            self::SERVICE_ELECTRICITY,
            self::SERVICE_EXAM_PIN,
            self::SERVICE_RECHARGE_PIN,
            self::SERVICE_DATA_PIN,
            self::SERVICE_ALPHA_TOPUP
        ];

        foreach ($serviceTypes as $serviceName) {
            $serviceTransactions = $transactions->where('servicename', $serviceName);
            $analytics['service_breakdown'][strtolower(str_replace(' ', '_', $serviceName))] = [
                'name' => $serviceName,
                'transactions' => $serviceTransactions->count(),
                'successful' => $serviceTransactions->where('status', self::STATUS_SUCCESS)->count(),
                'revenue' => $serviceTransactions->where('status', self::STATUS_SUCCESS)->sum(function($t) {
                    return floatval($t->amount);
                }),
                'profit' => $serviceTransactions->where('status', self::STATUS_SUCCESS)->sum('profit')
            ];
        }

        return $analytics;
    }

    /**
     * Get recent transactions for dashboard
     */
    public static function getRecentTransactions($limit = 10)
    {
        return static::with('user')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user transaction summary
     */
    public static function getUserTransactionSummary($userId)
    {
        $transactions = static::where('sId', $userId)->get();

        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', self::STATUS_SUCCESS)->count(),
            'total_spent' => $transactions->where('status', self::STATUS_SUCCESS)->sum(function($t) {
                return floatval($t->amount);
            }),
            'last_transaction' => $transactions->sortByDesc('date')->first(),
            'favorite_service' => $transactions->groupBy('servicename')->sortByDesc(function ($group) {
                return $group->count();
            })->keys()->first()
        ];
    }

    /**
     * Check for duplicate transactions
     */
    public static function checkDuplicate($userId, $servicename, $amount, $timeLimit = 60)
    {
        $timeAgo = Carbon::now()->subSeconds($timeLimit);

        return static::where('sId', $userId)
            ->where('servicename', $servicename)
            ->where('amount', (string) $amount)
            ->where('date', '>=', $timeAgo)
            ->exists();
    }

    /**
     * Record VTU transaction (compatible with old system)
     */
    public static function recordVtuTransaction($userId, $servicename, $description, $amount, $oldBalance, $newBalance, $profit = 0, $reference = null)
    {
        $data = [
            'user_id' => $userId,
            'servicename' => $servicename,
            'description' => $description,
            'amount' => $amount,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'profit' => $profit,
            'status' => self::STATUS_SUCCESS,
            'reference' => $reference
        ];

        return static::recordTransaction($data);
    }

    /**
     * Record wallet funding transaction (compatible with old system)
     */
    public static function recordWalletFunding($userId, $amount, $oldBalance, $newBalance, $gateway = 'Monnify', $charges = 0)
    {
        $chargesText = $charges > 0 ? " with a service charges of {$charges}%. You wallet have been credited with {$newBalance}" : '';

        $data = [
            'user_id' => $userId,
            'servicename' => self::SERVICE_WALLET_TOPUP,
            'description' => "Wallet funding of N{$amount} via {$gateway} bank transfer{$chargesText}",
            'amount' => $newBalance, // Amount after charges
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'profit' => 0,
            'status' => self::STATUS_SUCCESS,
            'type' => 'wallet_funding'
        ];

        return static::recordTransaction($data);
    }

    /**
     * Record wallet credit transaction (compatible with old system)
     */
    public static function recordWalletCredit($userId, $amount, $oldBalance, $newBalance, $reason = '', $adminEmail = '')
    {
        $data = [
            'user_id' => $userId,
            'servicename' => self::SERVICE_WALLET_CREDIT,
            'description' => "Wallet Credit of N{$amount} for user {$adminEmail}. Reason: {$reason}",
            'amount' => $amount,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'profit' => 0,
            'status' => self::STATUS_SUCCESS
        ];

        return static::recordTransaction($data);
    }

    /**
     * Get transaction by reference
     */
    public static function getByReference($reference)
    {
        return static::where('transref', $reference)->first();
    }

    /**
     * Get formatted amount (with Naira symbol)
     */
    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format(floatval($this->amount), 2);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('M d, Y H:i') : 'Unknown';
    }

    /**
     * Get service icon class
     */
    public function getServiceIconAttribute()
    {
        $icons = [
            self::SERVICE_AIRTIME => 'fas fa-phone',
            self::SERVICE_DATA => 'fas fa-wifi',
            self::SERVICE_CABLE_TV => 'fas fa-tv',
            self::SERVICE_ELECTRICITY => 'fas fa-bolt',
            self::SERVICE_EXAM_PIN => 'fas fa-graduation-cap',
            self::SERVICE_RECHARGE_PIN => 'fas fa-credit-card',
            self::SERVICE_DATA_PIN => 'fas fa-sim-card',
            self::SERVICE_ALPHA_TOPUP => 'fas fa-plus-circle',
            self::SERVICE_WALLET_TOPUP => 'fas fa-wallet',
            self::SERVICE_WALLET_CREDIT => 'fas fa-gift'
        ];

        return $icons[$this->servicename] ?? 'fas fa-receipt';
    }

    /**
     * Scope for successful transactions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope for failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    /**
     * Scope for service type
     */
    public function scopeService($query, $servicename)
    {
        return $query->where('servicename', $servicename);
    }
}
