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

        // Admin access only for actual admins
        if ($role === 'admin' && $user->role !== 'admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        // Seller switch: check if user has a store
        if ($role === 'seller') {
            if (!$user->store) {
                // User doesn't have a store yet -> redirect to seller registration
                return redirect()->route('seller.register.form')
                    ->with('info', 'Untuk menjadi penjual, Anda perlu mendaftarkan toko terlebih dahulu.');
            }
        }

        Session::put('active_role', $role);

        // Redirect based on role
        if ($role === 'seller') {
            return redirect()->route('seller.dashboard')->with('success', 'Beralih ke mode Seller.');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('home')->with('success', 'Beralih ke mode Pembeli.');
        }
    }
}
