<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
    use HasFactory;
    protected $table = 'promo';
    protected $fillable = [
       'nama_promo',
       'keterangan_promo',
       'jenis_promo',
        'minimal_pembelian',
        'bonus_promo'
    ];

    
}
