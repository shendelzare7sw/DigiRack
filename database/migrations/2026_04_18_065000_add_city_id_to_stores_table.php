<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('user_id')->constrained('cities')->nullOnDelete();
        });

        // Set default city for existing stores to avoid empty origin errors in the frontend
        // Assuming City ID 153 is Jakarta Selatan
        $defaultCity = \App\Models\City::first();
        if ($defaultCity) {
            \Illuminate\Support\Facades\DB::table('stores')->whereNull('city_id')->update(['city_id' => $defaultCity->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
        });
    }
};
