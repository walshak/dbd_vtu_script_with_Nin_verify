<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact';
    protected $primaryKey = 'cId';
    public $timestamps = true;

    protected $fillable = [
        'cName',
        'cEmail',
        'cSubject',
        'cMessage',
        'cPhone',
        'cStatus'
    ];

    protected $casts = [
        'cStatus' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const CREATED_AT = 'dPosted';
    const UPDATED_AT = null; // No updated_at in original table

    /**
     * Message status constants
     */
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_REPLIED = 2;

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('cStatus', self::STATUS_UNREAD);
    }

    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->where('cStatus', self::STATUS_READ);
    }

    /**
     * Scope for replied messages
     */
    public function scopeReplied($query)
    {
        return $query->where('cStatus', self::STATUS_REPLIED);
    }

    /**
     * Get recent contact messages
     */
    public static function getRecentMessages($limit = 10)
    {
        return static::orderBy('dPosted', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread message count
     */
    public static function getUnreadCount()
    {
        return static::unread()->count();
    }

    /**
     * Create new contact message
     */
    public static function createMessage($name, $email, $subject, $message, $phone = null)
    {
        return static::create([
            'cName' => $name,
            'cEmail' => $email,
            'cSubject' => $subject,
            'cMessage' => $message,
            'cPhone' => $phone,
            'cStatus' => self::STATUS_UNREAD,
            'dPosted' => now()
        ]);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->cStatus = self::STATUS_READ;
        return $this->save();
    }

    /**
     * Mark message as replied
     */
    public function markAsReplied()
    {
        $this->cStatus = self::STATUS_REPLIED;
        return $this->save();
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            self::STATUS_UNREAD => 'Unread',
            self::STATUS_READ => 'Read',
            self::STATUS_REPLIED => 'Replied'
        ];

        return $statuses[$this->cStatus] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            self::STATUS_UNREAD => 'badge-warning',
            self::STATUS_READ => 'badge-info',
            self::STATUS_REPLIED => 'badge-success'
        ];

        return $classes[$this->cStatus] ?? 'badge-secondary';
    }

    /**
     * Check if message is unread
     */
    public function isUnread()
    {
        return $this->cStatus == self::STATUS_UNREAD;
    }

    /**
     * Check if message is read
     */
    public function isRead()
    {
        return $this->cStatus == self::STATUS_READ;
    }

    /**
     * Check if message is replied
     */
    public function isReplied()
    {
        return $this->cStatus == self::STATUS_REPLIED;
    }

    /**
     * Get formatted message content
     */
    public function getFormattedMessageAttribute()
    {
        return nl2br(e($this->cMessage));
    }

    /**
     * Get message excerpt
     */
    public function getExcerpt($length = 100)
    {
        return strlen($this->cMessage) > $length
            ? substr($this->cMessage, 0, $length) . '...'
            : $this->cMessage;
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->dPosted ? $this->dPosted->format('M d, Y H:i') : 'Unknown';
    }
}
