<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing data
        City::query()->delete();
        Province::query()->delete();

        $data = [
            'Aceh' => [
                ['Banda Aceh','Kota','23111'],['Lhokseumawe','Kota','24300'],['Langsa','Kota','24400'],['Sabang','Kota','23500'],['Aceh Besar','Kabupaten','23300'],['Aceh Utara','Kabupaten','24300'],
            ],
            'Sumatera Utara' => [
                ['Medan','Kota','20100'],['Binjai','Kota','20700'],['Pematang Siantar','Kota','21100'],['Tebing Tinggi','Kota','20600'],['Deli Serdang','Kabupaten','20500'],['Langkat','Kabupaten','20700'],['Simalungun','Kabupaten','21100'],
            ],
            'Sumatera Barat' => [
                ['Padang','Kota','25100'],['Bukittinggi','Kota','26100'],['Payakumbuh','Kota','26200'],['Solok','Kota','27300'],['Padang Panjang','Kota','27100'],['Agam','Kabupaten','26400'],
            ],
            'Riau' => [
                ['Pekanbaru','Kota','28100'],['Dumai','Kota','28800'],['Kampar','Kabupaten','28400'],['Bengkalis','Kabupaten','28700'],['Siak','Kabupaten','28600'],
            ],
            'Jambi' => [
                ['Jambi','Kota','36100'],['Sungai Penuh','Kota','37100'],['Muaro Jambi','Kabupaten','36300'],['Batang Hari','Kabupaten','36600'],
            ],
            'Sumatera Selatan' => [
                ['Palembang','Kota','30100'],['Prabumulih','Kota','31100'],['Lubuklinggau','Kota','31600'],['Pagar Alam','Kota','31500'],['Ogan Komering Ilir','Kabupaten','30600'],['Banyuasin','Kabupaten','30900'],
            ],
            'Bengkulu' => [
                ['Bengkulu','Kota','38100'],['Rejang Lebong','Kabupaten','39100'],['Kepahiang','Kabupaten','39100'],
            ],
            'Lampung' => [
                ['Bandar Lampung','Kota','35100'],['Metro','Kota','34100'],['Lampung Tengah','Kabupaten','34100'],['Lampung Selatan','Kabupaten','35500'],['Lampung Utara','Kabupaten','34500'],
            ],
            'Kepulauan Bangka Belitung' => [
                ['Pangkal Pinang','Kota','33100'],['Bangka','Kabupaten','33200'],['Belitung','Kabupaten','33400'],
            ],
            'Kepulauan Riau' => [
                ['Batam','Kota','29400'],['Tanjung Pinang','Kota','29100'],['Bintan','Kabupaten','29100'],['Karimun','Kabupaten','29600'],
            ],
            'DKI Jakarta' => [
                ['Jakarta Pusat','Kota','10100'],['Jakarta Selatan','Kota','12100'],['Jakarta Barat','Kota','11100'],['Jakarta Timur','Kota','13100'],['Jakarta Utara','Kota','14100'],['Kepulauan Seribu','Kabupaten','14500'],
            ],
            'Jawa Barat' => [
                ['Bandung','Kota','40100'],['Bogor','Kota','16100'],['Depok','Kota','16400'],['Bekasi','Kota','17100'],['Cimahi','Kota','40500'],['Tasikmalaya','Kota','46100'],['Sukabumi','Kota','43100'],['Cirebon','Kota','45100'],['Karawang','Kabupaten','41300'],['Garut','Kabupaten','44100'],['Subang','Kabupaten','41200'],['Purwakarta','Kabupaten','41100'],['Bandung Barat','Kabupaten','40500'],['Sumedang','Kabupaten','45300'],
            ],
            'Jawa Tengah' => [
                ['Semarang','Kota','50100'],['Surakarta','Kota','57100'],['Magelang','Kota','56100'],['Salatiga','Kota','50700'],['Pekalongan','Kota','51100'],['Tegal','Kota','52100'],['Klaten','Kabupaten','57400'],['Banyumas','Kabupaten','53100'],['Kudus','Kabupaten','59300'],['Jepara','Kabupaten','59400'],['Boyolali','Kabupaten','57300'],['Cilacap','Kabupaten','53200'],
            ],
            'DI Yogyakarta' => [
                ['Yogyakarta','Kota','55100'],['Sleman','Kabupaten','55500'],['Bantul','Kabupaten','55700'],['Gunung Kidul','Kabupaten','55800'],['Kulon Progo','Kabupaten','55600'],
            ],
            'Jawa Timur' => [
                ['Surabaya','Kota','60100'],['Malang','Kota','65100'],['Kediri','Kota','64100'],['Blitar','Kota','66100'],['Mojokerto','Kota','61300'],['Madiun','Kota','63100'],['Batu','Kota','65300'],['Sidoarjo','Kabupaten','61200'],['Gresik','Kabupaten','61100'],['Jember','Kabupaten','68100'],['Pasuruan','Kabupaten','67100'],['Lamongan','Kabupaten','62200'],['Tuban','Kabupaten','62300'],['Bojonegoro','Kabupaten','62100'],
            ],
            'Banten' => [
                ['Tangerang','Kota','15100'],['Tangerang Selatan','Kota','15300'],['Serang','Kota','42100'],['Cilegon','Kota','42400'],['Pandeglang','Kabupaten','42200'],['Lebak','Kabupaten','42300'],
            ],
            'Bali' => [
                ['Denpasar','Kota','80100'],['Badung','Kabupaten','80300'],['Gianyar','Kabupaten','80500'],['Tabanan','Kabupaten','82100'],['Buleleng','Kabupaten','81100'],['Karangasem','Kabupaten','80800'],
            ],
            'Nusa Tenggara Barat' => [
                ['Mataram','Kota','83100'],['Bima','Kota','84100'],['Lombok Barat','Kabupaten','83300'],['Lombok Timur','Kabupaten','83600'],['Sumbawa','Kabupaten','84300'],
            ],
            'Nusa Tenggara Timur' => [
                ['Kupang','Kota','85100'],['Ende','Kabupaten','86300'],['Manggarai','Kabupaten','86500'],['Sikka','Kabupaten','86100'],['Timor Tengah Selatan','Kabupaten','85500'],
            ],
            'Kalimantan Barat' => [
                ['Pontianak','Kota','78100'],['Singkawang','Kota','79100'],['Kubu Raya','Kabupaten','78300'],['Sambas','Kabupaten','79400'],['Ketapang','Kabupaten','78800'],
            ],
            'Kalimantan Tengah' => [
                ['Palangka Raya','Kota','73100'],['Kotawaringin Barat','Kabupaten','74100'],['Kotawaringin Timur','Kabupaten','74300'],['Kapuas','Kabupaten','73500'],
            ],
            'Kalimantan Selatan' => [
                ['Banjarmasin','Kota','70100'],['Banjarbaru','Kota','70700'],['Banjar','Kabupaten','70600'],['Tanah Laut','Kabupaten','70800'],['Hulu Sungai Selatan','Kabupaten','71200'],
            ],
            'Kalimantan Timur' => [
                ['Samarinda','Kota','75100'],['Balikpapan','Kota','76100'],['Bontang','Kota','75300'],['Kutai Kartanegara','Kabupaten','75500'],['Berau','Kabupaten','77300'],['Penajam Paser Utara','Kabupaten','76100'],
            ],
            'Kalimantan Utara' => [
                ['Tarakan','Kota','77100'],['Bulungan','Kabupaten','77200'],['Malinau','Kabupaten','77500'],['Nunukan','Kabupaten','77400'],
            ],
            'Sulawesi Utara' => [
                ['Manado','Kota','95100'],['Bitung','Kota','95500'],['Tomohon','Kota','95300'],['Minahasa','Kabupaten','95600'],['Bolaang Mongondow','Kabupaten','95700'],
            ],
            'Sulawesi Tengah' => [
                ['Palu','Kota','94100'],['Donggala','Kabupaten','94300'],['Parigi Moutong','Kabupaten','94400'],['Banggai','Kabupaten','94700'],
            ],
            'Sulawesi Selatan' => [
                ['Makassar','Kota','90100'],['Parepare','Kota','91100'],['Palopo','Kota','91900'],['Maros','Kabupaten','90500'],['Gowa','Kabupaten','92100'],['Bone','Kabupaten','92700'],['Wajo','Kabupaten','90900'],['Bulukumba','Kabupaten','92500'],
            ],
            'Sulawesi Tenggara' => [
                ['Kendari','Kota','93100'],['Bau-Bau','Kota','93700'],['Konawe','Kabupaten','93400'],['Muna','Kabupaten','93600'],
            ],
            'Gorontalo' => [
                ['Gorontalo','Kota','96100'],['Bone Bolango','Kabupaten','96500'],['Gorontalo (Kab)','Kabupaten','96200'],
            ],
            'Sulawesi Barat' => [
                ['Mamuju','Kabupaten','91500'],['Polewali Mandar','Kabupaten','91300'],['Majene','Kabupaten','91400'],
            ],
            'Maluku' => [
                ['Ambon','Kota','97100'],['Tual','Kota','97600'],['Maluku Tengah','Kabupaten','97500'],['Seram Bagian Barat','Kabupaten','97500'],
            ],
            'Maluku Utara' => [
                ['Ternate','Kota','97700'],['Tidore Kepulauan','Kota','97800'],['Halmahera Utara','Kabupaten','97700'],
            ],
            'Papua' => [
                ['Jayapura','Kota','99100'],['Merauke','Kabupaten','99600'],['Mimika','Kabupaten','99900'],['Jayawijaya','Kabupaten','99500'],['Biak Numfor','Kabupaten','98100'],
            ],
            'Papua Barat' => [
                ['Sorong','Kota','98400'],['Manokwari','Kabupaten','98300'],['Fakfak','Kabupaten','98600'],['Raja Ampat','Kabupaten','98400'],
            ],
        ];

        foreach ($data as $provName => $cities) {
            $prov = Province::create(['name' => $provName]);
            foreach ($cities as [$cityName, $type, $postalCode]) {
                City::create([
                    'province_id' => $prov->id,
                    'name' => $cityName,
                    'type' => $type,
                    'postal_code' => $postalCode,
                ]);
            }
        }
    }
}
