<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->bigInteger('price');
            $table->integer('stock')->default(0);
            $table->integer('weight_gram')->default(0);
            $table->enum('condition', ['new', 'used'])->default('new');
            $table->enum('status', ['pending', 'active', 'rejected', 'inactive'])->default('pending');
            $table->integer('sold_count')->default(0);
            $table->decimal('avg_rating', 2, 1)->default(0);
            $table->json('specs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
