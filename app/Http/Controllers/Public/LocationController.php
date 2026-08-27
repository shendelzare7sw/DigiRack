<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\DeliveryAreaService;

class LocationController extends Controller
{
    public function getProvinces()
    {
        return response()->json([
            ['id' => 'Banten', 'name' => 'Banten'],
        ]);
    }

    public function getCities($province_id, DeliveryAreaService $deliveryAreas)
    {
        abort_unless(strcasecmp((string) $province_id, 'Banten') === 0, 404);

        return response()->json($deliveryAreas->cities()->map(fn (array $city) => [
            'id' => $city['name'],
            'name' => preg_replace('/^(Kota|Kabupaten)\s+/i', '', $city['name']),
            'type' => str_starts_with($city['name'], 'Kabupaten') ? 'Kabupaten' : 'Kota',
            'full_name' => $city['name'],
            'postal_code' => $city['postal_code'],
            'fee' => $city['fee'],
        ])->values());
    }

    public function getDistricts(string $city, DeliveryAreaService $deliveryAreas)
    {
        $districts = $deliveryAreas->districts(urldecode($city));
        abort_if(empty($districts), 404);

        return response()->json(collect($districts)->map(fn (string $district) => [
            'id' => $district,
            'name' => $district,
        ])->values());
    }
}
