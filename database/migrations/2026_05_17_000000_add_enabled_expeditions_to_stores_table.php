<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // List of enabled regular expedition codes (jne, pos, tiki).
            // Null/empty = no expedition active (strict): buyer sees no shipping
            // until the seller configures couriers.
            $table->json('enabled_expeditions')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('enabled_expeditions');
        });
    }
};
