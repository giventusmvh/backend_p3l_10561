<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Models\JadwalUmum;

class JadwalUmumController extends Controller
{
    
    public function cekJadwalInstruktur(Request $request){
        $cek = JadwalUmum::where('id_instruktur', $request->id_instruktur)
        ->where('hari', $request->hari)->where('jam_kelas', $request->jam_kelas)->first();
        if(is_null($cek)){
            return false;
        }else{
            return true;
        }
    }

    public function index(){
        $jadwalUmums = JadwalUmum::join('kelas', 'kelas.id', '=', 'jadwal_umums.id_kelas')
                        ->join('instrukturs', 'instrukturs.id', '=', 'jadwal_umums.id_instruktur')
                        ->orderByRaw("CASE jadwal_umums.hari
                                          WHEN 'Senin' THEN 1
                                          WHEN 'Selasa' THEN 2
                                          WHEN 'Rabu' THEN 3
                                          WHEN 'Kamis' THEN 4
                                          WHEN 'Jumat' THEN 5
                                          WHEN 'Sabtu' THEN 6
                                          ELSE 7
                                      END")
                        ->orderBy('jadwal_umums.jam_kelas')
                        ->select('jadwal_umums.hari', 'jadwal_umums.jam_kelas','instrukturs.nama_instruktur', 'kelas.nama_kelas', 'jadwal_umums.id')
                        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Jadwal Umum',
            'data' => $jadwalUmums
        ], 200);
    }
    
    
    public function add(Request $request){
        $storeData=$request->all();
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required|integer',
            'id_kelas' => 'required|integer',
            'hari' => 'required|string',
            'jam_kelas' => 'required',
        ]);
        if($validator->fails())
        return response()->json($validator->errors(), 400);

        if(self::cekJadwalInstruktur($request)){
            return response()->json([
                'tabrakan'=>true,
                'success' => false,
                'message' => [
                    'id_instruktur' => ['Jadwal Instruktur Bertabrakan'],
                ],
                'data' => null
            ], 400);
        }
        $JadwalUmum=JadwalUmum::create($storeData);
        if($JadwalUmum->save()){
            return response()->json([
                'success' => true,
                'message' => 'Jadwal Umum berhasil ditambahkan',
                'data' => $JadwalUmum
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Umum gagal ditambahkan',
                'data' => null
            ], 400);
        }
    }
    


    public function update(Request $request, $id){
        $JadwalUmum = JadwalUmum::find($id);
        if(is_null($JadwalUmum)){
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Umum Tidak Ada',
                'data' => null
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required|integer',
            'id_kelas' => 'required|integer',
            'hari' => 'required|string',
            'jam_kelas' => 'required',
        ]);
        if($validator->fails())
        return response()->json($validator->errors(), 400);
        if(self::cekJadwalInstruktur($request)){
            return response()->json([
                'tabrakan'=>true,
                'success' => false,
                'message' => [
                    'id_instruktur' => ['Jadwal Instruktur Bertabrakan'],
                ],
                'data' => null
            ], 400);
        }
        $updateData = $request->all(); 
        $JadwalUmum->id_instruktur = $updateData['id_instruktur'];
        $JadwalUmum->id_kelas = $updateData['id_kelas'];
        $JadwalUmum->hari = $updateData['hari'];
        $JadwalUmum->jam_kelas = $updateData['jam_kelas'];
        if($JadwalUmum->save()){
            return response()->json([
                'success' => true,
                'message' => 'Jadwal Umum berhasil diubah',
                'data' => $JadwalUmum
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Umum gagal diubah',
                'data' => null
            ], 400);
        }
    }
    


    public function delete($id){
        
        $JadwalUmum = JadwalUmum::find($id);
        if(is_null($JadwalUmum)){
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Umum tidak ditemukan',
                'data' => null
            ], 400);
        }
        if($JadwalUmum->delete()){
            return response()->json([
                'success' => true,
                'message' => 'Jadwal Umum berhasil dihapus',
                'data' => $JadwalUmum
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Umum gagal dihapus',
                'data' => null
            ], 400);
        }
    }
    
    public function show($id){
        $JadwalUmum = JadwalUmum::find($id);

        if(!is_null($JadwalUmum)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $JadwalUmum
            ], 200);
        } 

        return response([
            'message' => 'Jadwal Umum Not Found',
            'data' => null
        ], 400); 
    }

}
