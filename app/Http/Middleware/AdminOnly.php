<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        // 🔒 Kalau belum login sama sekali
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // ❌ Kalau sudah login tapi bukan admin
        if (Auth::user()->role_id != 1) {
            // Hanya buat log jika user sudah login (pasti true di sini)
            Log::create([
                'user_id'    => Auth::id(), // aman karena sudah login
                'action'     => 'akses-ditolak',
                'detail'     => 'Pengguna bukan admin mencoba mengakses halaman admin',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Auth::logout(); // paksa logout
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'Anda tidak memiliki akses sebagai admin.');
        }

        // ✅ Jika admin, lanjut ke route
        return $next($request);
    }
}