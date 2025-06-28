<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Request as GlobalRequest; 

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function login()
    {
        // Jika pengguna sudah login, redirect ke halaman utama (dashboard atau halaman lainnya)
        if (Auth::check()) {
            // Menggunakan back() untuk kembali ke halaman sebelumnya
            return redirect()->to('/dashboard');
        }


        return view('auth.login');
    }

    // Menangani autentikasi pengguna
public function authenticate(Request $request)
{
    // Validasi kredensial login
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Coba autentikasi pengguna
    if (Auth::attempt($credentials)) {
        // Regenerasi session untuk login yang berhasil
        $request->session()->regenerate();

        // Simpan log login admin
        Log::create([
            'user_id'    => Auth::user()->id,
            'action'     => 'login',
            'detail'     => "Admin login dengan email " . Auth::user()->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect ke halaman dashboard atau tujuan sebelumnya
        return redirect()->intended('/dashboard');
    }

    // Jika login gagal, tampilkan pesan error
    return back()->with('error', 'Email atau password salah.')->withInput();
}


    // Menangani logout pengguna
public function logout(Request $request)
{
    $user = Auth::user(); // ambil user dulu

    if ($user) {
        // buat log sebelum logout
        Log::create([
            'user_id'    => $user->id,
            'action'     => 'logout',
            'detail'     => "Admin logout",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    Auth::logout(); // baru logout
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}

}