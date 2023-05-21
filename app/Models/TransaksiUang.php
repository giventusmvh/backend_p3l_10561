<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiUang extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'no_struk_uang',
        'id_pegawai_uang',
        'id_member_uang',
        'id_promo_uang',
        'tgl_TransaksiUang',
        'jumlah_bayar_uang',
        'bonus_deposit_uang',
        'total_deposit_uang',
        'sisa_deposit_uang',
        
        
    ];



    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }// convert format created_at menjadi Y-m-d H:i:s

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }// convert
}
