<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiAktivasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'no_struk',
        'id_pegawai_aktivasi',
        'id_member_aktivasi',
        'tgl_TransaksiAktivasi',
        'jumlah_bayar_aktivasi',
        'masa_berlaku_aktivasi',
        
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
