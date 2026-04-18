<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\SystemSetting;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected function getStore()
    {
        return Auth::user()->store;
    }

    public function index()
    {
        $store = $this->getStore();
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'Anda harus memiliki toko.');
        }

        $wallet = Wallet::firstOrCreate(['store_id' => $store->id], ['balance' => 0]);
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)->orderBy('created_at', 'desc')->paginate(10);
        $payouts = PayoutRequest::where('store_id', $store->id)->orderBy('created_at', 'desc')->paginate(10);

        return view('seller.wallet.index', compact('wallet', 'transactions', 'payouts', 'store'));
    }

    public function requestPayout(Request $request)
    {
        $store = $this->getStore();
        $wallet = Wallet::firstOrCreate(['store_id' => $store->id], ['balance' => 0]);

        $request->validate([
            'amount' => "required|numeric|min:10000|max:{$wallet->balance}",
        ]);

        if (empty($store->bank_name) || empty($store->bank_account_no)) {
            return back()->with('error', 'Silakan lengkapi data Rekening Bank di Profil Toko sebelum menarik dana.');
        }

        try {
            DB::beginTransaction();

            // Potong saldo di wallet
            $wallet->balance -= $request->amount;
            $wallet->save();

            // Ambil persentase fee admin (misal 2% jika belum disetting)
            $feePercent = SystemSetting::val('withdrawal_fee_percentage', 2);
            $feeAmount = ($feePercent / 100) * $request->amount;
            $netAmount = $request->amount - $feeAmount;

            $payout = PayoutRequest::create([
                'store_id' => $store->id,
                'amount' => $request->amount,
                'fee' => $feeAmount,
                'net_amount' => $netAmount,
                'status' => 'pending',
            ]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $request->amount,
                'reference' => 'PAYOUT-' . $payout->id,
                'description' => 'Penarikan dana. Fee Admin: Rp ' . number_format($feeAmount, 0, ',', '.'),
            ]);

            DB::commit();

            return back()->with('success', 'Permintaan pencairan dana berhasil dibuat. Menunggu persetujuan Admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat request pencairan dana: ' . $e->getMessage());
        }
    }
}
