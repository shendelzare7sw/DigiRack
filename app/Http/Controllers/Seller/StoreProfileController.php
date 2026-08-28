<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        ]);

        $store->name = $request->name;
        // Public URL is a permanent Digital Hook identity, not derived from the display name.
        $store->slug = 'digihook';

        $store->description = $request->description;

        if ($request->hasFile('logo')) {
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $store->logo = $request->file('logo')->store('stores/logos', 'public');
        }

        $store->save();

        return back()->with('success', 'Profil bisnis berhasil diperbarui.');
    }
}
