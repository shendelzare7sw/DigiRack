<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyerTransactionFee;
use Illuminate\Http\Request;

class BuyerTransactionFeeController extends Controller
{
    public function index()
    {
        $fees = BuyerTransactionFee::orderBy('created_at', 'desc')->get();
        return view('admin.transaction_fees.index', compact('fees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        BuyerTransactionFee::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'is_active' => true,
        ]);

        return back()->with('success', 'Biaya Transaksi Pembeli berhasil ditambahkan.');
    }

    public function toggleActive($id)
    {
        $fee = BuyerTransactionFee::findOrFail($id);
        $fee->is_active = !$fee->is_active;
        $fee->save();

        $status = $fee->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Biaya '{$fee->name}' berhasil $status.");
    }

    public function destroy($id)
    {
        $fee = BuyerTransactionFee::findOrFail($id);
        $fee->delete();

        return back()->with('success', 'Biaya Transaksi Pembeli berhasil dihapus.');
    }
}
