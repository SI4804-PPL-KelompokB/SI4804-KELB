<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redirect central dashboard ke role masing-masing
     */
    public function index(): RedirectResponse
    {
        $role = strtolower(Auth::user()->role ?? '');

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dinkes' => redirect()->route('dinkes.dashboard'),
            'penyedia' => redirect()->route('penyedia.dashboard'),
            default => redirect()->route('faskes.dashboard'),
        };
    }

    /**
     * Dashboard Fasilitas Kesehatan (Faskes)
     */
    public function faskes(): View
    {
        $user = Auth::user()->load('faskes');
        return view('dashboard.faskes', compact('user'));
    }

    /**
     * Dashboard Dinas Kesehatan (Dinkes)
     */
    public function dinkes(): View
    {
        $user = Auth::user();
        return view('dashboard.dinkes', compact('user'));
    }

    /**
     * Dashboard Penyedia Obat (Gudang Farmasi / Apotek)
     */
    public function penyedia(): View
    {
        $user = Auth::user()->load('faskes');
        return view('dashboard.penyedia', compact('user'));
    }

    /**
     * Dashboard Administrator Sistem (Admin)
     */
    public function admin(): View
    {
        $user = Auth::user();
        return view('dashboard.admin', compact('user'));
    }
}
