<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceActiveBuyerRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Blokir rute pembeli untuk admin secara ketat.
            if ($user->role === 'admin') {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Admin platform tidak diizinkan melakukan transaksi logistik/belanja.'], 403);
                }
                return redirect()->route('admin.dashboard')->with('error', 'Admin tidak dapat menggunakan fitur pembeli.');
            }

        }

        return $next($request);
    }
}
