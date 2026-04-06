<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getProvinces()
    {
        return response()->json(Province::orderBy('name')->get());
    }

    public function getCities($province_id)
    {
        return response()->json(City::where('province_id', $province_id)->orderBy('name')->get());
    }
}
