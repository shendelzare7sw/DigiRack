<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@digihook.test')->firstOrFail();

        Store::firstOrCreate(
            ['slug' => 'digihook'],
            [
                'user_id' => $admin->id,
                'name' => 'Digital Hook',
                'description' => 'Perangkat komputer, laptop second, komponen, dan aksesori digital dengan pengantaran same-day di Tangerang Raya.',
                'is_active' => true,
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'avg_rating' => 4.8,
                'total_sold' => 0,
            ]
        );
    }
}
