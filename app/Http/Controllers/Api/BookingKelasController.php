<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\JadwalUmum;

use App\Models\BookingKelas;
use App\Models\JadwalHarian;
use Illuminate\Http\Request;
use App\Models\depositKelas_Member;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class BookingKelasController extends Controller
{
    //cek aktivasi member
    public function cekAktivasi(Request $request){
        $member = User::find($request->id_member);
        if(is_null($member) || $member->status_member == 0){
           return true;
       }else{
           return false;
       }
    }
    //cek deposit member
    public function cekDeposit(Request $request){
        $member = User::find($request->id_member);
        $cekSisaUang=$member->deposit_uang_member;
        $jadwalHarian = JadwalHarian::find($request->id_jadwalHarian);
        $jadwalUmum = JadwalUmum::find($jadwalHarian->id_jadwalUmum);
        $kelas = kelas::find($jadwalUmum->id_kelas);
        $depositKelasM=depositKelas_Member::where('id_member',$request->id_member)->where('id_kelas',$jadwalUmum->id_kelas)->where('masa_berlaku_depositK','>','0')->first();
        
        if($depositKelasM || $cekSisaUang >= $kelas->harga_kelas){
            return false;
        }else{
            return true;
        }
       
    }
    //cek kuota
    public function cekKuota(Request $request){
        $booking = bookingKelas::where('id_jadwalHarian', $request->id_jadwalHarian)       
            ->where('cancel', false)
            ->count();
        if($booking >= 10){
            return true;
        }else{
            return false;
        }
    }
    
    public function store(Request $request){
        $member = User::find($request->id_member);
        $jadwalHarian = JadwalHarian::find($request->id_jadwalHarian);
        $jadwalUmum = JadwalUmum::find($jadwalHarian->id_jadwalUmum);
        $kelas = kelas::find($jadwalUmum->id_kelas);
        $depositKelasM=depositKelas_Member::where('id_member',$request->id_member)->where('id_kelas',$jadwalUmum->id_kelas)->where('masa_berlaku_depositK','>','0')->first();

        if(self::cekAktivasi($request)){
            return response()->json([
                'gagalaktivasi'=>true,
                'success' => false,
                'message' => 'Cek Status Aktivasi Anda!',
                'data' => null
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'id_jadwalHarian' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }
        if(self::cekDeposit($request)){
            return response()->json([
                'gagaldeposit'=>true,
                'success' => false,
                'message' => 'Cek deposit anda!',
                'data' => null
            ], 400);
        }
        if(self::cekKuota($request)){
            return response()->json([
                'gagalkuota'=>true,
                'success' => false,
                'message' => 'Kuota sudah penuh',
                'data' => null
            ], 400);
        }
        $booking = new BookingKelas;
        $carbon=\Carbon\Carbon::now();
        $dateY=$carbon->format('y');
        $dateM=$carbon->format('m');
        $totalAktivasi=sprintf('%03d',(BookingKelas::all()->count())+1);
        $stringKode=$dateY.'.'.$dateM.'.'.$totalAktivasi;
        $booking->no_booking = $stringKode;
        $booking->id_member = $request->id_member;
        $booking->id_jadwalHarian = $request->id_jadwalHarian;
        $booking->id_kelas=$kelas->id;
        if($depositKelasM){
            $booking->id_depositKelasM = $depositKelasM->id;
        }
        
        if($booking->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil booking kelas',
                'data' => $booking
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal booking kelas',
                'data' => null
            ], 400);
        }
    }
    //batal booking
    public function cancel(Request $request){
        $booking = BookingKelas::find($request->id);
        if(is_null($booking)){
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'data' => null
            ], 400);
        }
        if($booking->cancel){
            return response()->json([
                'success' => false,
                'message' => 'Sudah cancel booking',
                'data' => null
            ], 400);
        }
        $jadwalHarian = jadwalHarian::find($booking->id_jadwalHarian);
        if(Carbon::now()->format('Y-m-d') >= $jadwalHarian->tanggal){
            return response()->json([
                'telat'=>true,
                'success' => false,
                'message' => 'Tidak bisa membatalkan booking',
                'data' => null
            ], 400);
        }
        $booking->cancel = true;
        if($booking->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil membatalkan booking',
                'data' => $booking
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan booking',
                'data' => null
            ], 400);
        }
    }

    public function show($id){

        $indexBookingKelas = BookingKelas::join('jadwal_harians', 'jadwal_harians.id', '=', 'booking_kelas.id_jadwalHarian')
                            ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
                            ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
                            ->join('instrukturs', 'instrukturs.id', '=', 'jadwal_umums.id_instruktur')
                            ->leftjoin('instrukturs as instruktursPengganti', 'instruktursPengganti.id', '=', 'jadwal_umums.id_instruktur_pengganti')
                            ->orderBy('booking_kelas.created_at', 'desc')
                            ->select('instruktursPengganti.nama_instruktur as pengganti','jadwal_harians.status_jadwalHarian','booking_kelas.id','booking_kelas.id_member','booking_kelas.no_booking','kelas.nama_kelas', 'jadwal_harians.tanggal', 'booking_kelas.cancel','jadwal_umums.jam_kelas', 'instrukturs.nama_instruktur')
                            ->where('booking_kelas.id_member', $id)
                            ->where('booking_kelas.cancel', false)
                            ->get();
            
    
            return response()->json([
                'success' => true,
                'message' => 'Daftar Booking Kelas',
                'data' => $indexBookingKelas,
            ], 200);
        
        
    }

    public function showKasir($id){

        $indexBookingKelas = BookingKelas::join('jadwal_harians', 'jadwal_harians.id', '=', 'booking_kelas.id_jadwalHarian')
        ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('instrukturs', 'instrukturs.id', '=', 'jadwal_umums.id_instruktur')
        ->join('users', 'users.id', '=', 'booking_kelas.id_member')
        ->leftjoin('instrukturs as instruktursPengganti', 'instruktursPengganti.id', '=', 'jadwal_umums.id_instruktur_pengganti')
        ->orderBy('booking_kelas.created_at', 'desc')
        ->select('booking_kelas.status_presensi'
                ,'booking_kelas.id'
                ,'users.deposit_uang_member'
                ,'booking_kelas.waktu_presensi_kelas'
                ,'booking_kelas.id_depositKelasM'
                ,'booking_kelas.id_member'
                ,'users.nama_member'
                ,'instruktursPengganti.nama_instruktur as pengganti'
                ,'jadwal_harians.status_jadwalHarian'
                ,'booking_kelas.no_booking'
                ,'kelas.nama_kelas'
                ,'kelas.id as id_kelas'
                ,'kelas.harga_kelas'
                , 'jadwal_harians.tanggal'
                , 'booking_kelas.cancel'
                ,'jadwal_umums.jam_kelas'
                , 'instrukturs.nama_instruktur')
        ->where('booking_kelas.id', $id)
        ->where('booking_kelas.cancel', false)
        ->get();
            
    
            return response()->json([
                'success' => true,
                'message' => 'Daftar Booking Kelas',
                'data' => $indexBookingKelas,
            ], 200);
        
        
    }

    public function showByID($id){
        $instruktur = BookingKelas::find($id);

        if(!is_null($instruktur)){
            return response([
                'message' => 'Retrieve Instruktur Success',
                'data' => $instruktur
            ], 200);
        } 

        return response([
            'message' => 'Instruktur Not Found',
            'data' => null
        ], 400); 
    }
}
