<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan formulir login split-screen MedStock.id
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string', 'in:faskes,dinkes,penyedia,admin'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'role.required' => 'Silakan pilih peran pengguna Anda.',
            'role.in' => 'Peran pengguna yang dipilih tidak valid.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // 1. Validasi keberadaan akun dan kecocokan password
        if (!$user || !Auth::validate(['email' => $validated['email'], 'password' => $validated['password']])) {
            throw ValidationException::withMessages([
                'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
            ]);
        }

        // 2. Validasi status keaktifan akun
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang dinonaktifkan. Hubungi Administrator Dinkes.',
            ]);
        }

        // 3. Validasi kesesuaian peran yang dipilih (Sesuai F1 dan Mockup 4.5 wewenang peran)
        if (strtolower($user->role) !== strtolower($validated['role'])) {
            throw ValidationException::withMessages([
                'role' => 'Peran yang Anda pilih tidak cocok dengan hak akses akun. Akun ini terdaftar sebagai ' . $user->role_label . '.',
            ]);
        }

        // 4. Lakukan login session (dengan dukungan opsi "Ingat Saya" / Remember Me)
        $remember = $request->boolean('remember');
        Auth::login($user, $remember);

        // Perbarui riwayat waktu login
        $user->recordLogin();

        // Regenerasi session ID untuk mitigasi session fixation
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', "Selamat datang kembali, {$user->name}! Berhasil masuk sebagai {$user->role_label}.");
    }

    /**
     * Proses logout pengguna dari sistem
     */
    public function logout(Request $request): RedirectResponse
    {
        $userName = Auth::user()?->name;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda telah berhasil keluar dari sistem MedStock.id.');
    }

    /**
     * Mengarahkan pengguna ke dashboard sesuai perannya (F1)
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        $role = strtolower($user->role ?? '');

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dinkes' => redirect()->route('dinkes.dashboard'),
            'penyedia' => redirect()->route('penyedia.dashboard'),
            default => redirect()->route('faskes.dashboard'),
        };
    }
}
