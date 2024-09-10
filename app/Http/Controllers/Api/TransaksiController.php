<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Bank;
use Illuminate\Http\Request;
use App\Models\RekeningAdmin;
use App\Models\TransaksiTransfer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function __construct()
    {
        // Pastikan semua request ke controller ini terotentikasi menggunakan JWT
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
        $this->validate($request, [
            'nilai_transfer'    => 'required|numeric|min:1000',
            'bank_tujuan'       => 'required|string',
            'rekening_tujuan'   => 'required|string',
            'atasnama_tujuan'   => 'required|string',
            'bank_pengirim'     => 'required|string',
        ]);

        // Cek apakah bank pengirim dan bank tujuan terdaftar
        $bank_tujuan    = Bank::where('bank_name', $request->bank_tujuan)->first();
        $bank_pengirim  = Bank::where('bank_name', $request->bank_pengirim)->first();

        if (!$bank_tujuan || !$bank_pengirim) {
            return response()->json(['error' => 'Bank tidak terdaftar'], 400);
        }

        // Generate ID transaksi unik (TF{YYMMDD}{counter 5 digit})
        $counter = TransaksiTransfer::whereDate('created_at', Carbon::today())->count() + 1;
        $id_transaksi = 'TF' . Carbon::now()->format('ymd') . str_pad($counter, 5, '0', STR_PAD_LEFT);
        $kode_unik = rand(100, 999); // Kode unik random 3 angka

        // Simulasi rekening perantara (rekening admin)
        $rekening_admin = RekeningAdmin::where('bank_id', $bank_pengirim->id)->first();
        if (!$rekening_admin) {
            return response()->json(['error' => 'Rekening admin tidak ditemukan untuk bank pengirim'], 500);
        }

        // Biaya admin
        $biaya_admin = 0;

        // Total transfer
        $total_transfer = $request->nilai_transfer + $kode_unik + $biaya_admin;

        // Simpan transaksi ke database
        $transaksi = TransaksiTransfer::create([
            'id' => $id_transaksi,
            'user_id' => Auth::id(), // Ambil user yang sudah login
            'bank_tujuan_id' => $bank_tujuan->id,
            'rekening_tujuan' => $request->rekening_tujuan,
            'kode_unik' => $kode_unik,
            'jumlah_transfer' => $request->nilai_transfer,
        ]);

        // Response dengan format sesuai permintaan
        $response = [
            'id_transaksi' => $id_transaksi,
            'nilai_transfer' => $request->nilai_transfer,
            'kode_unik' => $kode_unik,
            'biaya_admin' => $biaya_admin,
            'total_transfer' => $total_transfer,
            'bank_perantara' => $bank_pengirim->bank_name,
            'rekening_perantara' => $rekening_admin->no_rekening,
            'berlaku_hingga' => Carbon::now()->addMinutes(30)->toIso8601String(),
        ];

        if ($transaksi) {
            return response()->json([
                'success' => true,
                'data'  => $response
            ], 201);
        }
    }
}
