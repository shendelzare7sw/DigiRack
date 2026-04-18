<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('bank_name', 50)->nullable()->after('city_id');
            $table->string('bank_account_no', 100)->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_no');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_no', 'bank_account_name']);
        });
    }
};
