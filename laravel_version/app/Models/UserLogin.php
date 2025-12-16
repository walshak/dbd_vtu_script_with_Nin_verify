<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use HasFactory;

    protected $table = 'userlogin';

    protected $fillable = [
        'user',
        'token',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}
