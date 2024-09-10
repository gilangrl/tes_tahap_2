<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        // Validasi salah
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mengambil request hanya email dan password
        $credentials = $request->only('email', 'password');

        // Kondisi jika auth salah
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        // Generate refresh token
        $refreshToken = $this->generateRefreshToken($credentials);

        // Kondisi jika auth sukses
        return response()->json([
            'success' => true,
            // 'user'    => auth()->guard('api')->user(),
            'accessToken'  => $token,
            'refreshToken' => $refreshToken
        ], 200);
    }

    private function generateRefreshToken($credentials)
    {
        $refreshToken = JWTAuth::fromUser(auth()->guard('api')->user(), ['type' => 'refresh']);
        return $refreshToken;
    }

    public function updateToken(Request $request)
    {
        // Set validation
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        // Validasi salah
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil refresh token dari request
        $refreshToken = $request->input('token');

        // Verifikasi refresh token dan ambil user
        $user = $this->verifyRefreshToken($refreshToken);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token tidak valid'
            ], 401);
        }

        // Generate token akses baru
        $accessToken = auth()->guard('api')->login($user);

        // Generate refresh token baru
        $newRefreshToken = $this->generateRefreshToken($user);

        return response()->json([
            'accessToken'  => $accessToken,
            'refreshToken' => $newRefreshToken
        ], 200);
    }

    private function verifyRefreshToken($refreshToken)
    {
        // Implementasi untuk memverifikasi refresh token
        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
            return User::find($payload->get('sub'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
