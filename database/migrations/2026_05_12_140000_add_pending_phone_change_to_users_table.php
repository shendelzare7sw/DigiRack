<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pending_phone', 20)->nullable()->after('phone');
            $table->string('pending_phone_token')->nullable()->after('pending_phone');
            $table->timestamp('pending_phone_expires_at')->nullable()->after('pending_phone_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pending_phone',
                'pending_phone_token',
                'pending_phone_expires_at',
            ]);
        });
    }
};
