<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            'DKI Jakarta' => [
                ['name' => 'Jakarta Selatan', 'type' => 'Kota'],
                ['name' => 'Jakarta Pusat', 'type' => 'Kota'],
                ['name' => 'Jakarta Barat', 'type' => 'Kota'],
            ],
            'Jawa Barat' => [
                ['name' => 'Bandung', 'type' => 'Kota'],
                ['name' => 'Bogor', 'type' => 'Kota'],
                ['name' => 'Depok', 'type' => 'Kota'],
                ['name' => 'Bekasi', 'type' => 'Kota'],
            ],
            'Jawa Timur' => [
                ['name' => 'Surabaya', 'type' => 'Kota'],
                ['name' => 'Malang', 'type' => 'Kota'],
                ['name' => 'Sidoarjo', 'type' => 'Kabupaten'],
            ],
            'Jawa Tengah' => [
                ['name' => 'Semarang', 'type' => 'Kota'],
                ['name' => 'Surakarta (Solo)', 'type' => 'Kota'],
            ],
            'Banten' => [
                ['name' => 'Tangerang', 'type' => 'Kota'],
                ['name' => 'Tangerang Selatan', 'type' => 'Kota'],
            ],
            'DI Yogyakarta' => [
                ['name' => 'Yogyakarta', 'type' => 'Kota'],
                ['name' => 'Sleman', 'type' => 'Kabupaten'],
            ],
        ];

        foreach ($provinces as $provinceName => $cities) {
            $province = \App\Models\Province::create(['name' => $provinceName]);
            foreach ($cities as $city) {
                $city['province_id'] = $province->id;
                \App\Models\City::create($city);
            }
        }
    }
}
