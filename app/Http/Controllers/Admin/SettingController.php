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
            'platform_name' => 'nullable|string',
            'platform_email' => 'nullable|email',
            'platform_phone' => 'nullable|string',
            'auto_complete_hours' => 'nullable|integer|min:0|max:168',
        ]);

        $keys = [
            'midtrans_server_key', 'midtrans_client_key', 'midtrans_merchant_id',
            'midtrans_is_production', 'platform_name', 'platform_email', 'platform_phone',
            'auto_complete_hours'
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
