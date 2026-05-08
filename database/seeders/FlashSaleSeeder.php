<?php

namespace Database\Seeders;

use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Database\Seeder;

class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::inRandomOrder()->take(3)->get();

        foreach ($products as $product) {
            $discount = rand(15, 30);
            FlashSale::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'discount_percent' => $discount,
                    'original_price' => $product->price,
                    'sale_price' => (int) ($product->price * (100 - $discount) / 100),
                    'stock_flash' => rand(5, 20),
                    'start_time' => now()->subHours(2),
                    'end_time' => now()->addDays(3),
                    'is_active' => true,
                ]
            );
        }
    }
}
