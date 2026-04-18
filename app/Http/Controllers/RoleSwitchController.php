<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleSwitchController extends Controller
{
    public function switchRole($role)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($role, ['buyer', 'seller', 'admin'])) {
            return back()->with('error', 'Role tidak valid.');
        }

        // Validasi: hanya bisa switch ke seller jika memang role db nya seller, atau admin
        if ($role === 'seller' && $user->role !== 'seller') {
            return back()->with('error', 'Anda harus membuka toko terlebih dahulu untuk beralih ke mode penjual.');
        }

        if ($role === 'admin' && $user->role !== 'admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        Session::put('active_role', $role);

        // Redirect based on role
        if ($role === 'seller') {
            return redirect()->route('seller.dashboard');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('home')->with('success', 'Beralih ke mode Pembeli.');
        }
    }
}
