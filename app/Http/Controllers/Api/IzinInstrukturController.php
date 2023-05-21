<?php

namespace App\Http\Controllers\Api;

use App\Models\Instruktur;
use App\Models\JadwalHarian;
use Illuminate\Http\Request;
use App\Models\IzinInstruktur;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class IzinInstrukturController extends Controller
{
   //tampilkan daftar izin (MO)
   public function index(){

    $indexIzinInstruktur = IzinInstruktur::join('jadwal_harians', 'jadwal_harians.id', '=', 'izin_instrukturs.id_jadwalHarian')
                        ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
                        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                        ->join('instrukturs', 'izin_instrukturs.id_instruktur', '=', 'instrukturs.id')
                        ->join('instrukturs as instrukturPengganti', 'izin_instrukturs.id_instruktur_pengganti', '=', 'instrukturPengganti.id')
                        ->orderBy('izin_instrukturs.created_at', 'desc')
                        ->select('izin_instrukturs.keterangan','izin_instrukturs.id','kelas.nama_kelas', 'instrukturPengganti.nama_instruktur as nama_instrukturPengganti', 'instrukturs.nama_instruktur as nama_instrukturIzin','izin_instrukturs.tgl_izin','izin_instrukturs.created_at as tgl_izin_dibuat', 'izin_instrukturs.konfirmasi')
                        
                        ->get();
        

        return response()->json([
            'success' => true,
            'message' => 'Daftar Izin',
            'data' => $indexIzinInstruktur,
        ], 200);
    
    
}

public function belumKonfirmasiIndex(){
    

    $indexIzinInstruktur = IzinInstruktur::join('jadwal_harians', 'jadwal_harians.id', '=', 'izin_instrukturs.id_jadwalHarian')
                        ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
                        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                        ->join('instrukturs', 'izin_instrukturs.id_instruktur', '=', 'instrukturs.id')
                        ->join('instrukturs as instrukturPengganti', 'izin_instrukturs.id_instruktur_pengganti', '=', 'instrukturPengganti.id')
                        ->orderBy('izin_instrukturs.created_at', 'asc')
                        ->where('izin_instrukturs.konfirmasi', false)
                        ->select('izin_instrukturs.keterangan','izin_instrukturs.id','kelas.nama_kelas', 'instrukturPengganti.nama_instruktur as nama_instrukturPengganti', 'instrukturs.nama_instruktur as nama_instrukturIzin','izin_instrukturs.tgl_izin','izin_instrukturs.created_at as tgl_izin_dibuat', 'izin_instrukturs.konfirmasi')
                        
                        ->get();

    
    return response()->json([
        'success' => true,
        'message' => 'Daftar Izin yang belum dikonfirmasi',
        'data' => $indexIzinInstruktur
    ], 200);
}

public function show($id){

    $indexIzinInstruktur = IzinInstruktur::join('jadwal_harians', 'jadwal_harians.id', '=', 'izin_instrukturs.id_jadwalHarian')
                        ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
                        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                        ->join('instrukturs', 'izin_instrukturs.id_instruktur', '=', 'instrukturs.id')
                        ->join('instrukturs as instrukturPengganti', 'izin_instrukturs.id_instruktur_pengganti', '=', 'instrukturPengganti.id')
                        ->orderBy('izin_instrukturs.created_at', 'asc')
                        ->select('izin_instrukturs.keterangan','izin_instrukturs.id','kelas.nama_kelas', 'instrukturPengganti.nama_instruktur as nama_instrukturPengganti', 'instrukturs.nama_instruktur as nama_instrukturIzin','izin_instrukturs.tgl_izin','izin_instrukturs.created_at as tgl_izin_dibuat', 'izin_instrukturs.konfirmasi')
                        ->where('izin_instrukturs.id_instruktur', $id)
                        ->get();
        

        return response()->json([
            'success' => true,
            'message' => 'Daftar Izin',
            'data' => $indexIzinInstruktur,
        ], 200);
    
    
}

public function showByID($id){
    $izin = IzinInstruktur::find($id);

    if(!is_null($izin)){
        return response([
            'message' => 'Retrieve IzinInstruktur Success',
            'data' => $izin
        ], 200);
    } 

    return response([
        'message' => 'IzinInstruktur Not Found',
        'data' => null
    ], 400); 
}


public function konfirmasiIzin($id){
    
    $izinInstruktur = izinInstruktur::find($id);
    if(is_null($izinInstruktur)){
        return response()->json([
            'success' => false,
            'message' => 'Izin Instruktur tidak ditemukan',
            'data' => null
        ], 400);
    }
    $izinInstruktur->konfirmasi = true;
    
    if($izinInstruktur->save()){

        
        return response()->json([
            'success' => true,
            'message' => 'berhasil',
            'data' => $izinInstruktur
        ], 200);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Izin Instruktur gagal konfirmasi',
            'data' => null
        ], 400);
    }
}

public function ajukanizin(Request $request){
    
    
    $Validator = Validator::make($request->all(), [
        
        'id_instruktur_pengganti' => 'required',
        'id_jadwalHarian' => 'required',
        'keterangan' => 'required',
        
    ]);
    if($Validator->fails()){
        return response()->json([
            'success' => false,
            'message' => 'Gagal bang',
            'data' => null
        ], 400);
    }
    $jadwalHarian = JadwalHarian::find($request->id_jadwalHarian);
    $izinInstruktur = new izinInstruktur();
    $izinInstruktur->id_instruktur = $request->id_instruktur;
    $izinInstruktur->keterangan = $request->keterangan;
    $izinInstruktur->id_instruktur_pengganti = $request->id_instruktur_pengganti;
    $izinInstruktur->id_jadwalHarian = $request->id_jadwalHarian;
    $izinInstruktur->tgl_izin = $jadwalHarian->created_at;
    if($izinInstruktur->save()){
        return response()->json([
            'success' => true,
            'message' => 'Izin Instruktur berhasil ditambahkan',
            'data' => $izinInstruktur
        ], 200);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Izin Instruktur gagal ditambahkan',
            'data' => null
        ], 400);
    }
}



}
