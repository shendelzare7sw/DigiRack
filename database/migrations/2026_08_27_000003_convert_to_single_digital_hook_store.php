<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'seller')->update(['role' => 'buyer']);

        $admin = DB::table('users')->where('role', 'admin')->oldest('id')->first();
        if (! $admin) {
            return;
        }

        $store = DB::table('stores')->where('user_id', $admin->id)->oldest('id')->first();
        if (! $store) {
            $storeId = DB::table('stores')->insertGetId([
                'user_id' => $admin->id,
                'name' => 'Digital Hook',
                'slug' => 'digihook',
                'description' => 'Perangkat komputer, laptop second, komponen, dan aksesori digital untuk Tangerang Raya.',
                'enabled_expeditions' => null,
                'is_active' => true,
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'avg_rating' => 0,
                'total_sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $storeId = $store->id;
            DB::table('stores')->where('id', $storeId)->update([
                'name' => 'Digital Hook',
                'slug' => 'digihook',
                'description' => 'Perangkat komputer, laptop second, komponen, dan aksesori digital untuk Tangerang Raya.',
                'enabled_expeditions' => null,
                'is_active' => true,
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('products')->where('store_id', '!=', $storeId)->update(['store_id' => $storeId]);
        DB::table('orders')->where('store_id', '!=', $storeId)->update(['store_id' => $storeId]);
        DB::table('store_reviews')->where('store_id', '!=', $storeId)->update(['store_id' => $storeId]);
        DB::table('stores')->where('id', '!=', $storeId)->update(['is_active' => false]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','buyer') NOT NULL DEFAULT 'buyer'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','seller','buyer') NOT NULL DEFAULT 'buyer'");
        }
    }
};
