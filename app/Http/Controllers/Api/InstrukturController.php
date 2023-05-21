<?php

namespace App\Http\Controllers\Api;

use App\Models\Instruktur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstrukturController extends Controller
{
    public function index()
    {
        $instrukturs=Instruktur::all();

        if(count($instrukturs)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$instrukturs
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data'=>null
        ],400);
    }


    public function show($id){
        $instruktur = Instruktur::find($id);

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

    public function store(Request $request)
    {
        $storeData=$request->all();
        $validate=Validator::make($storeData,[
            'nama_instruktur'=>'required|max:60|unique:instrukturs',
            'alamat_instruktur'=>'required',
            'tgl_lahir_instruktur'=>'required',
            'telp_instruktur'=>'required',
            'password'=>'required',
            'email'=>'required|email:rfc,dns'
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);
        
            $storeData['password'] = Hash::make($request->password);
            $Instruktur=Instruktur::create($storeData);
            
            // $Instruktur=Instruktur::create($storeData);
            return response([
                'message'=>'add Instruktur success',
                'data'=>$Instruktur
            ],200);
    }

    public function update(Request $request, $id){
        $instruktur = Instruktur::find($id); 
        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur Not Found',
                'data' => null
            ], 404);
        } 
        
        $updateData = $request->all(); 
        $validate = Validator::make($updateData, [
            'nama_instruktur'=>'required|max:60',
            'alamat_instruktur'=>'required',
            'tgl_lahir_instruktur'=>'required|date',
            'telp_instruktur'=>'required',
            'password'=>'required',
            'email'=>'required|email:rfc,dns'
            
        ]); //membuat rule validasi input

        if($validate->fails())
        return response()->json($validate->errors(), 400);

        $updateData['password'] = Hash::make($request->password);
        $instruktur->nama_instruktur = $updateData['nama_instruktur'];
        $instruktur->alamat_instruktur = $updateData['alamat_instruktur'];   
        $instruktur->tgl_lahir_instruktur = $updateData['tgl_lahir_instruktur']; 
        $instruktur->telp_instruktur = $updateData['telp_instruktur']; 
        $instruktur->password = $updateData['password'];  
        $instruktur->email = $updateData['email'];     
        if($instruktur->save()){
            return response()->json([               
                'message' => 'Update Instruktur Success!',
                'instruktur'    => $instruktur  
            ],200);
        }
        return response()->json([
            'message' => 'Update Instruktur Failed',
            'data' => null
        ], 400); 
    }

    //Ubah password Instruktur berdasarkan token
    public function ubahPassword(Request $request,$id){
        $instruktur = instruktur::find($id);
        if(is_null($instruktur)){
            return response()->json([
                'success' => false,
                'message' => 'Instruktur tidak ditemukan',
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
        
            $instruktur->password = bcrypt($request->password);
            if($instruktur->save()){
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mengubah password',
                    'data' => $instruktur
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah password',
                    'data' => null
                ], 400);
            }
        
    }


    public function destroy($id)
    {
        $instruktur=Instruktur::find($id);

        if(is_null($instruktur)){
            return response([
                'message'=>'Instruktur not found',
                'data'=>null
            ],404);
        }

        if($instruktur->delete()){
            return response([
                'message'=>'delete Instruktur success',
                'data'=>$instruktur
            ],200);
        }

        return response([
            'message'=>'delete Instruktur failed',
            'data'=>null
        ],400);
    }
}
