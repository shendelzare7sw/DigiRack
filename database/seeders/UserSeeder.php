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
        User::firstOrCreate(
            ['email' => 'admin@digihook.test'],
            [
                'name' => 'Admin Digital Hook',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Buyers
        User::firstOrCreate(
            ['email' => 'buyer1@digihook.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'phone' => '081234567893',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'buyer2@digihook.test'],
            [
                'name' => 'Siti Rahayu',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'phone' => '081234567894',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'buyer3@digihook.test'],
            [
                'name' => 'Andi Wirawan',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'phone' => '081234567895',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
