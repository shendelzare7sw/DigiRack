<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key');
        $usedProducts = Product::query()
            ->with('category')
            ->where('status', 'active')
            ->where('condition', 'used')
            ->orderBy('name')
            ->get();
        $usedPartsSelectedProductIds = collect(json_decode(
            $settings['used_parts_section_product_ids'] ?? '[]',
            true,
        ))
            ->map(fn ($id) => (int) $id)
            ->all();

        return view('admin.settings.index', compact(
            'settings',
            'usedProducts',
            'usedPartsSelectedProductIds',
        ));
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
            'used_parts_section_enabled' => 'nullable|in:true,false',
            'used_parts_section_title' => 'nullable|string|max:120',
            'used_parts_section_description' => 'nullable|string|max:300',
            'used_parts_section_cta_label' => 'nullable|string|max:40',
            'used_parts_section_products_submitted' => 'nullable|boolean',
            'used_parts_section_product_ids' => 'nullable|array|max:10',
            'used_parts_section_product_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('products', 'id')->where(fn ($query) => $query
                    ->where('status', 'active')
                    ->where('condition', 'used')),
            ],
            'auto_complete_hours' => 'nullable|integer|min:0|max:168',
            'delivery_fee_kota_tangerang' => 'required|integer|min:0|max:10000000',
            'delivery_fee_tangerang_selatan' => 'required|integer|min:0|max:10000000',
            'delivery_fee_kabupaten_tangerang' => 'required|integer|min:0|max:10000000',
        ]);

        $keys = [
            'midtrans_server_key', 'midtrans_client_key', 'midtrans_merchant_id',
            'midtrans_is_production', 'platform_name', 'platform_email', 'platform_phone',
            'platform_address',
            'used_parts_section_enabled', 'used_parts_section_title',
            'used_parts_section_description', 'used_parts_section_cta_label',
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

        if ($request->boolean('used_parts_section_products_submitted')) {
            SystemSetting::updateOrCreate(
                ['key' => 'used_parts_section_product_ids'],
                ['value' => json_encode(array_map(
                    'intval',
                    $request->input('used_parts_section_product_ids', []),
                ))]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
