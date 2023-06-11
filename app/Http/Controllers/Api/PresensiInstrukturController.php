<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Instruktur;
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
        
        

        $presensiInstruktur = PresensiInstruktur::where('id_jadwalHarian',$id)->orderBy('id', 'desc')->first();
        $instruktur=Instruktur::where('id',$presensiInstruktur->id_instruktur)->first();
        if($presensiInstruktur->waktu_selesai!==null){
            return response()->json([
                'jamSelesai'=>true,
                'success' => false,
                'message' => 'Gagal',
                'data' => null
            ], 400);
        }
        $presensiInstruktur->waktu_selesai = Carbon::now()->addHour(2);
        $jamMulai = Carbon::parse($presensiInstruktur->waktu_mulai);
        $jamSelesai = Carbon::parse($presensiInstruktur->waktu_selesai);
        $presensiInstruktur->keterlambatan = $jamSelesai->diffInSeconds($jamMulai) - 7200;
        $instruktur->akumulasi_terlambat = $instruktur->akumulasi_terlambat + $presensiInstruktur->keterlambatan;
        if($presensiInstruktur->save()){
            $instruktur->save();
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

    public function history($id){

        $indexBookingKelas = PresensiInstruktur::join('jadwal_harians', 'jadwal_harians.id', '=', 'presensi_instrukturs.id_jadwalHarian')
                            ->join('instrukturs', 'instrukturs.id', '=', 'presensi_instrukturs.id_instruktur')
                            ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
                            ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                            ->orderBy('presensi_instrukturs.created_at', 'desc')
                            ->select('presensi_instrukturs.id'
                            ,'presensi_instrukturs.id_jadwalHarian'
                            ,'presensi_instrukturs.waktu_mulai'
                            ,'presensi_instrukturs.waktu_selesai'
                            ,'presensi_instrukturs.keterlambatan'
                            ,'presensi_instrukturs.created_at'
                            ,'jadwal_harians.tanggal'
                            ,'kelas.nama_kelas'
                            )
                            ->where('presensi_instrukturs.id_instruktur', $id)
                            ->get();
            
    
            return response()->json([
                'success' => true,
                'message' => 'Daftar Booking Gym',
                'data' => $indexBookingKelas,
            ], 200);
        
        
    }

}
