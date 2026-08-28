<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'midtrans_server_key' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_merchant_id' => 'nullable|string',
            'midtrans_is_production' => 'nullable|in:true,false',
            'platform_name' => 'nullable|string|max:100',
            'platform_email' => 'nullable|email|max:255',
            'platform_phone' => 'nullable|string|max:30',
            'platform_address' => 'nullable|string|max:500',
            'auto_complete_hours' => 'nullable|integer|min:0|max:168',
            'delivery_fee_kota_tangerang' => 'required|integer|min:0|max:10000000',
            'delivery_fee_tangerang_selatan' => 'required|integer|min:0|max:10000000',
            'delivery_fee_kabupaten_tangerang' => 'required|integer|min:0|max:10000000',
        ]);

        $keys = [
            'midtrans_server_key', 'midtrans_client_key', 'midtrans_merchant_id',
            'midtrans_is_production', 'platform_name', 'platform_email', 'platform_phone',
            'platform_address',
            'auto_complete_hours',
            'delivery_fee_kota_tangerang', 'delivery_fee_tangerang_selatan',
            'delivery_fee_kabupaten_tangerang',
        ];

        foreach ($request->only($keys) as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
