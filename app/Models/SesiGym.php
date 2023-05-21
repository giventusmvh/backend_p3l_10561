<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiGym extends Model
{
    use HasFactory;

    protected $fillable = [
       'id',
       'jam_mulai',
       'jam_selesai'
    ];

}
