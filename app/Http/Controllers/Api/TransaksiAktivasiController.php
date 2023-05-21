<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Instruktur;
use Illuminate\Http\Request;
use App\Models\TransaksiAktivasi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransaksiAktivasiController extends Controller
{
    public function index()
    {

        $indexAktivasi = TransaksiAktivasi::join('users', 'transaksi_aktivasis.id_member_aktivasi', '=', 'users.id')
                        ->select('transaksi_aktivasis.id'
                        ,'transaksi_aktivasis.no_struk'
                        ,'transaksi_aktivasis.id_pegawai_aktivasi'
                        ,'transaksi_aktivasis.id_member_aktivasi'
                        ,'transaksi_aktivasis.tgl_TransaksiAktivasi'
                        ,'transaksi_aktivasis.jumlah_bayar_aktivasi'
                        ,'transaksi_aktivasis.masa_berlaku_aktivasi'
                        , 'users.nama_member')
                        
                        ->get();

       

                        return response()->json([
                            'success' => true,
                            'message' => 'Daftar Aktivasi',
                            'data' => $indexAktivasi,
                        ], 200);
    }


    public function show($id){
        $transaksiAktivasi = TransaksiAktivasi::find($id);

        if(!is_null($transaksiAktivasi)){
            return response([
                'message' => 'Retrieve Transaksi Aktivasi Success',
                'data' => $transaksiAktivasi
            ], 200);
        } 

        return response([
            'message' => 'Transaksi Aktivasi Not Found',
            'data' => null
        ], 400); 
    }

    public function store(Request $request)
    {
        $carbon=\Carbon\Carbon::now();
        $expiredDate = date('Y-m-d', strtotime('+1 year', strtotime($carbon)));
        $dateY=$carbon->format('y');
        $dateM=$carbon->format('m');
        $totalAktivasi=sprintf('%03d',(TransaksiAktivasi::all()->count())+1);
        $stringKode=$dateY.'.'.$dateM.'.'.$totalAktivasi;
        $storeData=$request->all();
        $validate=Validator::make($storeData,[
            'id_pegawai_aktivasi'=>'required',
        'id_member_aktivasi'=>'required',
        'jumlah_bayar_aktivasi'=>'required',
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);
    
            $transaksiAktivasi=TransaksiAktivasi::create($storeData+['masa_berlaku_aktivasi'=>$expiredDate]+['no_struk'=>$stringKode]+['tgl_TransaksiAktivasi'=>$carbon]);
            

        $member = User::find($request->id_member_aktivasi);
        $member->tgl_expired_member = $expiredDate;
        $member->status_member = 1;
       
        if($member->save()){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil membuat transaksi aktivasi',
                'data' => $transaksiAktivasi,
            ], 200);
        }else{
            return response()->json([
                'success' => false,
            ], 400);
        }

    }
}
