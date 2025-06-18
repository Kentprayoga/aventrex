<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Log;
use Illuminate\Support\Facades\Request as GlobalRequest; // untuk IP dan User Agent

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Ambil user dengan relasi role
        $user = User::where('email', $request->email)->with('role')->first();

        // Jika user tidak ditemukan
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Jika password salah
        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Jika role bukan 'user'
        if (!$user->role || $user->role->name !== 'user') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya user yang diizinkan login.'
            ], 403);
        }

        // Jika status bukan active
        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Akun tidak aktif.'
            ], 403);
        }

        // Buat token
        $token = $user->createToken('mobile')->plainTextToken;
            Log::create([
                'user_id'    => $user->id,
                'action'     => 'login',
                'detail'     => 'Login dari aplikasi mobile',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);


        return response()->json([
            'token' => $token,
            'user' => [
                'id'     => $user->id,
                'email'  => $user->email,
                'status' => $user->status,
                'role'   => $user->role->name,
            ]
        ],200);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });
    Log::create([
        'user_id'    => $request->user()->id,
        'action'     => 'logout',
        'detail'     => 'Logout dari aplikasi mobile',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

        
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

}