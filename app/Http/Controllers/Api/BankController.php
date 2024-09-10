<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
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
        //set validation
        $validator = Validator::make($request->all(), [
            'bank_name'  => 'required',
            'bank_code'  => 'required|unique:banks,bank_code',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $bank = Bank::create([
            'bank_name'   => $request->bank_name,
            'bank_code'   => $request->bank_code,
        ]);

        if ($bank) {
            return response()->json([
                'success' => true,
                'data'    => $bank,
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
}
