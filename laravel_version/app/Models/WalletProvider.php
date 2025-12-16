<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletProvider extends Model
{
    use HasFactory;

    protected $table = 'wallet_providers';

    protected $fillable = [
        'provider_name',
        'api_key',
        'api_url',
        'balance',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Scope for active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for providers with sufficient balance
     */
    public function scopeWithSufficientBalance($query, $amount)
    {
        return $query->where('balance', '>=', $amount);
    }

    /**
     * Get the best provider for a transaction
     */
    public static function getBestProvider($amount)
    {
        return self::active()
            ->withSufficientBalance($amount)
            ->orderBy('priority', 'asc')
            ->first();
    }

    /**
     * Update provider balance
     */
    public function updateBalance($newBalance)
    {
        $this->balance = $newBalance;
        $this->save();
    }

    /**
     * Debit provider balance
     */
    public function debitBalance($amount)
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Credit provider balance
     */
    public function creditBalance($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Check if provider has sufficient balance
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }
}