<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $store1 = Store::where('slug', 'jaringan-nusantara')->first();
        $store2 = Store::where('slug', 'netpro-supply')->first();

        $products = [
            // Networking (category_id = 1)
            ['store_id' => $store1->id, 'category_id' => 1, 'name' => 'Mikrotik RB750Gr3 hEX', 'price' => 1150000, 'stock' => 50, 'weight_gram' => 350, 'description' => 'Router Mikrotik 5 port Gigabit Ethernet, cocok untuk SOHO dan small office. Dilengkapi RouterOS Level 4.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Mikrotik'], ['label' => 'Port', 'value' => '5x Gigabit'], ['label' => 'CPU', 'value' => 'MT7621A 880MHz']])],
            ['store_id' => $store2->id, 'category_id' => 1, 'name' => 'TP-Link TL-SG1024D Switch 24 Port', 'price' => 1850000, 'stock' => 30, 'weight_gram' => 2500, 'description' => 'Unmanaged Switch 24 Port Gigabit, ideal untuk jaringan kantor menengah.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'TP-Link'], ['label' => 'Port', 'value' => '24x Gigabit'], ['label' => 'Tipe', 'value' => 'Unmanaged']])],
            ['store_id' => $store1->id, 'category_id' => 1, 'name' => 'Cisco SG350-28 Managed Switch', 'price' => 8500000, 'stock' => 15, 'weight_gram' => 3200, 'description' => 'Managed Switch 28 port dengan fitur Layer 3 dasar, VLAN, QoS.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Cisco'], ['label' => 'Port', 'value' => '28x Gigabit'], ['label' => 'Tipe', 'value' => 'Managed L3']])],
            ['store_id' => $store2->id, 'category_id' => 1, 'name' => 'Ubiquiti EdgeRouter X', 'price' => 950000, 'stock' => 40, 'weight_gram' => 260, 'description' => 'Router compact dengan 5 port Gigabit, mendukung PoE passthrough. Ideal untuk jaringan kecil-menengah.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Ubiquiti'], ['label' => 'Port', 'value' => '5x Gigabit'], ['label' => 'PoE', 'value' => 'Passive PoE']])],

            // Konektivitas (category_id = 2)
            ['store_id' => $store1->id, 'category_id' => 2, 'name' => 'Kabel UTP Cat6 Belden 305m', 'price' => 2200000, 'stock' => 25, 'weight_gram' => 12000, 'description' => 'Kabel LAN Cat6 original Belden, panjang 305 meter per box. Cocok untuk instalasi gedung dan perkantoran.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Belden'], ['label' => 'Kategori', 'value' => 'Cat6'], ['label' => 'Panjang', 'value' => '305m']])],
            ['store_id' => $store2->id, 'category_id' => 2, 'name' => 'Patch Panel 24 Port Cat6', 'price' => 450000, 'stock' => 35, 'weight_gram' => 800, 'description' => 'Patch Panel 24 port Cat6 untuk rack 19 inch. Build quality premium.', 'specs' => json_encode([['label' => 'Port', 'value' => '24'], ['label' => 'Kategori', 'value' => 'Cat6'], ['label' => 'Ukuran', 'value' => '19 inch 1U']])],
            ['store_id' => $store1->id, 'category_id' => 2, 'name' => 'Fiber Optik Patch Cord SC-SC 3m', 'price' => 85000, 'stock' => 100, 'weight_gram' => 100, 'description' => 'Patch cord fiber optik Single Mode SC-SC, panjang 3 meter.', 'specs' => json_encode([['label' => 'Connector', 'value' => 'SC-SC'], ['label' => 'Mode', 'value' => 'Single Mode'], ['label' => 'Panjang', 'value' => '3m']])],
            ['store_id' => $store2->id, 'category_id' => 2, 'name' => 'RJ45 Connector Cat6 (100pcs)', 'price' => 120000, 'stock' => 200, 'weight_gram' => 300, 'description' => 'Konektor RJ45 Cat6 gold plated, isi 100 pcs per pack.', 'specs' => json_encode([['label' => 'Tipe', 'value' => 'RJ45 Cat6'], ['label' => 'Isi', 'value' => '100 pcs'], ['label' => 'Material', 'value' => 'Gold Plated']])],

            // Wireless (category_id = 3)
            ['store_id' => $store1->id, 'category_id' => 3, 'name' => 'Ubiquiti UniFi AP AC Pro', 'price' => 2850000, 'stock' => 20, 'weight_gram' => 350, 'description' => 'Access Point dual-band 802.11ac, throughput hingga 1750Mbps. Ideal untuk deployment enterprise.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Ubiquiti'], ['label' => 'Standard', 'value' => '802.11ac'], ['label' => 'Speed', 'value' => '1750Mbps']])],
            ['store_id' => $store2->id, 'category_id' => 3, 'name' => 'TP-Link Archer AX50 WiFi 6 Router', 'price' => 1650000, 'stock' => 25, 'weight_gram' => 600, 'description' => 'Router WiFi 6 dual-band, kecepatan hingga 3Gbps. Cocok untuk rumah dan kantor kecil.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'TP-Link'], ['label' => 'Standard', 'value' => 'WiFi 6 (802.11ax)'], ['label' => 'Speed', 'value' => '3Gbps']])],
            ['store_id' => $store1->id, 'category_id' => 3, 'name' => 'Mikrotik SXTsq 5 ac', 'price' => 780000, 'stock' => 45, 'weight_gram' => 400, 'description' => 'CPE outdoor 5GHz 802.11ac, antena terintegrasi 16dBi. Jarak hingga 3km.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Mikrotik'], ['label' => 'Frekuensi', 'value' => '5GHz'], ['label' => 'Gain', 'value' => '16dBi']])],
            ['store_id' => $store2->id, 'category_id' => 3, 'name' => 'Tenda O6 Outdoor CPE 5GHz', 'price' => 650000, 'stock' => 35, 'weight_gram' => 500, 'description' => 'CPE outdoor point-to-point 5GHz, jarak hingga 10km. Tahan cuaca IP65.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Tenda'], ['label' => 'Frekuensi', 'value' => '5GHz'], ['label' => 'Jarak', 'value' => '10km']])],

            // Server & Hardware (category_id = 4)
            ['store_id' => $store1->id, 'category_id' => 4, 'name' => 'Dell PowerEdge T40 Server', 'price' => 15500000, 'stock' => 5, 'weight_gram' => 12000, 'description' => 'Server tower entry-level Intel Xeon E-2224G, RAM 8GB ECC, HDD 1TB. Ideal untuk bisnis kecil.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Dell'], ['label' => 'CPU', 'value' => 'Xeon E-2224G'], ['label' => 'RAM', 'value' => '8GB ECC']])],
            ['store_id' => $store2->id, 'category_id' => 4, 'name' => 'Samsung 870 EVO 1TB SSD', 'price' => 1750000, 'stock' => 40, 'weight_gram' => 60, 'description' => 'SSD SATA 2.5" kapasitas 1TB, kecepatan baca 560MB/s. Cocok untuk upgrade server dan workstation.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Samsung'], ['label' => 'Kapasitas', 'value' => '1TB'], ['label' => 'Interface', 'value' => 'SATA III']])],
            ['store_id' => $store1->id, 'category_id' => 4, 'name' => 'Kingston Server RAM 16GB DDR4 ECC', 'price' => 1250000, 'stock' => 30, 'weight_gram' => 50, 'description' => 'RAM DDR4 ECC UDIMM 16GB 2666MHz untuk server dan workstation.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Kingston'], ['label' => 'Kapasitas', 'value' => '16GB'], ['label' => 'Tipe', 'value' => 'DDR4 ECC']])],
            ['store_id' => $store2->id, 'category_id' => 4, 'name' => 'Synology DS220+ NAS 2-Bay', 'price' => 5500000, 'stock' => 10, 'weight_gram' => 1500, 'description' => 'NAS 2-bay untuk backup data dan media server. Dual-core Intel Celeron J4025.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'Synology'], ['label' => 'Bay', 'value' => '2'], ['label' => 'CPU', 'value' => 'Celeron J4025']])],

            // Power & Infrastruktur (category_id = 5)
            ['store_id' => $store1->id, 'category_id' => 5, 'name' => 'APC Smart-UPS 1500VA', 'price' => 5200000, 'stock' => 12, 'weight_gram' => 18000, 'description' => 'UPS line-interactive 1500VA/1000W, output sinewave murni. Cocok untuk server dan perangkat kritis.', 'specs' => json_encode([['label' => 'Brand', 'value' => 'APC'], ['label' => 'Kapasitas', 'value' => '1500VA / 1000W'], ['label' => 'Tipe', 'value' => 'Line Interactive']])],
            ['store_id' => $store2->id, 'category_id' => 5, 'name' => 'Rack Server 42U 19" Standing', 'price' => 4500000, 'stock' => 8, 'weight_gram' => 65000, 'description' => 'Rack cabinet 42U 19 inch dengan pintu perforated, kunci keamanan, dan cable management.', 'specs' => json_encode([['label' => 'Ukuran', 'value' => '42U 19"'], ['label' => 'Tipe', 'value' => 'Standing'], ['label' => 'Pintu', 'value' => 'Perforated']])],
            ['store_id' => $store1->id, 'category_id' => 5, 'name' => 'PDU Rack Mount 8 Outlet', 'price' => 850000, 'stock' => 20, 'weight_gram' => 1500, 'description' => 'Power Distribution Unit 8 outlet untuk rack server 19 inch. Rating hingga 16A.', 'specs' => json_encode([['label' => 'Outlet', 'value' => '8'], ['label' => 'Rating', 'value' => '16A'], ['label' => 'Mount', 'value' => '19" Rack']])],
            ['store_id' => $store2->id, 'category_id' => 5, 'name' => 'Fan Tray 4 Unit Rack Cooling', 'price' => 650000, 'stock' => 15, 'weight_gram' => 2000, 'description' => 'Kit pendingin rack dengan 4 kipas untuk ventilasi optimal di rack cabinet.', 'specs' => json_encode([['label' => 'Fan', 'value' => '4 unit'], ['label' => 'Tipe', 'value' => 'Rack Mount'], ['label' => 'Ukuran', 'value' => '19" 1U']])],

            // Tools & Aksesoris (category_id = 6)
            ['store_id' => $store1->id, 'category_id' => 6, 'name' => 'Crimping Tool RJ45 Cat5e/Cat6', 'price' => 185000, 'stock' => 60, 'weight_gram' => 350, 'description' => 'Tang crimping profesional untuk konektor RJ45 Cat5e dan Cat6. Ergonomis dan presisi tinggi.', 'specs' => json_encode([['label' => 'Tipe', 'value' => 'RJ45'], ['label' => 'Kompatibel', 'value' => 'Cat5e/Cat6'], ['label' => 'Material', 'value' => 'Steel']])],
            ['store_id' => $store2->id, 'category_id' => 6, 'name' => 'LAN Cable Tester Digital', 'price' => 250000, 'stock' => 45, 'weight_gram' => 200, 'description' => 'Network cable tester digital untuk uji konektivitas kabel LAN. Mendukung RJ45 dan RJ11.', 'specs' => json_encode([['label' => 'Tipe', 'value' => 'Digital'], ['label' => 'Support', 'value' => 'RJ45/RJ11'], ['label' => 'Display', 'value' => 'LED']])],
            ['store_id' => $store1->id, 'category_id' => 6, 'name' => 'SFP Module 1.25G Single Mode', 'price' => 350000, 'stock' => 55, 'weight_gram' => 50, 'description' => 'SFP Transceiver 1.25Gbps Single Mode, jarak 20km. Kompatibel dengan berbagai switch managed.', 'specs' => json_encode([['label' => 'Speed', 'value' => '1.25Gbps'], ['label' => 'Mode', 'value' => 'Single Mode'], ['label' => 'Jarak', 'value' => '20km']])],
            ['store_id' => $store2->id, 'category_id' => 6, 'name' => 'Network Tool Kit Lengkap', 'price' => 450000, 'stock' => 30, 'weight_gram' => 1200, 'description' => 'Set lengkap alat jaringan: crimping tool, cable tester, punch down tool, stripper, konektor, dll.', 'specs' => json_encode([['label' => 'Isi', 'value' => '11 items'], ['label' => 'Koper', 'value' => 'Included'], ['label' => 'Tipe', 'value' => 'Complete Kit']])],
        ];

        foreach ($products as $p) {
            Product::create(array_merge($p, [
                'slug' => Str::slug($p['name']),
                'condition' => 'new',
                'status' => 'active',
                'sold_count' => rand(5, 50),
                'avg_rating' => round(rand(35, 50) / 10, 1),
            ]));
        }
    }
}
