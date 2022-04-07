<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $fillable = ['id_paket', 'jenis', 'harga'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $table = "paket";
    protected $primaryKey = 'id_paket';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
