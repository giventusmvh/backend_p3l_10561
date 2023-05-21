<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Instruktur;
use App\Models\BookingKelas;
use Illuminate\Http\Request;
use App\Models\PresensiInstruktur;
use App\Models\depositKelas_Member;
use App\Http\Controllers\Controller;
use App\Models\Kelas;

class PresensiKelasController extends Controller
{
    public function cekPresensi($id){
        $bookingKelas=BookingKelas::find($id);
        $presensiInstruktur = PresensiInstruktur::where('id_jadwalHarian', $bookingKelas->id_jadwalHarian)->first();
        if(is_null($presensiInstruktur)){
            return true;
        }else{
            return false;
        }
    }

    public function showToday($id){
       
            $indexBookingKelas = BookingKelas::join('jadwal_harians', 'jadwal_harians.id', '=', 'booking_kelas.id_jadwalHarian')
            ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
            ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
            ->join('instrukturs', 'instrukturs.id', '=', 'jadwal_umums.id_instruktur')
            ->join('users', 'users.id', '=', 'booking_kelas.id_member')
            ->leftjoin('instrukturs as instruktursPengganti', 'instruktursPengganti.id', '=', 'jadwal_umums.id_instruktur_pengganti')
            ->orderBy('booking_kelas.created_at', 'desc')
            ->select('booking_kelas.status_presensi','booking_kelas.id','booking_kelas.waktu_presensi_kelas','users.nama_member','instruktursPengganti.nama_instruktur as pengganti','jadwal_harians.status_jadwalHarian','booking_kelas.id','booking_kelas.no_booking','kelas.nama_kelas', 'jadwal_harians.tanggal', 'booking_kelas.cancel','jadwal_umums.jam_kelas', 'instrukturs.nama_instruktur','jadwal_umums.hari',)
            ->where('jadwal_harians.tanggal', Carbon::today())
            ->where('jadwal_harians.id',$id)
            ->where('booking_kelas.cancel', false)
            ->get();


            return response()->json([
            'success' => true,
            'message' => 'Daftar Booking Kelas',
            'data' => $indexBookingKelas,
            ], 200);

    }

    public function show(){
        $indexBookingKelas = BookingKelas::join('jadwal_harians', 'jadwal_harians.id', '=', 'booking_kelas.id_jadwalHarian')
        ->join('jadwal_umums', 'jadwal_umums.id', '=', 'jadwal_harians.id_jadwalUmum')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('instrukturs', 'instrukturs.id', '=', 'jadwal_umums.id_instruktur')
        ->join('users', 'users.id', '=', 'booking_kelas.id_member')
        ->leftjoin('instrukturs as instruktursPengganti', 'instruktursPengganti.id', '=', 'jadwal_umums.id_instruktur_pengganti')
        ->orderBy('booking_kelas.created_at', 'desc')
        ->select('booking_kelas.status_presensi'
                ,'booking_kelas.id'
                ,'booking_kelas.id_member'
                ,'booking_kelas.waktu_presensi_kelas'
                ,'instrukturs.id as id_instruktur'
                ,'booking_kelas.id_depositKelasM'
                ,'users.nama_member'
                ,'instruktursPengganti.nama_instruktur as pengganti'
                ,'jadwal_harians.status_jadwalHarian'
                ,'booking_kelas.id'
                ,'booking_kelas.no_booking'
                ,'kelas.nama_kelas'
                ,'kelas.id as id_kelas'
                , 'jadwal_harians.tanggal'
                , 'booking_kelas.cancel'
                ,'jadwal_umums.jam_kelas'
                , 'instrukturs.nama_instruktur')
        ->where('jadwal_harians.tanggal', Carbon::today())
        ->where('booking_kelas.cancel', false)
        ->get();


        return response()->json([
        'success' => true,
        'message' => 'Daftar Booking Kelas',
        'data' => $indexBookingKelas,
        ], 200);

    }

    public function presensiHadir($id){
        if(self::cekPresensi($id)){
            return response()->json([
                'success' => false,
                'cekPresensi'=>true,
                'message' => 'Instruktur Belum Presensi',
                'data' => null,
            ], 400);
        }

        $bookingKelas=BookingKelas::find($id);
       if($bookingKelas->id_depositKelasM === null){
        $member=User::find($bookingKelas->id_member);
        $kelas = Kelas::find($bookingKelas->id_kelas);
        $member->deposit_uang_member = $member->deposit_uang_member - $kelas->harga_kelas;
        $bookingKelas->waktu_presensi_kelas=Carbon::now();
       
       }else{
        $member=User::find($bookingKelas->id_member);
        $depositKelasM = depositKelas_Member::find($bookingKelas->id_depositKelasM);
        $depositKelasM->sisa_depositK = $depositKelasM->sisa_depositK - 1;
        $bookingKelas->waktu_presensi_kelas=Carbon::now();
       
       }
       $bookingKelas->status_presensi=1;
       if($bookingKelas->save()){

        if($bookingKelas->id_depositKelasM === null){           
            $member->save();
        }else{
            $depositKelasM->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'Berhasil',
            'data' => $bookingKelas
        ], 200);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Gagal',
            'data' => null
        ], 400);
    }

        
    }

    public function presensiTidakHadir($id){
        if(self::cekPresensi($id)){
            return response()->json([
                'success' => false,
                'cekPresensi'=>true,
                'message' => 'Instruktur Belum Presensi',
                'data' => null,
            ], 400);
        }

        $bookingKelas=BookingKelas::find($id);
       if($bookingKelas->id_depositKelasM === null){
        $member=User::find($bookingKelas->id_member);
        $kelas = Kelas::find($bookingKelas->id_kelas);
        $member->deposit_uang_member = $member->deposit_uang_member - $kelas->harga_kelas;
        $bookingKelas->waktu_presensi_kelas=Carbon::now();
       
       }else{
        $member=User::find($bookingKelas->id_member);
        $depositKelasM = depositKelas_Member::find($bookingKelas->id_depositKelasM);
        $depositKelasM->sisa_depositK = $depositKelasM->sisa_depositK - 1;
        $bookingKelas->waktu_presensi_kelas=Carbon::now();
       
       }
       $bookingKelas->status_presensi=0;
       if($bookingKelas->save()){

        if($bookingKelas->id_depositKelasM === null){           
            $member->save();
        }else{
            $depositKelasM->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'Berhasil',
            'data' => $bookingKelas
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
