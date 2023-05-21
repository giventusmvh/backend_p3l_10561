<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\PresensiInstruktur;
use App\Models\JadwalHarian;
use App\Models\IzinInstruktur;
use App\Models\JadwalUmum;
use Carbon\Carbon;


class PresensiInstrukturController extends Controller
{
    public function updateJamMulai($id){
        
        $cekPresensi = PresensiInstruktur::where('id_jadwalHarian',$id)->first();
        if($cekPresensi){
            return response()->json([
                'jamMulai'=>true,
                'success' => false,
                'message' => 'Gagal',
                'data' => null
            ], 400);
        }

        $presensiInstruktur = new presensiInstruktur;
        $presensiInstruktur->id_jadwalHarian = $id;

        $jadwalHarian = jadwalHarian::find($presensiInstruktur->id_jadwalHarian);
        $jadwalUmum = JadwalUmum::find($jadwalHarian->id_jadwalUmum);
        if($jadwalUmum->id_instruktur_pengganti != null){
            $izinInstruktur = izinInstruktur::where('id_jadwalHarian', $jadwalHarian->id)
                ->where('tgl_izin', $jadwalHarian->tanggal)
                ->first();
            $presensiInstruktur->id_instruktur = $izinInstruktur->id_instruktur_pengganti;
        }else{
            $presensiInstruktur->id_instruktur = $jadwalUmum->id_instruktur;
        }

        
        $presensiInstruktur->waktu_mulai = Carbon::now();
        if($presensiInstruktur->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => $presensiInstruktur
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
                'data' => null
            ], 400);
        }
    }
    
    public function updateJamSelesai($id){
        

        $presensiInstruktur = PresensiInstruktur::where('id_jadwalHarian',$id)->first();
        if($presensiInstruktur->waktu_selesai!==null){
            return response()->json([
                'jamSelesai'=>true,
                'success' => false,
                'message' => 'Gagal',
                'data' => null
            ], 400);
        }
        $presensiInstruktur->waktu_selesai = Carbon::now();
        if($presensiInstruktur->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => $presensiInstruktur
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
                'data' => null
            ], 400);
        }
    }

}
