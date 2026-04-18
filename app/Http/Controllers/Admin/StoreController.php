<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Notifications\StoreStatusNotification;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::with('user')->withCount('products');

        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_verified', false);
            }
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $stores = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());
        
        $pendingCount = Store::where('is_verified', false)->count();

        return view('admin.stores.index', compact('stores', 'pendingCount'));
    }

    public function show($id)
    {
        $store = Store::with(['user', 'products'])->findOrFail($id);
        return view('admin.stores.show', compact('store'));
    }

    public function toggleVerification(Request $request, $id)
    {
        $store = Store::findOrFail($id);
        
        $store->is_verified = !$store->is_verified;
        
        // As a business logic, verifying a store should also make sure it's active
        if ($store->is_verified && !$store->is_active) {
            $store->is_active = true;
        }

        $store->save();

        // Notify seller
        try {
            $sellerUser = $store->user;
            if ($sellerUser) {
                if ($store->is_verified) {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_verified',
                        '✅ Toko Anda Telah Terverifikasi!',
                        'Selamat! Identitas bisnis toko "' . $store->name . '" telah lolos kurasi Admin. Anda kini dapat berjualan dan menerima pencairan dana.',
                        route('seller.dashboard'),
                        '🎊'
                    ));
                } else {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_unverified',
                        '⚠️ Status Verifikasi Dicabut',
                        'Status verifikasi toko "' . $store->name . '" telah dicabut oleh Admin. Silakan hubungi support untuk informasi lebih lanjut.',
                        route('seller.dashboard'),
                        '⚠️'
                    ));
                }
            }
        } catch (\Exception $e) {}

        $status = $store->is_verified ? 'diverifikasi' : 'dicabut verifikasinya';
        return back()->with('success', "Toko {$store->name} berhasil {$status}.");
    }

    public function toggleActive(Request $request, $id)
    {
        $store = Store::findOrFail($id);
        $store->is_active = !$store->is_active;
        $store->save();

        // Notify seller
        try {
            $sellerUser = $store->user;
            if ($sellerUser) {
                if ($store->is_active) {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_restored',
                        '🟢 Toko Anda Telah Dipulihkan!',
                        'Toko "' . $store->name . '" telah diaktifkan kembali oleh Admin. Selamat berjualan!',
                        route('seller.dashboard'),
                        '✅'
                    ));
                } else {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_banned',
                        '🚫 Toko Anda Telah Dinonaktifkan',
                        'Toko "' . $store->name . '" telah dinonaktifkan (banned) oleh Admin. Seluruh produk Anda tidak akan tampil di marketplace.',
                        route('seller.dashboard'),
                        '🚫'
                    ));
                }
            }
        } catch (\Exception $e) {}

        $status = $store->is_active ? 'diaktifkan' : 'dinonaktifkan (banned)';
        return back()->with('success', "Toko {$store->name} berhasil {$status}.");
    }
}
