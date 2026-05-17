<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            FlashSaleSeeder::class,
            BannerSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
