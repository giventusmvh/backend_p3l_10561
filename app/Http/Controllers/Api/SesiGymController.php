<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SesiGym;
use Illuminate\Http\Request;

class SesiGymController extends Controller
{
    public function index(Request $request){
        $sesi = SesiGym::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Sesi',
            'data' => $sesi
        ], 200);
    }

    public function show($id){
        $instruktur = SesiGym::find($id);

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
