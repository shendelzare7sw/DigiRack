<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Notifications\OrderNotification;
use App\Services\IrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    public function index()
    {
        $payouts = PayoutRequest::with('store')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.payouts.index', compact('payouts'));
    }

    public function approve(Request $request, $id, IrisService $irisService)
    {
        $payout = PayoutRequest::with('store')->findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Payout request is not pending.');
        }
        
        $store = $payout->store;

        $payoutData = [
            'beneficiary_name' => $store->bank_account_name ?? 'DigiRack Seller', // required
            'beneficiary_account' => $store->bank_account_no, // required
            'beneficiary_bank' => strtolower($store->bank_name), // e.g. bca, bni
            'amount' => $payout->net_amount, // The amount after admin fee logic
            'notes' => 'Pencairan Dana Toko ' . $store->name,
        ];

        try {
            // Hit Midtrans IRIS
            $response = $irisService->createPayout($payoutData);

            if ($response['success']) {
                $payout->status = 'completed';
                // Midtrans IRIS returns a list of references, but in dummy we just return one reference_no.
                // Usually it returns an array of reference numbers.
                $payout->iris_reference_no = $response['data']['reference_no'] ?? (json_encode($response['data']));
                $payout->save();

                // Notify seller
                try {
                    $store->user->notify(new OrderNotification(
                        'payout_approved',
                        '💸 Pencairan Dana Berhasil!',
                        'Dana sebesar Rp ' . number_format($payout->net_amount, 0, ',', '.') . ' telah ditransfer ke rekening ' . $store->bank_name . ' ' . $store->bank_account_no . '.',
                        route('seller.wallet.index'),
                        '✅'
                    ));
                } catch (\Exception $e) {}

                return back()->with('success', 'Pencairan dana berhasil disetujui dan ditransfer via IRIS.');
            } else {
                // If API fails, log error
                return back()->with('error', 'Gagal mengirim instruksi ke IRIS: ' . ($response['message'] ?? 'Unknown Error'));
            }
        } catch (\Exception $e) {
            \Log::error('IRIS payout error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghubungi layanan pencairan. Silakan coba lagi.');
        }
    }

    public function reject(Request $request, $id)
    {
        $payout = PayoutRequest::with('store.wallet')->findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Pencairan ini sudah tidak berstatus menunggu.');
        }

        try {
            DB::beginTransaction();

            $payout->status = 'rejected';
            $payout->save();

            // Reverse the wallet deduction
            $wallet = $payout->store->wallet;
            if ($wallet) {
                $wallet->balance += $payout->amount;
                $wallet->save();

                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $payout->amount,
                    'reference' => 'REFUND-PAYOUT-' . $payout->id,
                    'description' => 'Pengembalian dana karena pencairan ditolak oleh admin.',
                ]);
            }

            DB::commit();

            // Notify seller about rejection
            try {
                $payout->store->user->notify(new OrderNotification(
                    'payout_rejected',
                    '❌ Pencairan Dana Ditolak',
                    'Permintaan pencairan Rp ' . number_format($payout->amount, 0, ',', '.') . ' ditolak oleh Admin. Dana telah dikembalikan ke saldo toko Anda.',
                    route('seller.wallet.index'),
                    '🔄'
                ));
            } catch (\Exception $e) {}

            return back()->with('success', 'Pencairan ditolak dan dana dikembalikan ke wallet Seller.');
        } catch (\Exception $e) {
            \Log::error('Payout reject error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak pencairan. Silakan coba lagi.');
        }
    }
}
