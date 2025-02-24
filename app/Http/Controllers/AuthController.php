<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
{
    // Validasi input
    $request->validate([
        'name' => 'required',
        'password' => 'required',
    ]);

    // Cek pengguna berdasarkan name
    $user = User::where('name', $request->name)->first();
    $hashedPassword = Hash::make('admin');
    $hashedpetugas = Hash::make('petugas');
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Name atau password salah',
            'petugas' => $user->password,
            'admin' => $hashedPassword
        ], 401);
    }

    // Buat token API
    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login berhasil',
        'data' => [
            'user' => $user,
            'token' => $token,
        ],
    ]);
}
}
