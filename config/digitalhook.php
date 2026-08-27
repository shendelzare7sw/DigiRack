<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Digital Hook delivery coverage
    |--------------------------------------------------------------------------
    |
    | The store only accepts same-day orders that can be delivered by its own
    | courier. District names are intentionally explicit: an address is outside
    | coverage when it does not appear here. Fees can be tuned without changing
    | checkout code.
    |
    */
    'delivery_areas' => [
        'Kota Tangerang' => [
            'province' => 'Banten',
            'postal_code' => '15100',
            'fee' => 20_000,
            'districts' => [
                'Batuceper', 'Benda', 'Cibodas', 'Ciledug', 'Cipondoh',
                'Jatiuwung', 'Karang Tengah', 'Karawaci', 'Larangan',
                'Neglasari', 'Periuk', 'Pinang', 'Tangerang',
            ],
        ],
        'Kota Tangerang Selatan' => [
            'province' => 'Banten',
            'postal_code' => '15300',
            'fee' => 25_000,
            'districts' => [
                'Ciputat', 'Ciputat Timur', 'Pamulang', 'Pondok Aren',
                'Serpong', 'Serpong Utara', 'Setu',
            ],
        ],
        'Kabupaten Tangerang' => [
            'province' => 'Banten',
            'postal_code' => '15700',
            'fee' => 30_000,
            'districts' => [
                'Cikupa', 'Cisauk', 'Curug', 'Kelapa Dua', 'Kosambi', 'Legok',
                'Pagedangan', 'Panongan', 'Pasar Kemis', 'Sepatan',
                'Sepatan Timur', 'Teluknaga', 'Tigaraksa',
            ],
        ],
    ],

    'courier_name' => 'Kurir Digital Hook Same Day',
    'order_cutoff' => env('DIGITAL_HOOK_SAMEDAY_CUTOFF', '15:00'),
];
