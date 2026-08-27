<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\SystemSetting;
use App\Notifications\OrderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCompleteOrders extends Command
{
    protected $signature = 'orders:auto-complete';
    protected $description = 'Otomatis menyelesaikan pesanan delivered yang melewati batas waktu konfirmasi buyer';

    public function handle(): int
    {
        $hours = (int) SystemSetting::val('auto_complete_hours', 24);

        if ($hours <= 0) {
            $this->info('Auto-complete dinonaktifkan (durasi: 0 jam).');
            return self::SUCCESS;
        }

        $cutoff = now()->subHours($hours);

        // Auto-complete baru dimulai setelah paket tercatat sampai ke alamat,
        // bukan sejak paket dikirim. Ini aman untuk kurir toko dan reguler.
        $orders = Order::where('status', 'shipped')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', $cutoff)
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

                $order = Order::with(['items.product', 'store.user', 'buyer'])
                    ->whereKey($order->id)
                    ->lockForUpdate()
                    ->first();

                if (!$order || $order->status !== 'shipped') {
                    DB::rollBack();
                    continue;
                }

                $order->status = 'completed';
                $order->save();

                DB::commit();

                // Notify seller
                try {
                    $sellerUser = $order->store->user ?? null;
                    if ($sellerUser) {
                        $sellerUser->notify(new OrderNotification(
                            'order_auto_completed',
                            '✅ Pesanan Otomatis Selesai',
                            'Pesanan ' . $order->invoice_number . ' telah otomatis diselesaikan karena pembeli tidak mengonfirmasi dalam ' . $hours . ' jam.',
                            route('admin.orders.show', $order->id),
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
                $this->info("{$order->invoice_number} selesai otomatis.");

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
