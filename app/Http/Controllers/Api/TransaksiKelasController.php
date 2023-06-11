<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Promo;
use Illuminate\Http\Request;
use App\Models\TransaksiKelas;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\depositKelas_Member;
use App\Models\Kelas;

use function PHPUnit\Framework\isNull;

class TransaksiKelasController extends Controller
{
    public function index()
    {

        
        $indexKelas = TransaksiKelas::join('users', 'transaksi_kelass.id_member_kelas', '=', 'users.id')
                        ->join('kelas', 'transaksi_kelass.id_kelas', '=', 'kelas.id')
                        ->select('transaksi_kelass.id'
                        ,'transaksi_kelass.no_struk_kelas'
                        ,'transaksi_kelass.id_promo_kelas'
                        ,'transaksi_kelass.id_pegawai_kelas'
                        ,'transaksi_kelass.id_member_kelas'
                        ,'transaksi_kelass.id_kelas'
                        ,'transaksi_kelass.tgl_TransaksiKelas'
                        ,'transaksi_kelass.jumlah_bayar_kelas'
                        ,'transaksi_kelass.total_pembayaran_kelas'
                        ,'transaksi_kelass.bonus_deposit_kelas'
                        ,'transaksi_kelass.total_deposit_kelas'
                        ,'transaksi_kelass.sisa_deposit_kelas'
                        ,'transaksi_kelass.masa_berlaku_depositKelas'
                        ,'kelas.harga_kelas'
                        ,'kelas.nama_kelas'
                        , 'users.nama_member')
                        ->orderBy('transaksi_kelass.tgl_TransaksiKelas', 'desc')
                        ->get();

       

                        return response()->json([
                            'success' => true,
                            'message' => 'Daftar Transaksi Uang',
                            'data' => $indexKelas,
                        ], 200);

    }


    public function show($id){
        $transaksiKelas = TransaksiKelas::find($id);

        if(!is_null($transaksiKelas)){
            return response([
                'message' => 'Retrieve Transaksi Kelas Success',
                'data' => $transaksiKelas
            ], 200);
        } 

        return response([
            'message' => 'Transaksi Kelas Not Found',
            'data' => null
        ], 400); 
    }

    public function store(Request $request)
    {
        $carbon=\Carbon\Carbon::now();
        $dateY=$carbon->format('y');
        $dateM=$carbon->format('m');
        $totalKelas=sprintf('%03d',(TransaksiKelas::all()->count())+1);
        $stringKode=$dateY.'.'.$dateM.'.'.$totalKelas;
        $storeData=$request->all();
        $validate=Validator::make($storeData,[   
            'id_pegawai_kelas'=>'required',
            'id_member_kelas'=>'required',
            'id_promo_kelas'=>'required',
            'id_kelas'=>'required',  
            'jumlah_bayar_kelas'=>'required',   
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);
    
        $member = User::find($request->id_member_kelas);
        $promo = Promo::find($request->id_promo_kelas);

        //mencari apakah member sudah pernah deposit kelas atau belum
        $dataDepoBaru=depositKelas_Member::find($request->id_member_kelas);
        $kelas = Kelas::find($request->id_kelas);


        //mencari apakah member tersebut sudah pernah deposit ke kelas tertentu atau belum
        $cekKondisi1=depositKelas_Member::where('id_member',$request->id_member_kelas)
                    ->where('id_kelas',$request->id_kelas)->first();

        if(!is_null($cekKondisi1)){
            if($cekKondisi1->sisa_depositK!=0){
                return response([
                    'belumhabis'=>true,
                    'message' => 'Deposit Kelas belum habis, tidak bisa menambah deposit',
                    'data' => null
                ], 400); 
            }else{
                if($storeData['id_promo_kelas']==2){
                    if($storeData['jumlah_bayar_kelas']==$promo->minimal_pembelian){  
                        $storeData['total_pembayaran_kelas']=$storeData['jumlah_bayar_kelas']*$kelas->harga_kelas;
                        $storeData['bonus_deposit_kelas']=$promo->bonus_promo;
                        $storeData['total_deposit_kelas']=$storeData['jumlah_bayar_kelas']+$storeData['bonus_deposit_kelas'];
                        $storeData['sisa_deposit_kelas']=$cekKondisi1->sisa_depositK;
                        $transaksiKelas=TransaksiKelas::create($storeData+['masa_berlaku_depositKelas'=>Carbon::now()->addMonths(1)]+['no_struk_kelas'=>$stringKode]+['tgl_TransaksiKelas'=>$carbon]);
                        $cekKondisi1->id_member=$storeData['id_member_kelas'];
                        $cekKondisi1->id_kelas=$storeData['id_kelas'];
                        $cekKondisi1->masa_berlaku_depositK=Carbon::now()->addMonths(1);
                        $cekKondisi1->sisa_depositK=$storeData['total_deposit_kelas'];
                        $cekKondisi1->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Berhasil Menambah Transaksi Kelas',
                            'data' => $transaksiKelas
                        ], 200);

                    }else{
                        return response()->json([
                            'promoerror'=>true,
                            'success' => false,
                            'message' => 'Tidak Sesuai Promo',
                            'data' => null
                        ], 400);
                    }
                }else if($storeData['id_promo_kelas']==3){
                    if($storeData['jumlah_bayar_kelas']==$promo->minimal_pembelian){  
                        $storeData['total_pembayaran_kelas']=$storeData['jumlah_bayar_kelas']*$kelas->harga_kelas;
                        $storeData['bonus_deposit_kelas']=$promo->bonus_promo;
                        $storeData['total_deposit_kelas']=$storeData['jumlah_bayar_kelas']+$storeData['bonus_deposit_kelas'];
                        $storeData['sisa_deposit_kelas']=$cekKondisi1->sisa_depositK;
                        $transaksiKelas=TransaksiKelas::create($storeData+['masa_berlaku_depositKelas'=>Carbon::now()->addMonths(2)]+['no_struk_kelas'=>$stringKode]+['tgl_TransaksiKelas'=>$carbon]);
                        $cekKondisi1->id_member=$storeData['id_member_kelas'];
                        $cekKondisi1->id_kelas=$storeData['id_kelas'];
                        $cekKondisi1->masa_berlaku_depositK=Carbon::now()->addMonths(2);
                        $cekKondisi1->sisa_depositK=$storeData['total_deposit_kelas'];
                        $cekKondisi1->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Berhasil Menambah Transaksi Kelas',
                            'data' => $transaksiKelas
                        ], 200);

                    }else{
                        return response()->json([
                            'promoerror'=>true,
                            'success' => false,
                            'message' => 'GAGAL',
                            'data' => null
                        ], 400);
                    }
                }else if($storeData['id_promo_kelas']==4){   
                    
                    $storeData['total_pembayaran_kelas']=$storeData['jumlah_bayar_kelas']*$kelas->harga_kelas;
                        $storeData['bonus_deposit_kelas']=$promo->bonus_promo;
                        $storeData['total_deposit_kelas']=$storeData['jumlah_bayar_kelas']+$storeData['bonus_deposit_kelas'];
                        $storeData['sisa_deposit_kelas']=$storeData['total_deposit_kelas'];
                        $transaksiKelas=TransaksiKelas::create($storeData+['masa_berlaku_depositKelas'=>Carbon::now()->addMonths(1)]+['no_struk_kelas'=>$stringKode]+['tgl_TransaksiKelas'=>$carbon]);
                        $cekKondisi1->id_member=$storeData['id_member_kelas'];
                        $cekKondisi1->id_kelas=$storeData['id_kelas'];
                        $cekKondisi1->masa_berlaku_depositK=Carbon::now()->addMonths(1);
                        $cekKondisi1->sisa_depositK=$storeData['total_deposit_kelas'];
                        $cekKondisi1->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Berhasil Menambah Transaksi Kelas',
                            'data' => $transaksiKelas
                        ], 200);  
                
                }
            }
        }else if($storeData['id_promo_kelas']!=4){
            if($storeData['id_promo_kelas']==2){
                if($storeData['jumlah_bayar_kelas']==$promo->minimal_pembelian){
                    $storeData['total_pembayaran_kelas']=$storeData['jumlah_bayar_kelas']*$kelas->harga_kelas;
                    $dataDepoKelasM = new depositKelas_Member();
                    $storeData['bonus_deposit_kelas']=$promo->bonus_promo;
                    $storeData['total_deposit_kelas']=$storeData['jumlah_bayar_kelas']+$storeData['bonus_deposit_kelas'];
                    $storeData['sisa_deposit_kelas']=$storeData['total_deposit_kelas'];
                    $transaksiKelas=TransaksiKelas::create($storeData+['masa_berlaku_depositKelas'=>Carbon::now()->addMonths(1)]+['no_struk_kelas'=>$stringKode]+['tgl_TransaksiKelas'=>$carbon]);
                    $dataDepoKelasM->id_member=$storeData['id_member_kelas'];
                    $dataDepoKelasM->id_kelas=$storeData['id_kelas'];
                    $dataDepoKelasM->masa_berlaku_depositK=Carbon::now()->addMonths(1);
                    $dataDepoKelasM->sisa_depositK=$storeData['total_deposit_kelas'];
                    $dataDepoKelasM->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil Menambah Transaksi Kelas',
                        'data' => $transaksiKelas
                    ], 200);

                }else{
                    return response()->json([
                        'promoerror'=>true,
                        'success' => false,
                        'message' => 'Tidak Sesuai dengan Promo',
                        'data' => null
                    ], 400);
                }
                
            }else if($storeData['id_promo_kelas']==3){
                if($storeData['jumlah_bayar_kelas']==$promo->minimal_pembelian){
                    $storeData['total_pembayaran_kelas']=$storeData['jumlah_bayar_kelas']*$kelas->harga_kelas;
                    $dataDepoKelasM = new depositKelas_Member();
                    $storeData['bonus_deposit_kelas']=$promo->bonus_promo;
                    $storeData['total_deposit_kelas']=$storeData['jumlah_bayar_kelas']+$storeData['bonus_deposit_kelas'];
                    $storeData['sisa_deposit_kelas']=$storeData['total_deposit_kelas'];
                    $transaksiKelas=TransaksiKelas::create($storeData+['masa_berlaku_depositKelas'=>Carbon::now()->addMonths(2)]+['no_struk_kelas'=>$stringKode]+['tgl_TransaksiKelas'=>$carbon]);
                    $dataDepoKelasM->id_member=$storeData['id_member_kelas'];
                    $dataDepoKelasM->id_kelas=$storeData['id_kelas'];
                    $dataDepoKelasM->masa_berlaku_depositK=Carbon::now()->addMonths(1);
                    $dataDepoKelasM->sisa_depositK=$storeData['total_deposit_kelas'];
                    $dataDepoKelasM->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil Menambah Transaksi Kelas',
                        'data' => $transaksiKelas
                    ], 200);

                }else{
                    return response()->json([
                        'promoerror'=>true,
                        'success' => false,
                        'message' => 'Tidak Sesuai dengan Promo',
                        'data' => null
                    ], 400);
                }
                
            }
        }else if($storeData['id_promo_kelas']==4){   
            $storeData['total_pembayaran_kelas']=$storeData['jumlah_bayar_kelas']*$kelas->harga_kelas;
                $dataDepoKelasM = new depositKelas_Member();
                    $storeData['bonus_deposit_kelas']=$promo->bonus_promo;
                    $storeData['total_deposit_kelas']=$storeData['jumlah_bayar_kelas']+$storeData['bonus_deposit_kelas'];
                    $storeData['sisa_deposit_kelas']=$storeData['total_deposit_kelas'];
                    $transaksiKelas=TransaksiKelas::create($storeData+['masa_berlaku_depositKelas'=>Carbon::now()->addMonths(1)]+['no_struk_kelas'=>$stringKode]+['tgl_TransaksiKelas'=>$carbon]);
                    $dataDepoKelasM->id_member=$storeData['id_member_kelas'];
                    $dataDepoKelasM->id_kelas=$storeData['id_kelas'];
                    $dataDepoKelasM->masa_berlaku_depositK=Carbon::now()->addMonths(1);
                    $dataDepoKelasM->sisa_depositK=$storeData['total_deposit_kelas'];
                    $dataDepoKelasM->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil Menambah Transaksi Kelas',
                        'data' => $transaksiKelas
                    ], 200);  
            
        }

          
    }
}
