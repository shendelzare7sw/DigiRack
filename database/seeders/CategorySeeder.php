<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Komponen PC', 'slug' => 'komponen-pc', 'description' => 'Processor, motherboard, VGA, power supply, dan casing', 'sort_order' => 1],
            ['name' => 'Laptop & Komputer', 'slug' => 'laptop-komputer', 'description' => 'Laptop baru/second, PC rakitan, dan mini PC', 'sort_order' => 2],
            ['name' => 'Penyimpanan & Memori', 'slug' => 'penyimpanan-memori', 'description' => 'SSD, hard disk, RAM, flash drive, dan kartu memori', 'sort_order' => 3],
            ['name' => 'Monitor & Periferal', 'slug' => 'monitor-periferal', 'description' => 'Monitor, keyboard, mouse, webcam, headset, dan speaker', 'sort_order' => 4],
            ['name' => 'Jaringan & Kabel', 'slug' => 'jaringan-kabel', 'description' => 'Router rumahan, Wi-Fi adapter, kabel LAN, dan konektor', 'sort_order' => 5],
            ['name' => 'Aksesori Digital', 'slug' => 'aksesori-digital', 'description' => 'Charger, hub USB, kabel data, cooling pad, dan aksesori umum', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['icon_svg' => null, 'is_active' => true]),
            );
        }

        Category::whereNotIn('slug', collect($categories)->pluck('slug'))->update(['is_active' => false]);
    }
}
