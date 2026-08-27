<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Collection;

class DeliveryAreaService
{
    public function areas(): Collection
    {
        return collect(config('digitalhook.delivery_areas', []));
    }

    public function cities(): Collection
    {
        return $this->areas()->map(function (array $area, string $city) {
            return [
                'name' => $city,
                'province' => $area['province'],
                'postal_code' => $area['postal_code'],
                'fee' => (int) $area['fee'],
            ];
        })->values();
    }

    public function districts(string $city): array
    {
        return array_values($this->areas()->get($this->normaliseCity($city), [])['districts'] ?? []);
    }

    public function isCovered(string $province, string $city, string $district): bool
    {
        $city = $this->normaliseCity($city);
        $area = $this->areas()->get($city);

        if (! $area || strcasecmp($area['province'], trim($province)) !== 0) {
            return false;
        }

        return collect($area['districts'])
            ->contains(fn (string $covered) => strcasecmp($covered, trim($district)) === 0);
    }

    public function shippingFee(Address $address): int
    {
        abort_unless($this->isCovered($address->province, $address->city, $address->district), 422,
            'Alamat tidak berada dalam wilayah pengantaran Digital Hook.');

        return (int) $this->areas()->get($this->normaliseCity($address->city))['fee'];
    }

    public function normaliseCity(string $city): string
    {
        $city = trim($city);

        if (preg_match('/^(Kota|Kabupaten)\s+/i', $city)) {
            return collect($this->areas()->keys())
                ->first(fn (string $covered) => strcasecmp($covered, $city) === 0, $city);
        }

        return collect($this->areas()->keys())
            ->first(fn (string $covered) => strcasecmp(preg_replace('/^(Kota|Kabupaten)\s+/i', '', $covered), $city) === 0, $city);
    }
}
