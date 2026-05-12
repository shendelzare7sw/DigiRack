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
            'identity_document' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:6144',
        ]);

        // Generate unique slug
        $slug = Str::slug($request->store_name) . '-' . Str::random(5);
        while (Store::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->store_name) . '-' . Str::random(5);
        }

        $identityPath = $request->file('identity_document')->store('store-identities', 'public');

        // Create the store as pending. Admin approval will activate seller access.
        Store::create([
            'user_id' => $user->id,
            'name' => $request->store_name,
            'slug' => $slug,
            'description' => $request->store_description ?? '',
            'identity_document_path' => $identityPath,
            'identity_submitted_at' => now(),
            'is_active' => false,
            'is_verified' => false,
            'verification_status' => 'pending',
            'verification_notes' => null,
            'verified_at' => null,
        ]);

        return redirect()->route('seller.dashboard')
            ->with('success', 'Toko "' . $request->store_name . '" berhasil diajukan. Anda bisa masuk dashboard seller, tetapi menu penjualan aktif setelah verifikasi admin.');
    }
}
