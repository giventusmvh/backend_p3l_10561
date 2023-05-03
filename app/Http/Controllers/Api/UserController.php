<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function index()
    {
        $users=User::all();

        if(count($users)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$users
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data'=>null
        ],400);
    }


    public function show($id){
        $user = User::find($id);

        if(!is_null($user)){
            return response([
                'message' => 'Retrieve User Success',
                'data' => $user
            ], 200);
        } 

        return response([
            'message' => 'User Not Found',
            'data' => null
        ], 400); 
    }

    public function store(Request $request)
    {
        $carbon=\Carbon\Carbon::now();
        $dateY=$carbon->format('y');
        $dateM=$carbon->format('m');
        $totalMember=sprintf('%02d',(User::all()->count())+1);
        $stringKode=$dateY.'.'.$dateM.'.'.$totalMember;
        // $expiredDate = date('Y-m-d', strtotime('+1 year', strtotime($carbon)));
        $storeData=$request->all();
        $validate=Validator::make($storeData,[
            'nama_member'=>'required|max:60',
            'email'=>'required|email:rfc,dns',
            'tgl_lahir_member'=>'required|date',
            'jk_member'=>'required',
            'telp_member'=>'required',
            'alamat_member'=>'required',
            'deposit_uang_member'=>'required',
            // 'status_member'=>'required',
            // 'status_member'=>'required',
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);

            // 
            $tgllahir=Carbon::parse($storeData['tgl_lahir_member'])->format('dmY');
            $password=Hash::make($tgllahir);
            $storeData['password']=$password;
            $User=User::create($storeData+['id_member'=>$stringKode]);
            //+['tgl_expired_member'=>$expiredDate]
            // $User=User::create($storeData);
            return response([
                'message'=>'add User success',
                'data'=>$User
            ],200);
    }

    public function update(Request $request, $id){
        $user = User::find($id); 
        if(is_null($user)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        } 
        
        $updateData = $request->all(); 
        $validate = Validator::make($updateData, [
            'nama_member'=>'required|max:60',
            'email'=>'required|email:rfc,dns',
            'tgl_lahir_member'=>'required|date',
            'jk_member'=>'required',
            'telp_member'=>'required',
            'alamat_member'=>'required',
            'password'=>'required',
            // 'tgl_expired_member'=>'required|date',
            'deposit_uang_member'=>'required',
            // 'status_member'=>'required',
            
        ]); //membuat rule validasi input

        if($validate->fails())
        return response()->json($validate->errors(), 400);

        $updateData['password'] = Hash::make($request->password);
        $user->nama_member = $updateData['nama_member'];
        $user->email = $updateData['email'];   
        $user->tgl_lahir_member = $updateData['tgl_lahir_member']; 
        $user->jk_member = $updateData['jk_member']; 
        $user->telp_member = $updateData['telp_member'];  
        $user->alamat_member = $updateData['alamat_member'];     
        $user->password = $updateData['password'];     
        // $user->tgl_expired_member = $updateData['tgl_expired_member'];     
        $user->deposit_uang_member = $updateData['deposit_uang_member'];     
        // $user->status_member = $updateData['status_member'];     
        if($user->save()){
            return response()->json([               
                'message' => 'Update User Success!',
                'user'    => $user  
            ],200);
        }
        return response()->json([
            'message' => 'Update User Failed',
            'data' => null
        ], 400); 
    }

    public function destroy($id)
    {
        $user=User::find($id);

        if(is_null($user)){
            return response([
                'message'=>'User not found',
                'data'=>null
            ],404);
        }

        if($user->delete()){
            return response([
                'message'=>'delete User success',
                'data'=>$user
            ],200);
        }

        return response([
            'message'=>'delete User failed',
            'data'=>null
        ],400);
    }

    public function resetPassword($id)
    {
        $user = User::find($id); 
        if(is_null($user)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        } 
        
        $updatePassword = $user->password;
        $tgllahir=$user->tgl_lahir_member;
        $tgllahirformatted=Carbon::parse( $tgllahir)->format('dmY');
        $password=Hash::make($tgllahirformatted);
        $updatePassword = $password;
        $user->password = $updatePassword; 
        if($user->save()){
            return response()->json([               
                'message' => 'Reset Password Success!',
                'user'    => $user  
            ],200);
        }
        return response()->json([
            'message' => 'Reset Password Failed',
            'data' => null
        ], 400); 
    }
}
