<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        $store = $user->store;
        if (!$store) {
            return redirect()->route('seller.register.form')
                ->with('error', 'Anda perlu mendaftarkan toko terlebih dahulu.');
        }

        if (!$store->isApproved()) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Toko Anda masih menunggu verifikasi admin. Menu seller akan aktif setelah toko disetujui.');
        }

        return $next($request);
    }
}
