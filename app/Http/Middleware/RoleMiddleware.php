<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah peran user cocok dengan salah satu peran yang diizinkan
        if ($user->hasRole($roles)) {
            return $next($request);
        }

        // Jika tidak berhak, arahkan ke dashboard peran pengguna masing-masing
        $userRole = strtolower($user->role ?? '');
        $targetRoute = match ($userRole) {
            'admin' => 'admin.dashboard',
            'dinkes' => 'dinkes.dashboard',
            'penyedia' => 'penyedia.dashboard',
            default => 'faskes.dashboard',
        };

        return redirect()->route($targetRoute)->with('error', 'Akses ditolak: Anda tidak memiliki wewenang untuk membuka halaman tersebut.');
    }
}
