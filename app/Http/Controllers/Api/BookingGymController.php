<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BookingGym;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BookingGymController extends Controller
{
    public function cekAktivasi(Request $request){
        $member = User::find($request->id_member);
        if(is_null($member) || $member->status_member == 0){
           return true;
       }else{
           return false;
       }
    }

    public function cekKuota(Request $request){
        $booking = BookingGym::where('id_sesi', $request->id_sesi)
            ->where('tgl_booking', $request->tgl_booking)
            ->where('cancel', false)
            ->count();
        if($booking >= 10){
            return true;
        }else{
            return false;
        }
    }

    public function cekSudahBooking(Request $request){
        $booking = BookingGym::where('id_member', $request->id_member)
            ->where('tgl_booking', $request->tgl_booking)
            ->where('cancel', false)
            ->count();
        if($booking >= 1){
            return true;
        }else{
            return false;
        }
    }

    public function store(Request $request){
        if(self::cekAktivasi($request)){
            return response()->json([
                'gagalaktivasi'=>true,
                'success' => false,
                'message' => 'Cek Aktivasi Anda',
                'data' => null
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'id_sesi' => 'required',
            'tgl_booking' => 'required|date_format:Y-m-d',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }
        if(self::cekKuota($request)){
            return response()->json([
                'gagalkuota'=>true,
                'success' => false,
                'message' => 'Kuota Sesi Tersebut Penuh',
                'data' => null
            ], 400);
        }
        if(self::cekSudahBooking($request)){
            return response()->json([
                'gagalpernahbooking'=>true,
                'success' => false,
                'message' => 'Anda sudah booking hari tersebut',
                'data' => null
            ], 400);
        }
        $booking = new BookingGym();
        $carbon=\Carbon\Carbon::now();
        $dateY=$carbon->format('y');
        $dateM=$carbon->format('m');
        $totalAktivasi=sprintf('%03d',(BookingGym::all()->count())+1);
        $stringKode=$dateY.'.'.$dateM.'.'.$totalAktivasi;
        $booking->no_booking= $stringKode;
        $booking->id_member = $request->id_member;
        $booking->id_sesi = $request->id_sesi;
        $booking->tgl_booking = $request->tgl_booking;
        if($booking->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil booking',
                'data' => $booking
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal booking',
                'data' => null
            ], 400);
        }
    }

    public function cancel(Request $request){
        $booking = BookingGym::find($request->id);
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
        if(Carbon::now()->format('Y-m-d') >= $booking->tgl_booking){
            return response()->json([
                'telat'=>true,
                'success' => false,
                'message' => 'Sudah tidak bisa dibatalkan',
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

    //show by user
    public function show($id){

        $indexBookingKelas = BookingGym::join('sesi_gyms', 'sesi_gyms.id', '=', 'booking_gyms.id_sesi')
                            ->join('users', 'users.id', '=', 'booking_gyms.id_member')
                            ->orderBy('booking_gyms.created_at', 'desc')
                            ->select('booking_gyms.id','booking_gyms.id_member','booking_gyms.no_booking','booking_gyms.tgl_booking', 'booking_gyms.id_sesi'
                            ,'booking_gyms.waktu_presensi_gym'
                            ,'booking_gyms.cancel'
                            ,'booking_gyms.created_at'
                            ,'sesi_gyms.jam_mulai'
                            ,'sesi_gyms.jam_selesai'
                            ,'users.nama_member')
                            ->where('booking_gyms.id_member', $id)
                            ->where('booking_gyms.cancel', false)
                            ->get();
            
    
            return response()->json([
                'success' => true,
                'message' => 'Daftar Booking Gym',
                'data' => $indexBookingKelas,
            ], 200);
        
        
    }

    //show all by date
    public function index(){

        $indexBookingGym = BookingGym::join('sesi_gyms', 'sesi_gyms.id', '=', 'booking_gyms.id_sesi')
                            ->join('users', 'users.id', '=', 'booking_gyms.id_member')
                            ->orderBy('booking_gyms.created_at', 'desc')
                            ->select('booking_gyms.id'
                            ,'booking_gyms.id_member'
                            ,'booking_gyms.no_booking'
                            ,'booking_gyms.tgl_booking'
                            ,'booking_gyms.id_sesi'
                            ,'booking_gyms.waktu_presensi_gym'
                            ,'booking_gyms.cancel'
                            ,'booking_gyms.created_at'
                            ,'sesi_gyms.jam_mulai'
                            ,'sesi_gyms.jam_selesai'
                            ,'users.nama_member')
                            ->where('booking_gyms.tgl_booking', Carbon::today())
                            ->where('booking_gyms.cancel', false)
                            ->get();
            
    
            return response()->json([
                'success' => true,
                'message' => 'Daftar Booking Gym',
                'data' => $indexBookingGym,
            ], 200);
        
        
    }

    public function showByID($id){
        $instruktur = BookingGym::find($id);

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

    public function presensi($id){
    
        $BookingGym = BookingGym::find($id);
        if(is_null($BookingGym)){
            return response()->json([
                'success' => false,
                'message' => 'Booking Gym tidak ditemukan',
                'data' => null
            ], 400);
        }
        $BookingGym->waktu_presensi_gym = Carbon::now();
        
        if($BookingGym->save()){
            return response()->json([
                'success' => true,
                'message' => 'berhasil',
                'data' => $BookingGym
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Booking Gym gagal presensi',
                'data' => null
            ], 400);
        }
    }
}
