<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Instruktur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function loginWeb(Request $request){
        $loginData = $request->all();
        if(Pegawai::where('email', '=', $loginData['email'])->first()){
            $pegawai = Pegawai::where('email' , '=', $loginData['email'])->first();

            if(Hash::check($loginData['password'], $pegawai['password'])){
                $token = Str::random(80);
    
                $pegawai->api_token = hash('sha256', $token);
                $pegawai->save();
    
                return response()->json([
                    'success'=>true,
                    'message' => 'Pegawai Authenticated',
                    'user' => $pegawai,
                    'token_type' => 'Bearer',
                    'token' => $token,
                ]); // return data user dan token dalam bentuk json
            }else {
                
                return response()->json([
                    'success'=>false,
                    'message' =>' Wrong Email or Password',
                ], 400);
            }
        }
        
    }

    public function loginMobile(Request $request){
        $loginData = $request->all();
        if(Pegawai::where('email', '=', $loginData['email'])->first()){
            $pegawai = Pegawai::where('email' , '=', $loginData['email'])->first();

            if($pegawai->role=='manager'){
                if(Hash::check($loginData['password'], $pegawai['password'])){
                    $token = Str::random(80);
        
                    $pegawai->api_token = hash('sha256', $token);
                    $pegawai->save();
        
                    return response()->json([
                        'id'=>$pegawai->id,
                        'manager'=>true,
                        'success'=>true,
                        'message' => 'Pegawai Authenticated',
                        'user' => $pegawai,
                        'token_type' => 'Bearer',
                        'token' => $token,
                    ]); // return data user dan token dalam bentuk json
                }else{
                    
                    return response()->json([
                        'success'=>false,
                        'message' =>' Wrong Email or Password',
                    ], 400);
                }
            }else{
                return response()->json([
                    'success'=>false,
                    'message' =>'BUKAN MANAGER OPERASIONAL',
                ], 400);
            }

            
        }else if(Instruktur::where('email', '=', $loginData['email'])->first()){
            $instruktur = Instruktur::where('email' , '=', $loginData['email'])->first();

            if(Hash::check($loginData['password'], $instruktur['password'])){
                $token = Str::random(80);
    
                $instruktur->api_token = hash('sha256', $token);
                $instruktur->save();
    
                return response()->json([
                    'id'=>$instruktur->id,
                    'instruktur'=>true,
                    'success'=>true,
                    'message' => 'Instruktur Authenticated',
                    'user' => $instruktur,
                    'token_type' => 'Bearer',
                    'token' => $token,
                ]); // return data user dan token dalam bentuk json
            }else{
                
                return response()->json([
                    'success'=>false,
                    'message' =>' Wrong Email or Password',
                ], 400);
            }
        }else if(User::where('email', '=', $loginData['email'])->first()){
            $member = User::where('email' , '=', $loginData['email'])->first();

            if(Hash::check($loginData['password'], $member['password'])){
                $token = Str::random(80);
    
                $member->api_token = hash('sha256', $token);
                $member->save();
    
                return response()->json([
                    'id'=>$member->id,
                    'member'=>true,
                    'success'=>true,
                    'message' => 'User / Member Authenticated',
                    'user' => $member,
                    'token_type' => 'Bearer',
                    'token' => $token,
                ]); // return data user dan token dalam bentuk json
            }else{
                
                return response()->json([
                    'success'=>false,
                    'message' =>' Wrong Email or Password',
                ], 400);
            }
        }
        
    }
}
