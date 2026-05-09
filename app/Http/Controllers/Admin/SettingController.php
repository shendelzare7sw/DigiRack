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
            'midtrans_iris_api_key' => 'nullable|string',
            'midtrans_is_production' => 'nullable|in:true,false',
            'platform_fee_per_item' => 'nullable|numeric|min:0',
            'withdrawal_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'rajaongkir_api_key' => 'nullable|string',
            'platform_name' => 'nullable|string',
            'platform_email' => 'nullable|email',
            'platform_phone' => 'nullable|string',
        ]);

        $keys = [
            'midtrans_server_key', 'midtrans_client_key', 'midtrans_merchant_id',
            'midtrans_iris_api_key', 'midtrans_is_production', 
            'platform_fee_per_item', 'withdrawal_fee_percentage',
            'rajaongkir_api_key', 'platform_name', 'platform_email', 'platform_phone'
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
