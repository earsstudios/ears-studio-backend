<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserCustom extends Authenticatable implements JWTSubject
{
    protected $table = 'tbl_users'; // Ganti dengan nama tabelmu

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'is_member', 'membership_id'
    ];

    protected $hidden = [
        'password',
    ];

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
