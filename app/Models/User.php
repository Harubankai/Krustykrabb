<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Explicitly define the table name
    protected $table = 'user';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'role',
        'password_reset_token',
        'token_expires_at',
        'profile_picture',
        'vehicle',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
