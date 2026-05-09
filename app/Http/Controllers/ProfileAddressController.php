<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileAddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'full_address' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_primary' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // If it's the first address, force it to primary
        $isPrimary = $request->has('is_primary') ? true : false;
        if ($user->addresses()->count() === 0) {
            $isPrimary = true;
        }

        if ($isPrimary) {
            // Remove primary from others
            $user->addresses()->update(['is_primary' => false]);
        }

        $validated['user_id'] = $user->id;
        $validated['is_primary'] = $isPrimary;

        Address::create($validated);

        return redirect()->back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'full_address' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_primary' => 'nullable|boolean',
        ]);

        $isPrimary = $request->has('is_primary') ? true : false;

        if ($isPrimary && !$address->is_primary) {
            Auth::user()->addresses()->update(['is_primary' => false]);
        }
        
        // If unchecking primary and it's the only one, force it back to primary (not strictly needed but good practice)
        if (!$isPrimary && $address->is_primary && Auth::user()->addresses()->count() === 1) {
            $isPrimary = true;
        }

        $validated['is_primary'] = $isPrimary;

        $address->update($validated);

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasPrimary = $address->is_primary;
        $address->delete();

        // If primary was deleted, set another one as primary automatically if exists
        $user = Auth::user();
        if ($wasPrimary && $user->addresses()->count() > 0) {
            $first = $user->addresses()->first();
            $first->update(['is_primary' => true]);
        }

        return redirect()->back()->with('success', 'Alamat dihapus.');
    }

    public function setPrimary(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Alamat utama berhasil diubah.');
    }
}
