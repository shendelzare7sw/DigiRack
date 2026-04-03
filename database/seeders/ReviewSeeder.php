<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::where('role', 'buyer')->get();
        $products = Product::with('store')->get();

        $comments = [
            5 => ['Produk sangat bagus, sesuai deskripsi!', 'Kualitas premium, sangat puas!', 'Mantap, pengiriman cepat dan produk original.'],
            4 => ['Bagus, sesuai ekspektasi.', 'Oke, cukup memuaskan.', 'Produk baik, packaging rapi.'],
            3 => ['Cukup oke, standar lah.', 'Lumayan, sesuai harganya.', 'Biasa saja, tapi berfungsi dengan baik.'],
        ];

        $orderNumber = 0;
        foreach ($buyers as $buyer) {
            // Each buyer reviews some random products
            $reviewProducts = $products->random(min(4, $products->count()));

            foreach ($reviewProducts as $product) {
                $orderNumber++;
                $rating = array_rand([3 => 1, 4 => 2, 5 => 3]);
                $rating = max(3, min(5, $rating + 2)); // ratings 3-5

                // Create a completed order for context
                $order = Order::create([
                    'buyer_id' => $buyer->id,
                    'store_id' => $product->store_id,
                    'status' => 'completed',
                    'total_price' => $product->price,
                    'shipping_cost' => 15000,
                    'payment_method' => 'transfer',
                    'payment_status' => 'paid',
                    'shipping_address' => [
                        'name' => $buyer->name,
                        'phone' => $buyer->phone ?? '081234567890',
                        'address' => 'Jl. Contoh No. ' . $orderNumber,
                        'city' => 'Jakarta',
                        'province' => 'DKI Jakarta',
                        'postal_code' => '10110',
                    ],
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->name,
                    'price_snapshot' => $product->price,
                    'quantity' => 1,
                ]);

                $commentPool = $comments[$rating] ?? $comments[4];

                Review::create([
                    'buyer_id' => $buyer->id,
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'rating' => $rating,
                    'comment' => $commentPool[array_rand($commentPool)],
                ]);
            }
        }
    }
}
