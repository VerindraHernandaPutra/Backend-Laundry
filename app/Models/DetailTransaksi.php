<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $fillable = ['id_detailtransaksi', 'id_transaksi', 'id_paket', 'weight'];
    protected $hidden = ['create_at', 'updated_at'];
    protected $table = "detailtransaksi";
    protected $primaryKey = 'id_detailtransaksi';
}