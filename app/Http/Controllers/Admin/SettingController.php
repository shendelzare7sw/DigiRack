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
            'midtrans_iris_api_key' => 'nullable|string',
            'midtrans_is_production' => 'nullable|in:true,false',
            'platform_fee_per_item' => 'nullable|numeric|min:0',
            'withdrawal_fee_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $keys = [
            'midtrans_server_key', 'midtrans_client_key', 'midtrans_iris_api_key', 
            'midtrans_is_production', 'platform_fee_per_item', 'withdrawal_fee_percentage'
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
