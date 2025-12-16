<?php<?php<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;namespace App\Models;namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Notification extends Model

{use Illuminate\Database\Eloquent\Factories\HasFactory;use Illuminate\Database\Eloquent\Factories\HasFactory;

    use HasFactory;

use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Model;

    protected $table = 'notifications';

    protected $primaryKey = 'msgId';

    public $timestamps = false;

class Notification extends Modelclass Notification extends Model

    protected $fillable = [

        'msgfor',{{

        'subject',

        'message',    use HasFactory;    use HasFactory;

        'status',

        'dPosted',

    ];

    protected $table = 'notifications';    protected $table = 'notifications';

    protected $casts = [

        'dPosted' => 'datetime',    protected $primaryKey = 'msgId';    protected $primaryKey = 'nId';

        'status' => 'integer',

        'msgfor' => 'integer',    public $timestamps = false;    public $timestamps = true;

    ];



    // Message target constants

    const FOR_SUBSCRIBERS = 1;    protected $fillable = [    protected $fillable = [

    const FOR_AGENTS = 2;

    const FOR_GENERAL = 3;        'msgfor',        'nSubject',



    // Status constants        'subject',        'nMessageFor',

    const STATUS_ACTIVE = 0;

    const STATUS_INACTIVE = 1;        'message',        'nMessage',



    public function getTargetAudienceAttribute()        'status',        'nStatus'

    {

        return match($this->msgfor) {        'dPosted',    ];

            self::FOR_SUBSCRIBERS => 'Subscribers',

            self::FOR_AGENTS => 'Agents',     ];

            self::FOR_GENERAL => 'General',

            default => 'Unknown'    protected $casts = [

        };

    }    protected $casts = [        'nStatus' => 'integer',



    public function getStatusNameAttribute()        'dPosted' => 'datetime',        'created_at' => 'datetime',

    {

        return match($this->status) {        'status' => 'integer',        'updated_at' => 'datetime'

            self::STATUS_ACTIVE => 'Active',

            self::STATUS_INACTIVE => 'Inactive',        'msgfor' => 'integer',    ];

            default => 'Unknown'

        };    ];

    }

    const CREATED_AT = 'dPosted';

    public function scopeActive($query)

    {    /**    const UPDATED_AT = 'dUpdated';

        return $query->where('status', self::STATUS_ACTIVE);

    }     * Message target constants



    public function scopeForAudience($query, $audience)     */    /**

    {

        return $query->where('msgfor', $audience);    const FOR_SUBSCRIBERS = 1;     * Notification target types

    }

    const FOR_AGENTS = 2;     */

    public function scopeRecent($query, $limit = 10)

    {    const FOR_GENERAL = 3;    const TARGET_ALL = 'all';

        return $query->orderBy('dPosted', 'desc')->limit($limit);

    }    const TARGET_USERS = 'users';



    public function isActive()    /**    const TARGET_AGENTS = 'agents';

    {

        return $this->status === self::STATUS_ACTIVE;     * Status constants    const TARGET_VENDORS = 'vendors';

    }

     */

    public function getFormattedDateAttribute()

    {    const STATUS_ACTIVE = 0;    /**

        return $this->dPosted->format('M d, Y h:i A');

    }    const STATUS_INACTIVE = 1;     * Notification status



    public static function getForUserType($userType, $limit = null)     */

    {

        $query = static::active()->where(function ($q) use ($userType) {    /**    const STATUS_ACTIVE = 1;

            $q->where('msgfor', $userType)

              ->orWhere('msgfor', self::FOR_GENERAL);     * Get the target audience name    const STATUS_INACTIVE = 0;

        })->orderBy('dPosted', 'desc');

     */

        if ($limit) {

            $query->limit($limit);    public function getTargetAudienceAttribute()    /**

        }

    {     * Scope for active notifications

        return $query->get();

    }        return match($this->msgfor) {     */



    public static function getAudienceTypes()            self::FOR_SUBSCRIBERS => 'Subscribers',    public function scopeActive($query)

    {

        return [            self::FOR_AGENTS => 'Agents',    {

            self::FOR_SUBSCRIBERS => 'Subscribers',

            self::FOR_AGENTS => 'Agents',            self::FOR_GENERAL => 'General',        return $query->where('nStatus', self::STATUS_ACTIVE);

            self::FOR_GENERAL => 'General',

        ];            default => 'Unknown'    }

    }

}        };

    }    /**

     * Scope for specific target audience

    /**     */

     * Get status name    public function scopeForTarget($query, $target)

     */    {

    public function getStatusNameAttribute()        return $query->where('nMessageFor', $target);

    {    }

        return match($this->status) {

            self::STATUS_ACTIVE => 'Active',    /**

            self::STATUS_INACTIVE => 'Inactive',     * Get notifications for user type

            default => 'Unknown'     */

        };    public static function getForUserType($userType = 'user')

    }    {

        return static::active()

    /**            ->whereIn('nMessageFor', ['all', strtolower($userType)])

     * Scope for active notifications            ->orderBy('dPosted', 'desc')

     */            ->get();

    public function scopeActive($query)    }

    {

        return $query->where('status', self::STATUS_ACTIVE);    /**

    }     * Get active notifications for display

     */

    /**    public static function getActiveNotifications($limit = 5)

     * Scope for specific target audience    {

     */        return static::active()

    public function scopeForAudience($query, $audience)            ->orderBy('dPosted', 'desc')

    {            ->limit($limit)

        return $query->where('msgfor', $audience);            ->get();

    }    }



    /**    /**

     * Scope for subscribers     * Create new notification

     */     */

    public function scopeForSubscribers($query)    public static function createNotification($subject, $message, $target = self::TARGET_ALL)

    {    {

        return $query->where('msgfor', self::FOR_SUBSCRIBERS);        return static::create([

    }            'nSubject' => $subject,

            'nMessage' => $message,

    /**            'nMessageFor' => $target,

     * Scope for agents            'nStatus' => self::STATUS_ACTIVE,

     */            'dPosted' => now()

    public function scopeForAgents($query)        ]);

    {    }

        return $query->where('msgfor', self::FOR_AGENTS);

    }    /**

     * Toggle notification status

    /**     */

     * Scope for general notifications    public function toggleStatus()

     */    {

    public function scopeForGeneral($query)        $this->nStatus = $this->nStatus == self::STATUS_ACTIVE ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;

    {        return $this->save();

        return $query->where('msgfor', self::FOR_GENERAL);    }

    }

    /**

    /**     * Get formatted subject

     * Get recent notifications     */

     */    public function getFormattedSubjectAttribute()

    public function scopeRecent($query, $limit = 10)    {

    {        return ucfirst($this->nSubject);

        return $query->orderBy('dPosted', 'desc')->limit($limit);    }

    }

    /**

    /**     * Get formatted message

     * Check if notification is active     */

     */    public function getFormattedMessageAttribute()

    public function isActive()    {

    {        return nl2br(e($this->nMessage));

        return $this->status === self::STATUS_ACTIVE;    }

    }

    /**

    /**     * Get target audience display name

     * Check if notification is for subscribers     */

     */    public function getTargetDisplayAttribute()

    public function isForSubscribers()    {

    {        $targets = [

        return $this->msgfor === self::FOR_SUBSCRIBERS;            'all' => 'All Users',

    }            'users' => 'Regular Users',

            'agents' => 'Agents',

    /**            'vendors' => 'Vendors'

     * Check if notification is for agents        ];

     */

    public function isForAgents()        return $targets[$this->nMessageFor] ?? 'All Users';

    {    }

        return $this->msgfor === self::FOR_AGENTS;

    }    /**

     * Get status display name

    /**     */

     * Check if notification is general    public function getStatusDisplayAttribute()

     */    {

    public function isGeneral()        return $this->nStatus == self::STATUS_ACTIVE ? 'Active' : 'Inactive';

    {    }

        return $this->msgfor === self::FOR_GENERAL;

    }    /**

     * Get status badge class

    /**     */

     * Get formatted date    public function getStatusBadgeClassAttribute()

     */    {

    public function getFormattedDateAttribute()        return $this->nStatus == self::STATUS_ACTIVE ? 'badge-success' : 'badge-secondary';

    {    }

        return $this->dPosted->format('M d, Y h:i A');

    }    /**

     * Check if notification is active

    /**     */

     * Get notifications for specific user type    public function isActive()

     */    {

    public static function getForUserType($userType, $limit = null)        return $this->nStatus == self::STATUS_ACTIVE;

    {    }

        $query = static::active()->where(function ($q) use ($userType) {

            $q->where('msgfor', $userType)    /**

              ->orWhere('msgfor', self::FOR_GENERAL);     * Get notification excerpt

        })->orderBy('dPosted', 'desc');     */

    public function getExcerpt($length = 100)

        if ($limit) {    {

            $query->limit($limit);        return strlen($this->nMessage) > $length

        }            ? substr($this->nMessage, 0, $length) . '...'

            : $this->nMessage;

        return $query->get();    }

    }}


    /**
     * Get all audience types
     */
    public static function getAudienceTypes()
    {
        return [
            self::FOR_SUBSCRIBERS => 'Subscribers',
            self::FOR_AGENTS => 'Agents',
            self::FOR_GENERAL => 'General',
        ];
    }
}