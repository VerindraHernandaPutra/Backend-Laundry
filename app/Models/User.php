<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = ['id_user', 'id_outlet', 'nama', 'username', 'password', 'role'];
    protected $hidden = ['password', 'created_at', 'updated_at'];
    protected $table = "user";
    protected $primaryKey = 'id_user';

    public function outlet(){
        return $this->belongsTo('App\Models\Outlet','id_outlet','id_outlet');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
