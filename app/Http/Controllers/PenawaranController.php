<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenawaranController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($user && $user->is_active, 403, 'Akun Anda tidak aktif.');

        $canManage = $user->hasRole('faskes') && $user->faskes_id !== null;

        return view('penawaran.index', [
            'canManage' => $canManage,
        ]);
    }
}
