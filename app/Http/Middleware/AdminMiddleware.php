<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah pengguna sudah login dan apakah role_id mereka adalah admin (misalnya role_id 1 untuk admin)
        if (Auth::check() && Auth::user()->role_id == 1) {
            return $next($request);
        }

        // Jika bukan admin, redirect ke halaman lain atau halaman error
        return redirect('/')->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
    }
}