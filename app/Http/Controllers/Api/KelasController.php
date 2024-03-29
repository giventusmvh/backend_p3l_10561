<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index(Request $request){
        $kelas = Kelas::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Kelas',
            'data' => $kelas
        ], 200);
    }

    public function show($id){
        $kelas = Kelas::find($id);

        if(!is_null($kelas)){
            return response([
                'message' => 'Retrieve Kelas Success',
                'data' => $kelas
            ], 200);
        } 

        return response([
            'message' => 'Kelas Not Found',
            'data' => null
        ], 400); 
    }

}
