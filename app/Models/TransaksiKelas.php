<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKelas extends Model
{
    use HasFactory;
    protected $table = 'transaksi_kelass';
    protected $fillable = [
        'id',
        'no_struk_kelas',
        'id_pegawai_kelas',
        'id_member_kelas',
        'id_promo_kelas',
        'id_kelas',
        'tgl_TransaksiKelas',
        'jumlah_bayar_kelas',
        'bonus_deposit_kelas',
        'total_deposit_kelas',
        'sisa_deposit_kelas',
        'masa_berlaku_depositKelas',
        'total_pembayaran_kelas'
        
        
    ];
}
