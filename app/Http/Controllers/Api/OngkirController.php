<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class OngkirController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * Hitung ongkos kirim (AJAX call)
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string|in:jne,pos,tiki',
        ]);

        try {
            $data = $this->rajaOngkir->getCost(
                $request->origin,
                $request->destination,
                $request->weight,
                $request->courier
            );

            // Extract just the costs array from Rajaongkir format
            if (isset($data['rajaongkir']['results'][0]['costs'])) {
                $costs = $data['rajaongkir']['results'][0]['costs'];
                if (count($costs) > 0) {
                    return response()->json([
                        'success' => true,
                        'data' => $costs
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Layanan kurir tidak tersedia untuk rute ini'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
