<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';
    protected $primaryKey = 'msgId';
    public $timestamps = false;

    protected $fillable = [
        'sId',
        'name',
        'contact',
        'subject',
        'message',
        'dPosted',
    ];

    protected $casts = [
        'dPosted' => 'datetime',
        'sId' => 'integer',
    ];

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->dPosted->format('M d, Y h:i A');
    }

    /**
     * Get recent contacts
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('dPosted', 'desc')->limit($limit);
    }

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'sId', 'id');
    }

    /**
     * Get contact method type (email/phone)
     */
    public function getContactTypeAttribute()
    {
        if (filter_var($this->contact, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if (preg_match('/^[\+]?[1-9][\d]{0,15}$/', $this->contact)) {
            return 'phone';
        }

        return 'other';
    }
}
