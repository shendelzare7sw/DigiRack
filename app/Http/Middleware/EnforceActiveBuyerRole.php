<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EnforceActiveBuyerRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // 1. Blokir rute pembeli untuk Admin secara ketat
            if ($user->role === 'admin') {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Admin platform tidak diizinkan melakukan transaksi logistik/belanja.'], 403);
                }
                return redirect()->route('admin.dashboard')->with('error', 'Admin tidak dapat menggunakan fitur pembeli.');
            }

            // 2. Jika Seller menyentuh rute pembeli, otomatis beralih Session Context menjadi Buyer
            if (Session::get('active_role', $user->role) === 'seller') {
                Session::put('active_role', 'buyer');
                // Sisi UI akan otomatis merender navbar versi pembeli setelah ini
            }
        }

        return $next($request);
    }
}
