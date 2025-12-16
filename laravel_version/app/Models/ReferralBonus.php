<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferralBonus extends Model
{
    use HasFactory;

    protected $table = 'referral_bonuses';

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'service_type',
        'bonus_amount',
        'transaction_ref',
        'paid',
    ];

    protected $casts = [
        'bonus_amount' => 'decimal:2',
        'paid' => 'boolean',
    ];

    /**
     * Get the referrer user
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id', 'id');
    }

    /**
     * Get the referred user
     */
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id', 'id');
    }

    /**
     * Get the transaction
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_ref', 'transref');
    }

    /**
     * Scope for paid bonuses
     */
    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    /**
     * Scope for unpaid bonuses
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }
}
