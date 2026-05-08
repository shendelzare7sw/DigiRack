<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $seller1 = User::where('email', 'seller1@digirack.test')->first();
        $seller2 = User::where('email', 'seller2@digirack.test')->first();

        Store::firstOrCreate(
            ['slug' => 'jaringan-nusantara'],
            [
                'user_id' => $seller1->id,
                'name' => 'Jaringan Nusantara',
                'description' => 'Distributor peralatan jaringan terlengkap di Indonesia. Menyediakan berbagai produk networking, server, dan infrastruktur IT berkualitas tinggi.',
                'is_active' => true,
                'is_verified' => true,
                'avg_rating' => 4.5,
                'total_sold' => 150,
            ]
        );

        Store::firstOrCreate(
            ['slug' => 'netpro-supply'],
            [
                'user_id' => $seller2->id,
                'name' => 'NetPro Supply',
                'description' => 'Supplier perangkat jaringan profesional untuk kebutuhan enterprise dan SOHO. Harga bersaing, produk original bergaransi.',
                'is_active' => true,
                'is_verified' => true,
                'avg_rating' => 4.3,
                'total_sold' => 98,
            ]
        );
    }
}
