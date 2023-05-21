<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class depositKelas_Member extends Model
{
    use HasFactory;
    protected $table = 'deposit_kelasmembers';
    protected $fillable = [
        'id',
        'id_member',
        'id_kelas',
        'masa_berlaku_depositK',
        'sisa_depositK',
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
