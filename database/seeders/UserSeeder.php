<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin DigiRack',
            'email' => 'admin@digirack.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Sellers
        User::create([
            'name' => 'Toko Jaringan Nusantara',
            'email' => 'seller1@digirack.test',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '081234567891',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'NetPro Supply',
            'email' => 'seller2@digirack.test',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '081234567892',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Buyers
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'buyer1@digirack.test',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081234567893',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Siti Rahayu',
            'email' => 'buyer2@digirack.test',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081234567894',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Andi Wirawan',
            'email' => 'buyer3@digirack.test',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081234567895',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
