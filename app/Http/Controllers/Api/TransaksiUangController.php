<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Promo;
use Illuminate\Http\Request;
use App\Models\TransaksiUang;
use App\Models\TransaksiAktivasi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class TransaksiUangController extends Controller
{
    public function index()
    {

        $indexUang = TransaksiUang::join('users', 'transaksi_uangs.id_member_uang', '=', 'users.id')
                        ->select('transaksi_uangs.id'
                        ,'transaksi_uangs.no_struk_uang'
                        ,'transaksi_uangs.id_promo_uang'
                        ,'transaksi_uangs.id_pegawai_uang'
                        ,'transaksi_uangs.id_member_uang'
                        ,'transaksi_uangs.tgl_TransaksiUang'
                        ,'transaksi_uangs.jumlah_bayar_uang'
                        ,'transaksi_uangs.bonus_deposit_uang'
                        ,'transaksi_uangs.total_deposit_uang'
                        ,'transaksi_uangs.sisa_deposit_uang'
                        , 'users.nama_member')
                        
                        ->get();

       

                        return response()->json([
                            'success' => true,
                            'message' => 'Daftar Transaksi Uang',
                            'data' => $indexUang,
                        ], 200);

        
    }


    public function show($id){
        $transaksiUangs = TransaksiUang::find($id);

        if(!is_null($transaksiUangs)){
            return response([
                'message' => 'Retrieve Transaksi Uang Success',
                'data' => $transaksiUangs
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
        $dateY=$carbon->format('y');
        $dateM=$carbon->format('m');
        $totalTransaksiUang=sprintf('%03d',(TransaksiUang::all()->count())+1);
        $stringKode=$dateY.'.'.$dateM.'.'.$totalTransaksiUang;
        $storeData=$request->all();
        $validate=Validator::make($storeData,[
            
            'id_member_uang'=>'required',
           
            'jumlah_bayar_uang'=>'required|integer|min:500000',
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);
    
            
            

        $member = User::find($request->id_member_uang);
        $promo = Promo::find($request->id_promo_uang);
        $storeData['sisa_deposit_uang'] = $member->deposit_uang_member;
        if($storeData['jumlah_bayar_uang']>=$promo->minimal_pembelian){
            $eligibleSpent = floor($storeData['jumlah_bayar_uang'] / $promo->minimal_pembelian) * $promo->minimal_pembelian; 
            $storeData['bonus_deposit_uang'] = floor($eligibleSpent / $promo->minimal_pembelian) * $promo->bonus_promo; 
            $storeData['total_deposit_uang'] = $storeData['bonus_deposit_uang'] + $storeData['jumlah_bayar_uang']+$storeData['sisa_deposit_uang'];
            $member->deposit_uang_member = $storeData['total_deposit_uang'];
            $storeData['sisa_deposit_uang']=$storeData['total_deposit_uang'];
            $transaksiUang=TransaksiUang::create($storeData+['no_struk_uang'=>$stringKode]+['tgl_TransaksiUang'=>$carbon]);
            if($member->save()){
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat transaksi uang dengan promo',
                    'data' => $transaksiUang,
                ], 200);
            }else{
               
                return response()->json([
                    'success' => false,
                ], 400);
            }
        }else{
            $storeData['bonus_deposit_uang'] = 0;
            $storeData['total_deposit_uang'] = $storeData['bonus_deposit_uang'] + $storeData['jumlah_bayar_uang']+$storeData['sisa_deposit_uang'];
            $member->deposit_uang_member = $storeData['total_deposit_uang'];
            $storeData['sisa_deposit_uang']=$storeData['total_deposit_uang'];
            $transaksiUang=TransaksiUang::create($storeData+['no_struk_uang'=>$stringKode]+['tgl_TransaksiUang'=>$carbon]);
            if($member->save()){
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat transaksi uang tanpa promo',
                    'data' => $transaksiUang,
                ], 200);
            }else{
               
                return response()->json([
                    'success' => false,
                ], 400);
            }
        }
        
        

    }
}