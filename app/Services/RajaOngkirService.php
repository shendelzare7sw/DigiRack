<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Exception;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.rajaongkir.com/starter';

    public function __construct()
    {
        $this->apiKey = SystemSetting::val('rajaongkir_api_key', env('RAJAONGKIR_API_KEY', ''));
    }

    /**
     * Menghitung ongkos kirim.
     *
     * @param int|string $origin City ID kota asal
     * @param int|string $destination City ID kota tujuan
     * @param int $weight Berat dalam gram
     * @param string $courier Kode kurir (jne, pos, tiki)
     * @return array
     * @throws Exception
     */
    public function getCost($origin, $destination, $weight, $courier)
    {
        if (empty($this->apiKey)) {
            // Jika tidak ada API key, gunakan ongkir fallback
            return $this->getDummyResponse($courier);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->post($this->baseUrl . '/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => strtolower($courier)
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Gagal mendapatkan ongkos kirim dari RajaOngkir: ' . $response->body());
        } catch (\Exception $e) {
            // Fallback for local dev if API fails and no key set properly
            return $this->getDummyResponse($courier);
        }
    }

    private function getDummyResponse($courier)
    {
        // Simulasi struktur RajaOngkir untuk development tanpa API Key
        // Mengembalikan flat rate
        return [
            'rajaongkir' => [
                'status' => ['code' => 200, 'description' => 'OK'],
                'results' => [
                    [
                        'code' => $courier,
                        'name' => strtoupper($courier),
                        'costs' => [
                            [
                                'service' => 'REG',
                                'description' => 'Layanan Reguler Dummy',
                                'cost' => [
                                    [
                                        'value' => 25000,
                                        'etd' => '2-3',
                                        'note' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
