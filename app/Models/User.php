<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = "user";
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

}
