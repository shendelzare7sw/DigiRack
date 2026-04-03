<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending_payment', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending_payment');
            $table->bigInteger('total_price');
            $table->bigInteger('shipping_cost')->default(0);
            $table->enum('payment_method', ['transfer', 'cod'])->default('transfer');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->json('shipping_address');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
