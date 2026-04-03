<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'title' => 'Promo Perangkat Networking — Diskon Hingga 30%',
            'image_path' => 'banners/banner-promo-1.jpg',
            'link_url' => '/products?category=networking',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Banner::create([
            'title' => 'Flash Sale Server & Hardware — Waktu Terbatas!',
            'image_path' => 'banners/banner-flash-sale.jpg',
            'link_url' => '/products?flash_sale=1',
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}
