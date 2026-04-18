<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\StoreCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    public function index()
    {
        $store = Auth::user()->store;
        $couriers = StoreCourier::where('store_id', $store->id)->orderBy('created_at', 'desc')->get();
        return view('seller.couriers.index', compact('couriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'estimation' => 'nullable|string|max:100',
        ]);

        $store = Auth::user()->store;

        StoreCourier::create([
            'store_id' => $store->id,
            'name' => $request->name,
            'price' => $request->price,
            'estimation' => $request->estimation,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kurir internal toko berhasil ditambahkan.');
    }

    public function toggleActive($id)
    {
        $store = Auth::user()->store;
        $courier = StoreCourier::where('store_id', $store->id)->findOrFail($id);
        
        $courier->update(['is_active' => !$courier->is_active]);

        $status = $courier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kurir '{$courier->name}' berhasil $status.");
    }

    public function destroy($id)
    {
        $store = Auth::user()->store;
        $courier = StoreCourier::where('store_id', $store->id)->findOrFail($id);
        
        $courier->delete();

        return back()->with('success', 'Kurir internal dihapus.');
    }
}
