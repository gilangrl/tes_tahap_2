<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\RekeningAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RekeningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Validasi input
        $validate = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'no_rekening' => 'required|string',
            'atas_nama' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        // Menyimpan rekening admin baru
        $rekeningAdmin = RekeningAdmin::create($request->all());

        if ($rekeningAdmin) {
            return response()->json([
                'success' => true,
                'data'    => $rekeningAdmin,
            ], 201);
        }

        
    }
}
