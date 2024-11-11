<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Mengambil data input
        $credentials = $request->only('email', 'password');

        // Mencoba login dengan kredensial yang diberikan
        if (Auth::attempt($credentials)) {
            // Jika login berhasil, buat token API untuk user
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        // Jika gagal, kembalikan response gagal
        return response()->json([
            'message' => 'Login failed, incorrect credentials',
        ], 401);
    }

    public function logout(Request $request)
    {
        // Menghapus semua token untuk user yang sedang login
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}

