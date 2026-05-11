<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\SystemSetting;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Notifications\OrderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCompleteOrders extends Command
{
    protected $signature = 'orders:auto-complete';
    protected $description = 'Otomatis menyelesaikan pesanan shipped yang melewati batas waktu konfirmasi buyer';

    public function handle(): int
    {
        $hours = (int) SystemSetting::val('auto_complete_hours', 24);

        if ($hours <= 0) {
            $this->info('Auto-complete dinonaktifkan (durasi: 0 jam).');
            return self::SUCCESS;
        }

        $cutoff = now()->subHours($hours);

        // Cari pesanan shipped yang sudah melewati batas waktu
        $orders = Order::where('status', 'shipped')
            ->where('updated_at', '<=', $cutoff)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Tidak ada pesanan yang perlu di-auto-complete.');
            return self::SUCCESS;
        }

        $completed = 0;
        $failed = 0;

        foreach ($orders as $order) {
            try {
                DB::beginTransaction();

                $order->status = 'completed';
                $order->save();

                // Hitung dana ke seller (sama seperti logic di OrderController@confirm)
                $productSubtotal = $order->total_price - $order->shipping_cost;

                $courier = $order->shipping_address['courier'] ?? '';
                $shippingToSeller = 0;
                if (str_starts_with(strtolower($courier), 'toko_')) {
                    $shippingToSeller = $order->shipping_cost;
                }

                // Potongan platform per-item
                $order->load('items');
                $totalQty = $order->items->sum('quantity');
                $feePerItem = SystemSetting::val('platform_fee_per_item', 0);
                $totalPlatformFee = $totalQty * $feePerItem;

                $netToSeller = $productSubtotal + $shippingToSeller - $totalPlatformFee;
                if ($netToSeller < 0) {
                    $netToSeller = 0;
                }

                if ($netToSeller > 0) {
                    $wallet = Wallet::firstOrCreate(
                        ['store_id' => $order->store_id],
                        ['balance' => 0]
                    );

                    $wallet->balance += $netToSeller;
                    $wallet->save();

                    $desc = 'Auto-complete pesanan ' . $order->invoice_number;
                    if ($shippingToSeller > 0) {
                        $desc .= ' (+Ongkir Internal: Rp' . number_format($shippingToSeller, 0, ',', '.') . ')';
                    }
                    if ($totalPlatformFee > 0) {
                        $desc .= ' (Dipotong Fee: Rp' . number_format($totalPlatformFee, 0, ',', '.') . ')';
                    }

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'credit',
                        'amount' => $netToSeller,
                        'reference' => 'AUTO-ORDER-' . $order->id,
                        'description' => $desc,
                    ]);
                }

                DB::commit();

                // Notify seller
                try {
                    $sellerUser = $order->store->user ?? null;
                    if ($sellerUser) {
                        $sellerUser->notify(new OrderNotification(
                            'order_auto_completed',
                            '✅ Pesanan Otomatis Selesai',
                            'Pesanan ' . $order->invoice_number . ' telah otomatis diselesaikan karena buyer tidak mengkonfirmasi dalam ' . $hours . ' jam. Dana Rp ' . number_format($netToSeller, 0, ',', '.') . ' telah masuk ke saldo toko.',
                            route('seller.wallet.index'),
                            '💰'
                        ));
                    }
                } catch (\Exception $e) {
                    // Notifikasi gagal bukan masalah kritis
                }

                // Notify buyer
                try {
                    $order->buyer->notify(new OrderNotification(
                        'order_auto_completed',
                        '📦 Pesanan Otomatis Selesai',
                        'Pesanan ' . $order->invoice_number . ' telah otomatis diselesaikan. Jika ada kendala, silakan hubungi penjual.',
                        route('buyer.orders.show', $order->id),
                        '✅'
                    ));
                } catch (\Exception $e) {}

                $completed++;
                $this->info("✓ {$order->invoice_number} → completed (Rp " . number_format($netToSeller, 0, ',', '.') . " → seller wallet)");

            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                Log::error("Auto-complete order #{$order->id} failed: " . $e->getMessage());
                $this->error("✗ {$order->invoice_number} → GAGAL: " . $e->getMessage());
            }
        }

        $this->info("Selesai: {$completed} berhasil, {$failed} gagal.");
        Log::info("Auto-complete orders: {$completed} completed, {$failed} failed.");

        return self::SUCCESS;
    }
}
