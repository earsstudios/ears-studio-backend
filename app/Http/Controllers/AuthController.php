<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            $make = Hash::make('admin123');
            return response()->json(['success' => false, 'message' => $make], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => auth()->user()
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['success' => true, 'message' => 'Berhasil logout']);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
