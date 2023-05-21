<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawais=Pegawai::all();

        if(count($pegawais)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$pegawais
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data'=>null
        ],400);
    }


    public function show($id){
        $pegawai = Pegawai::find($id);

        if(!is_null($pegawai)){
            return response([
                'message' => 'Retrieve Pegawai Success',
                'data' => $pegawai
            ], 200);
        } 

        return response([
            'message' => 'Pegawai Not Found',
            'data' => null
        ], 400); 
    }

    public function store(Request $request)
    {
        $totalPegawai=sprintf('%02d',(Pegawai::all()->count())+1);
        $stringKode='P'.'-'.$totalPegawai;
        $storeData=$request->all();
        $validate=Validator::make($storeData,[
            'nama_pegawai'=>'required|max:60|unique:pegawais',
            'alamat_pegawai'=>'required|max:60',
            'telp_pegawai'=>'required',
            'role'=>'required',
            'password'=>'required',
            'tgl_lahir_pegawai'=>'required|date',
            'email'=>'required|email:rfc,dns'
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);
        
        $storeData['password'] = Hash::make($request->password);
            $pegawai=Pegawai::create($storeData+['id_pegawai'=>$stringKode]);
            
            // $pegawai=Pegawai::create($storeData);
            return response([
                'message'=>'add pegawai success',
                'data'=>$pegawai
            ],200);
    }

    public function update(Request $request, $id){
        $pegawai = Pegawai::find($id); 
        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Not Found',
                'data' => null
            ], 404);
        } 
        
        $updateData = $request->all(); 
        $validate = Validator::make($updateData, [
            'nama_pegawai'=>'required|max:60|unique:pegawais',
            'alamat_pegawai'=>'required|max:60',
            'telp_pegawai'=>'required',
            'role'=>'required',
            'password'=>'required',
            'tgl_lahir_pegawai'=>'required|date',
            'email'=>'required|email:rfc,dns'
            
        ]); //membuat rule validasi input

        if($validate->fails())
        return response()->json($validate->errors(), 400);

        $updateData['password'] = Hash::make($request->password);
        $pegawai->nama_pegawai = $updateData['nama_pegawai'];
        $pegawai->alamat_pegawai = $updateData['alamat_pegawai'];   
        $pegawai->telp_pegawai = $updateData['telp_pegawai']; 
        $pegawai->role = $updateData['role'];  
        $pegawai->password_ = $updateData['password_'];  
        $pegawai->tgl_lahir_pegawai = $updateData['tgl_lahir_pegawai'];      
        $pegawai->email = $updateData['email'];     
        if($pegawai->save()){
            return response()->json([               
                'message' => 'Update Pegawai Success!',
                'pegawai'    => $pegawai  
            ],200);
        }
        return response()->json([
            'message' => 'Update Pegawai Failed',
            'data' => null
        ], 400); 
    }

    public function ubahPassword(Request $request,$id){
        $pegawai = Pegawai::find($id);
        if(is_null($pegawai)){
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan',
                'data' => null
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 400);
        }
        
            $pegawai->password = bcrypt($request->password);
            if($pegawai->save()){
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mengubah password',
                    'data' => $pegawai
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah password',
                    'data' => null
                ], 400);
            }
        
    }

}
