<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_proof_paths')) {
                $table->json('delivery_proof_paths')->nullable()->after('delivery_proof_path');
            }
        });

        if (Schema::hasColumn('orders', 'delivery_proof_path') && Schema::hasColumn('orders', 'delivery_proof_paths')) {
            DB::table('orders')
                ->whereNotNull('delivery_proof_path')
                ->whereNull('delivery_proof_paths')
                ->orderBy('id')
                ->chunkById(100, function ($orders) {
                    foreach ($orders as $order) {
                        DB::table('orders')
                            ->where('id', $order->id)
                            ->update([
                                'delivery_proof_paths' => json_encode([$order->delivery_proof_path]),
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_proof_paths')) {
                $table->dropColumn('delivery_proof_paths');
            }
        });
    }
};
