<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Notifications\StoreStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $store = Store::create([
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

        User::where('role', 'admin')->get()->each(function (User $admin) use ($store, $user) {
            $admin->notify(new StoreStatusNotification(
                'store_registration_submitted',
                'Pengajuan toko baru',
                'Toko "' . $store->name . '" diajukan oleh ' . $user->name . '. Periksa dokumen identitas sebelum verifikasi.',
                route('admin.stores.show', $store->id),
                'ID'
            ));
        });

        $user->notify(new StoreStatusNotification(
            'store_registration_received',
            'Pengajuan toko diterima',
            'Toko "' . $store->name . '" sudah masuk antrean verifikasi admin. Anda akan mendapat notifikasi setelah diproses.',
            route('seller.dashboard'),
            'OK'
        ));

        return redirect()->route('seller.dashboard')
            ->with('success', 'Toko "' . $request->store_name . '" berhasil diajukan. Anda bisa masuk dashboard seller, tetapi menu penjualan aktif setelah verifikasi admin.');
    }

    /**
     * Show identity document upload form for an existing store that has no
     * (or rejected) document yet — e.g. stores created before identity was required.
     */
    public function showIdentityForm()
    {
        $user = Auth::user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('seller.register.form')
                ->with('info', 'Daftarkan toko Anda terlebih dahulu.');
        }

        if ($store->isApproved()) {
            return redirect()->route('seller.dashboard')
                ->with('info', 'Toko Anda sudah terverifikasi.');
        }

        return view('seller.identity', compact('store'));
    }

    /**
     * Store/replace the identity document for an existing store and
     * push it back into the admin verification queue.
     */
    public function submitIdentity(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('seller.register.form')
                ->with('info', 'Daftarkan toko Anda terlebih dahulu.');
        }

        if ($store->isApproved()) {
            return redirect()->route('seller.dashboard')
                ->with('info', 'Toko Anda sudah terverifikasi.');
        }

        $request->validate([
            'identity_document' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:6144',
        ]);

        if ($store->identity_document_path && Storage::disk('public')->exists($store->identity_document_path)) {
            Storage::disk('public')->delete($store->identity_document_path);
        }

        $identityPath = $request->file('identity_document')->store('store-identities', 'public');

        $store->update([
            'identity_document_path' => $identityPath,
            'identity_submitted_at' => now(),
            'is_verified' => false,
            'is_active' => false,
            'verification_status' => 'pending',
            'verification_notes' => null,
            'verified_at' => null,
        ]);

        User::where('role', 'admin')->get()->each(function (User $admin) use ($store, $user) {
            $admin->notify(new StoreStatusNotification(
                'store_identity_submitted',
                'Dokumen identitas toko diperbarui',
                'Toko "' . $store->name . '" mengirim dokumen identitas dari ' . $user->name . '. Silakan tinjau untuk verifikasi.',
                route('admin.stores.show', $store->id),
                'ID'
            ));
        });

        return redirect()->route('seller.dashboard')
            ->with('success', 'Dokumen identitas berhasil dikirim. Toko Anda kembali masuk antrean verifikasi admin.');
    }
}
