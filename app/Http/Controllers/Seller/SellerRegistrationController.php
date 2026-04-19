<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerRegistrationController extends Controller
{
    /**
     * Show seller registration form for buyers who want to open a store.
     */
    public function showForm()
    {
        $user = Auth::user();

        // Already has store? Redirect to seller dashboard
        if ($user->store) {
            return redirect()->route('seller.dashboard')->with('info', 'Anda sudah memiliki toko terdaftar.');
        }

        return view('seller.register');
    }

    /**
     * Process seller registration: create store, upgrade role.
     */
    public function register(Request $request)
    {
        $user = Auth::user();

        if ($user->store) {
            return redirect()->route('seller.dashboard')->with('info', 'Toko Anda sudah terdaftar.');
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string|max:1000',
        ]);

        // Generate unique slug
        $slug = Str::slug($request->store_name) . '-' . Str::random(5);
        while (Store::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->store_name) . '-' . Str::random(5);
        }

        // Create the store
        Store::create([
            'user_id' => $user->id,
            'name' => $request->store_name,
            'slug' => $slug,
            'description' => $request->store_description ?? '',
            'is_active' => true,
            'is_verified' => false, // Needs admin verification
        ]);

        // Upgrade user role to seller
        $user->role = 'seller';
        $user->save();

        return redirect()->route('seller.dashboard')->with('success', 'Selamat! Toko "' . $request->store_name . '" berhasil didaftarkan. Menunggu verifikasi Admin untuk bisa mencairkan dana.');
    }
}
