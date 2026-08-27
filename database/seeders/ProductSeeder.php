<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'digihook')->firstOrFail();
        $categoryIds = Category::pluck('id', 'slug');

        $products = [
            ['komponen-pc', 'AMD Ryzen 5 5600 Box', 1899000, 12, 500, 'new', 'Processor 6 core 12 thread untuk PC gaming dan produktivitas harian.'],
            ['komponen-pc', 'Intel Core i5-12400F Box', 2099000, 10, 500, 'new', 'Processor desktop 6 core dengan performa efisien untuk rakitan kelas menengah.'],
            ['komponen-pc', 'Power Supply 550W 80+ Bronze', 725000, 14, 1800, 'new', 'PSU 550 watt bersertifikasi 80+ Bronze untuk PC rumahan dan gaming.'],
            ['laptop-komputer', 'Lenovo ThinkPad T480 Second', 4899000, 4, 2100, 'used', 'Laptop second business class, Intel Core i5 Gen 8, RAM 8GB, SSD 256GB. Sudah melalui pengecekan fungsi.'],
            ['laptop-komputer', 'Dell Latitude 5400 Second', 4599000, 3, 2100, 'used', 'Laptop second Core i5 Gen 8, RAM 8GB dan SSD 256GB untuk kerja dan belajar.'],
            ['laptop-komputer', 'Mini PC Intel N100 16/512GB', 3299000, 6, 900, 'new', 'Mini PC ringkas untuk pekerjaan kantor, kasir, belajar, dan hiburan rumah.'],
            ['penyimpanan-memori', 'SSD NVMe 512GB Gen3', 625000, 20, 80, 'new', 'SSD NVMe M.2 512GB untuk upgrade laptop atau PC dengan garansi toko.'],
            ['penyimpanan-memori', 'RAM DDR4 8GB 3200MHz', 375000, 24, 70, 'new', 'Memori DDR4 8GB 3200MHz untuk PC desktop.'],
            ['penyimpanan-memori', 'Flash Drive USB 3.2 64GB', 115000, 30, 50, 'new', 'Flash drive 64GB dengan antarmuka USB 3.2 untuk pemindahan data harian.'],
            ['monitor-periferal', 'Monitor IPS 24 Inch 100Hz', 1499000, 8, 4200, 'new', 'Monitor Full HD panel IPS 100Hz untuk kerja, belajar, dan gaming kasual.'],
            ['monitor-periferal', 'Keyboard Mechanical Hot-swap', 549000, 15, 900, 'new', 'Keyboard mechanical ringkas dengan switch hot-swap dan pencahayaan RGB.'],
            ['monitor-periferal', 'Mouse Wireless Dual Mode', 189000, 25, 180, 'new', 'Mouse Bluetooth dan 2.4GHz untuk laptop, tablet, dan PC.'],
            ['jaringan-kabel', 'Kabel LAN Cat6 10 Meter', 85000, 40, 400, 'new', 'Kabel jaringan Cat6 siap pakai sepanjang 10 meter untuk router, PC, dan perangkat lain.'],
            ['jaringan-kabel', 'Router Wi-Fi 6 AX1500', 699000, 12, 650, 'new', 'Router dual-band Wi-Fi 6 untuk rumah, kos, atau kantor kecil.'],
            ['jaringan-kabel', 'USB Wi-Fi Adapter AC Dual Band', 169000, 20, 100, 'new', 'Adapter Wi-Fi USB dual-band untuk menambahkan koneksi nirkabel pada PC.'],
            ['aksesori-digital', 'USB-C Hub 6-in-1 HDMI', 399000, 18, 180, 'new', 'Hub USB-C dengan HDMI, USB 3.0, pembaca kartu, dan Power Delivery.'],
            ['aksesori-digital', 'Cooling Pad Laptop 5 Fan', 275000, 16, 1100, 'new', 'Cooling pad lima kipas untuk laptop hingga 17 inci.'],
            ['aksesori-digital', 'Charger GaN 65W Dual Port', 425000, 20, 220, 'new', 'Charger GaN ringkas 65W dengan USB-C dan USB-A untuk laptop maupun ponsel.'],
        ];

        foreach ($products as [$categorySlug, $name, $price, $stock, $weight, $condition, $description]) {
            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'store_id' => $store->id,
                    'category_id' => $categoryIds[$categorySlug],
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'weight_gram' => $weight,
                    'condition' => $condition,
                    'status' => 'active',
                    'sold_count' => 0,
                    'avg_rating' => 0,
                    'specs' => [],
                ],
            );
        }
    }
}
