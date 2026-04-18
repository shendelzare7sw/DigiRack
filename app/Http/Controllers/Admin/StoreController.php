<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;

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

        $status = $store->is_verified ? 'diverifikasi' : 'dicabut verifikasinya';
        return back()->with('success', "Toko {$store->name} berhasil {$status}.");
    }

    public function toggleActive(Request $request, $id)
    {
        $store = Store::findOrFail($id);
        $store->is_active = !$store->is_active;
        $store->save();

        $status = $store->is_active ? 'diaktifkan' : 'dinonaktifkan (banned)';
        return back()->with('success', "Toko {$store->name} berhasil {$status}.");
    }
}
