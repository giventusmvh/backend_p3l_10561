<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IzinInstruktur;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\JadwalUmum ;
use App\Models\JadwalHarian ;

class JadwalHarianController extends Controller
{

    public function show($id){
        $JadwalHarian = jadwalHarian::find($id);

        if(!is_null($JadwalHarian)){
            return response([
                'message' => 'Retrieve Jadwal Harian Success',
                'data' => $JadwalHarian
            ], 200);
        } 

        return response([
            'message' => 'Jadwal Harian Not Found',
            'data' => null
        ], 400); 
    }
   
    public function cekGenerate(){
        $jadwalHarian = jadwalHarian::where('tanggal', '>', Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'))
            ->first();
        if(is_null($jadwalHarian)){
            return false;
        }else{
            return true;
        }
    }
 
    

    public function generateJadwalHarian(){
        
        if(self::cekGenerate()){
            return response()->json([
                'success' => false,
                'cekGenerate'=>true,
                'message' => 'Jadwal harian minggu ini sudah di generate',
                'data' => null,
            ], 400);
        }

        // // ambil semua data jadwal yang sudah ada
        // $jadwalLama = JadwalHarian::all();
        // $jadwalUmumlama=JadwalUmum::all();
        // // hapus semua data jadwal yang sudah ada
        // foreach ($jadwalLama as $jadwal) {
        //     $jadwal->delete();
        // }

        // foreach ($jadwalUmumlama as $jadwalU) {
        //     $jadwalU->id_instruktur_pengganti =NULL;
        //     $jadwalU->save();
        // }

        $start_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDay();
        $end_date =  Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays(7);
        for($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $jadwalUmum = jadwalUmum::where('hari', Carbon::parse($date)->format('l'))->get();
            for($index = 0; $index < count($jadwalUmum); $index++){
                $jadwalHarian = new jadwalHarian;
                $jadwalHarian->id_jadwalUmum = $jadwalUmum[$index]->id;
                $jadwalHarian->tanggal = $date;
                
                $jadwalHarian->save();    
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Jadwal harian berhasil di generate',
            'data' => null
        ], 200);
    }
    
    public function libur($id){
       
        $jadwalHarian = jadwalHarian::find($id);
        // $izinInstruktur=IzinInstruktur::where('id_jadwalHarian',$id)->get();

        if(is_null($jadwalHarian)){
            return response()->json([
                'success' => false,
                'message' => 'Jadwal harian tidak ditemukan',
                'data' => null
            ], 400);
        }
        $jadwalHarian->status_jadwalHarian = 0;
        $jadwalHarian->save();
        return response()->json([
            'success' => true,
            'message' => 'Jadwal harian berhasil di liburkan',
            'data' => null
        ], 200);
    }

    public function gantiInstruktur($id){
       
        $jadwalHarian = jadwalHarian::find($id);
        $izinInstruktur=IzinInstruktur::where('id_jadwalHarian',$id)->first();
        $jadwalUmum=jadwalUmum::where('id',$jadwalHarian->id_jadwalUmum)->first();
        if(is_null($jadwalHarian)){
            return response()->json([
                'success' => false,
                'message' => 'Jadwal harian tidak ditemukan',
                'data' => null
            ], 400);
        }
        $jadwalUmum->id_instruktur_pengganti = $izinInstruktur->id_instruktur_pengganti;
        $jadwalUmum->save();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil ganti Instruktur',
            'data' => null
        ], 200);
    }


    

    public function index(){
        $start_date = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $end_date = Carbon::now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');

        $indexJadwalHarian = jadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwalUmum', '=', 'jadwal_umums.id')
                        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                        ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
                        ->leftJoin('izin_instrukturs', 'jadwal_harians.id', '=', 'izin_instrukturs.id_jadwalHarian')
                        ->leftjoin('instrukturs as instrukturPengganti', 'jadwal_umums.id_instruktur_pengganti', '=', 'instrukturPengganti.id')
                        ->orderBy('jadwal_harians.tanggal', 'asc')
                        ->orderBy('jadwal_umums.jam_kelas')
                        ->whereBetween('jadwal_harians.tanggal', [$start_date, $end_date])
                        ->select('izin_instrukturs.konfirmasi','jadwal_harians.id','jadwal_harians.status_jadwalHarian','jadwal_harians.tanggal', 'jadwal_umums.jam_kelas','jadwal_umums.hari', 'kelas.nama_kelas as nama_kelas', 'instrukturs.nama_instruktur as nama_instruktur','instrukturPengganti.nama_instruktur as nama_instruktur_pengganti','jadwal_umums.jam_kelas',)
                        
                        ->get();
            
        

        return response()->json([
            'success' => true,
            'message' => 'Daftar Jadwal Harian',
            'data' => $indexJadwalHarian,
        ], 200);
    }

    public function indexToday(){
        

        $indexJadwalHarian = jadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwalUmum', '=', 'jadwal_umums.id')
                        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                        ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
                        ->leftJoin('izin_instrukturs', 'jadwal_harians.id', '=', 'izin_instrukturs.id_jadwalHarian')
                        ->leftjoin('instrukturs as instrukturPengganti', 'jadwal_umums.id_instruktur_pengganti', '=', 'instrukturPengganti.id')
                        ->orderBy('jadwal_harians.tanggal', 'asc')
                        ->orderBy('jadwal_umums.jam_kelas')
                        ->where('jadwal_harians.tanggal',Carbon::today())
                        ->select('izin_instrukturs.konfirmasi','jadwal_harians.id','jadwal_harians.status_jadwalHarian','jadwal_harians.tanggal', 'jadwal_umums.jam_kelas','jadwal_umums.hari', 'kelas.nama_kelas as nama_kelas', 'instrukturs.nama_instruktur as nama_instruktur','instrukturPengganti.nama_instruktur as nama_instruktur_pengganti','jadwal_umums.jam_kelas',)
                        ->get();
            
        

        return response()->json([
            'success' => true,
            'message' => 'Daftar Jadwal Harian',
            'data' => $indexJadwalHarian,
        ], 200);
    }

    public function indexTodayByInstruktur($id){
        

        $indexJadwalHarian = jadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwalUmum', '=', 'jadwal_umums.id')
                        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                        ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
                        ->leftJoin('izin_instrukturs', 'jadwal_harians.id', '=', 'izin_instrukturs.id_jadwalHarian')
                        ->leftjoin('instrukturs as instrukturPengganti', 'jadwal_umums.id_instruktur_pengganti', '=', 'instrukturPengganti.id')
                        ->orderBy('jadwal_harians.tanggal', 'asc')
                        ->orderBy('jadwal_umums.jam_kelas')
                        ->where('jadwal_umums.id_instruktur',$id)
                        ->where('jadwal_harians.tanggal',Carbon::today())
                        ->select('izin_instrukturs.konfirmasi','jadwal_harians.id','jadwal_harians.status_jadwalHarian','jadwal_harians.tanggal', 'jadwal_umums.jam_kelas','jadwal_umums.hari', 'kelas.nama_kelas as nama_kelas', 'instrukturs.nama_instruktur as nama_instruktur','instrukturPengganti.nama_instruktur as nama_instruktur_pengganti','jadwal_umums.jam_kelas',)
                        ->get();
            
        

        return response()->json([
            'success' => true,
            'message' => 'Daftar Jadwal Harian',
            'data' => $indexJadwalHarian,
        ], 200);
    }

}
