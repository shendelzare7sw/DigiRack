<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreProfileController extends Controller
{
    public function show()
    {
        $store = Auth::user()->store;
        return view('seller.store.index', compact('store'));
    }

    public function update(Request $request)
    {
        $store = Auth::user()->store;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
        ]);

        $store->name = $request->name;
        // Only update slug if name actually changed significantly
        if ($store->isDirty('name')) {
            $store->slug = Str::slug($request->name) . '-' . uniqid();
        }
        
        $store->description = $request->description;
        $store->bank_name = $request->bank_name;
        $store->bank_account_no = $request->bank_account_no;
        $store->bank_account_name = $request->bank_account_name;

        if ($request->hasFile('logo')) {
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $store->logo = $request->file('logo')->store('stores/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($store->banner && Storage::disk('public')->exists($store->banner)) {
                Storage::disk('public')->delete($store->banner);
            }
            $store->banner = $request->file('banner')->store('stores/banners', 'public');
        }

        $store->save();

        return back()->with('success', 'Profil toko berhasil diperbarui.');
    }
}
