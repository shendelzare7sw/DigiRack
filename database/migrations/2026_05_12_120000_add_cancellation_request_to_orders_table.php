<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending_payment', 'processing', 'cancellation_requested', 'shipped', 'completed', 'cancelled') DEFAULT 'pending_payment'");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('shipping_tracking_number');
            }
            if (!Schema::hasColumn('orders', 'cancellation_response')) {
                $table->text('cancellation_response')->nullable()->after('cancellation_reason');
            }
            if (!Schema::hasColumn('orders', 'cancellation_requested_at')) {
                $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_response');
            }
            if (!Schema::hasColumn('orders', 'cancellation_resolved_at')) {
                $table->timestamp('cancellation_resolved_at')->nullable()->after('cancellation_requested_at');
            }
        });
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', 'cancellation_requested')
            ->update(['status' => 'processing']);

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'cancellation_resolved_at')) {
                $table->dropColumn('cancellation_resolved_at');
            }
            if (Schema::hasColumn('orders', 'cancellation_requested_at')) {
                $table->dropColumn('cancellation_requested_at');
            }
            if (Schema::hasColumn('orders', 'cancellation_response')) {
                $table->dropColumn('cancellation_response');
            }
            if (Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending_payment', 'processing', 'shipped', 'completed', 'cancelled') DEFAULT 'pending_payment'");
        }
    }
};
