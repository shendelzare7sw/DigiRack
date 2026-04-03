<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Networking',
                'slug' => 'networking',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M5.636 4.575a.75.75 0 0 1 0 1.061 9.004 9.004 0 0 0 0 12.728.75.75 0 1 1-1.06 1.06c-4.101-4.1-4.101-10.748 0-14.849a.75.75 0 0 1 1.06 0Zm12.728 0a.75.75 0 0 1 1.06 0c4.101 4.1 4.101 10.749 0 14.85a.75.75 0 1 1-1.06-1.061 9.004 9.004 0 0 0 0-12.728.75.75 0 0 1 0-1.06ZM7.757 6.697a.75.75 0 0 1 0 1.06 6.004 6.004 0 0 0 0 8.486.75.75 0 0 1-1.06 1.06 7.504 7.504 0 0 1 0-10.606.75.75 0 0 1 1.06 0Zm8.486 0a.75.75 0 0 1 1.06 0 7.504 7.504 0 0 1 0 10.607.75.75 0 1 1-1.06-1.06 6.004 6.004 0 0 0 0-8.486.75.75 0 0 1 0-1.06ZM9.879 8.818a.75.75 0 0 1 0 1.06 3.003 3.003 0 0 0 0 4.243.75.75 0 1 1-1.061 1.061 4.503 4.503 0 0 1 0-6.364.75.75 0 0 1 1.06 0Zm4.242 0a.75.75 0 0 1 1.061 0 4.503 4.503 0 0 1 0 6.364.75.75 0 0 1-1.06-1.06 3.003 3.003 0 0 0 0-4.243.75.75 0 0 1 0-1.06ZM10.875 12a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" /></svg>',
                'description' => 'Switch, Router, Mikrotik, Access Point',
                'sort_order' => 1,
            ],
            [
                'name' => 'Konektivitas',
                'slug' => 'konektivitas',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M18.97 3.659a2.25 2.25 0 0 0-3.182 0l-10.94 10.94a3.75 3.75 0 1 0 5.304 5.303l7.693-7.693a.75.75 0 0 1 1.06 1.06l-7.693 7.693a5.25 5.25 0 1 1-7.424-7.424l10.939-10.94a3.75 3.75 0 1 1 5.303 5.304L9.097 18.835a2.25 2.25 0 0 1-3.182-3.182l8.31-8.31a.75.75 0 0 1 1.061 1.06l-8.31 8.311a.75.75 0 0 0 1.06 1.06l10.94-10.94a2.25 2.25 0 0 0 0-3.175Z" clip-rule="evenodd" /></svg>',
                'description' => 'Kabel LAN, Fiber Optik, Patch Panel',
                'sort_order' => 2,
            ],
            [
                'name' => 'Wireless',
                'slug' => 'wireless',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M1.371 8.143c5.858-5.857 15.356-5.857 21.213 0a.75.75 0 0 1 0 1.061.75.75 0 0 1-1.06 0 13.5 13.5 0 0 0-19.093 0 .75.75 0 0 1-1.06-1.06Zm3.535 3.535a10.5 10.5 0 0 1 14.142 0 .75.75 0 1 1-1.06 1.06 9 9 0 0 0-12.022 0 .75.75 0 1 1-1.06-1.06Zm3.536 3.535a6 6 0 0 1 7.07 0 .75.75 0 1 1-.884 1.212 4.5 4.5 0 0 0-5.302 0 .75.75 0 0 1-.884-1.212ZM12 16.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z" clip-rule="evenodd" /></svg>',
                'description' => 'Modem, Repeater, CPE, Antena Grid',
                'sort_order' => 3,
            ],
            [
                'name' => 'Server & Hardware',
                'slug' => 'server-hardware',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M4.08 5.227A3 3 0 0 1 6.979 3H17.02a3 3 0 0 1 2.9 2.227l2.113 7.926A5.228 5.228 0 0 0 18.75 12H5.25a5.228 5.228 0 0 0-3.284 1.153L4.08 5.227ZM24 15.75a3.75 3.75 0 0 1-3.75 3.75H3.75A3.75 3.75 0 0 1 0 15.75v-.75a4.5 4.5 0 0 1 4.5-4.5h15a4.5 4.5 0 0 1 4.5 4.5v.75Zm-5.165-1.875a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Zm-2.953 0a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Z" /></svg>',
                'description' => 'Server, RAM ECC, HDD/SSD Server, NAS',
                'sort_order' => 4,
            ],
            [
                'name' => 'Power & Infrastruktur',
                'slug' => 'power-infrastruktur',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.75a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .913-.143Z" clip-rule="evenodd" /></svg>',
                'description' => 'UPS, PDU, Rack Cabinet, Cooling',
                'sort_order' => 5,
            ],
            [
                'name' => 'Tools & Aksesoris',
                'slug' => 'tools-aksesoris',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 6.75a5.25 5.25 0 0 1 6.775-5.025.75.75 0 0 1 .313 1.248l-3.32 3.319c.063.475.276.934.641 1.299.365.365.824.578 1.3.64l3.318-3.319a.75.75 0 0 1 1.248.313 5.25 5.25 0 0 1-5.472 6.756c-1.018-.086-1.87.1-2.309.634L7.344 21.3A3.298 3.298 0 1 1 2.7 16.657l8.684-7.151c.533-.44.72-1.291.634-2.309A5.342 5.342 0 0 1 12 6.75ZM4.117 19.125a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Z" clip-rule="evenodd" /><path d="m10.076 8.64-2.201-2.2V4.874a.75.75 0 0 0-.364-.643l-3.75-2.25a.75.75 0 0 0-.916.113l-.75.75a.75.75 0 0 0-.113.916l2.25 3.75a.75.75 0 0 0 .643.364h1.564l2.062 2.062 1.575-1.297Z" /><path fill-rule="evenodd" d="m12.556 17.329 4.183 4.182a3.375 3.375 0 0 0 4.773-4.773l-3.306-3.305a6.803 6.803 0 0 1-1.53.043c-.394-.034-.682-.006-.867.042a.589.589 0 0 0-.167.063l-3.086 3.748Zm3.414-1.36a.75.75 0 0 1 1.06 0l1.875 1.876a.75.75 0 1 1-1.06 1.06L15.97 17.03a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>',
                'description' => 'Crimping Tool, LAN Tester, SFP Module',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }
    }
}
