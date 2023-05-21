<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\depositKelas_Member;
use Illuminate\Http\Request;

class DepositKelasController extends Controller
{
    public function show($id){
        $kelas =depositKelas_Member::find($id);

        if(!is_null($kelas)){
            return response([
                'message' => 'Retrieve Kelas Success',
                'data' => $kelas
            ], 200);
        } 
    }
}
