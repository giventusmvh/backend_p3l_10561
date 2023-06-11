<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\depositKelas_Member;
use App\Http\Controllers\Controller;

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

    public function profile($id){

        $indexDepositKelas = depositKelas_Member::join('kelas', 'kelas.id', '=', 'deposit_kelasmembers.id_kelas')
                            ->join('users', 'users.id', '=', 'deposit_kelasmembers.id_member')
                            ->orderBy('deposit_kelasmembers.created_at', 'desc')
                            ->select('deposit_kelasmembers.id'
                            ,'deposit_kelasmembers.id_member'
                            ,'deposit_kelasmembers.id_kelas'
                            ,'deposit_kelasmembers.masa_berlaku_depositK'
                            ,'deposit_kelasmembers.sisa_depositK'
                            ,'kelas.nama_kelas')
                            ->where('deposit_kelasmembers.id_member', $id)
                            ->whereBetween('deposit_kelasmembers.sisa_depositK', [1,100])
                            ->get();
            
    
            return response()->json([
                'success' => true,
                'message' => 'Daftar Booking Gym',
                'data' => $indexDepositKelas,
            ], 200);
        
        
    }
}
