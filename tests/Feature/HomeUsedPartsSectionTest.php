<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class HomeUsedPartsSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_automatically_displays_available_used_products(): void
    {
        [$admin, $store, $category] = $this->catalogContext();
        $usedProduct = $this->product($store, $category, 'RAM Laptop Second', 'used');
        $this->product($store, $category, 'RAM Laptop Baru', 'new');
        $this->product($store, $category, 'SSD Second Habis', 'used', stock: 0);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Mencari pretelan untuk perbaikan perangkat?')
            ->assertViewHas('usedParts', function (Collection $products) use ($usedProduct) {
                return $products->pluck('id')->all() === [$usedProduct->id];
            });
    }

    public function test_admin_can_customize_section_and_select_used_products(): void
    {
        [$admin, $store, $category] = $this->catalogContext();
        $selectedProduct = $this->product($store, $category, 'Motherboard Laptop Second', 'used');
        $this->product($store, $category, 'Keyboard Baru', 'new');

        $this->actingAs($admin)->post(route('admin.settings.store'), [
            'used_parts_section_enabled' => 'true',
            'used_parts_section_title' => 'Komponen second untuk perangkatmu',
            'used_parts_section_description' => 'Pilihan komponen yang bisa digunakan kembali.',
            'used_parts_section_cta_label' => 'Lihat semua komponen',
            'used_parts_section_products_submitted' => '1',
            'used_parts_section_product_ids' => [$selectedProduct->id],
            'delivery_fee_kota_tangerang' => 0,
            'delivery_fee_tangerang_selatan' => 0,
            'delivery_fee_kabupaten_tangerang' => 0,
        ])->assertSessionHas('success');

        $this->assertSame('Komponen second untuk perangkatmu', SystemSetting::val('used_parts_section_title'));
        $this->assertSame(json_encode([$selectedProduct->id]), SystemSetting::val('used_parts_section_product_ids'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Komponen second untuk perangkatmu')
            ->assertSee('Pilihan komponen yang bisa digunakan kembali.')
            ->assertSee('Lihat semua komponen')
            ->assertViewHas('usedParts', fn (Collection $products) => $products->pluck('id')->all() === [$selectedProduct->id]);
    }

    public function test_admin_can_hide_used_parts_section(): void
    {
        [$admin, $store, $category] = $this->catalogContext();
        $this->product($store, $category, 'Prosesor Second', 'used');

        SystemSetting::create(['key' => 'used_parts_section_enabled', 'value' => 'false']);
        SystemSetting::create(['key' => 'used_parts_section_title', 'value' => 'Judul yang disembunyikan']);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Judul yang disembunyikan')
            ->assertViewHas('usedParts', fn (Collection $products) => $products->isEmpty());
    }

    public function test_admin_cannot_select_new_product_for_used_parts_section(): void
    {
        [$admin, $store, $category] = $this->catalogContext();
        $newProduct = $this->product($store, $category, 'Produk Baru', 'new');

        $this->actingAs($admin)->post(route('admin.settings.store'), [
            'used_parts_section_products_submitted' => '1',
            'used_parts_section_product_ids' => [$newProduct->id],
            'delivery_fee_kota_tangerang' => 0,
            'delivery_fee_tangerang_selatan' => 0,
            'delivery_fee_kabupaten_tangerang' => 0,
        ])->assertSessionHasErrors('used_parts_section_product_ids.0');

        $this->assertNull(SystemSetting::val('used_parts_section_product_ids'));
    }

    /**
     * @return array{User, Store, Category}
     */
    private function catalogContext(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $store = Store::create([
            'user_id' => $admin->id,
            'name' => 'Digital Hook',
            'slug' => 'digihook',
        ]);
        $category = Category::create([
            'name' => 'Komponen PC',
            'slug' => 'komponen-pc',
            'is_active' => true,
        ]);

        return [$admin, $store, $category];
    }

    private function product(
        Store $store,
        Category $category,
        string $name,
        string $condition,
        int $stock = 5,
    ): Product {
        return Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'price' => 250_000,
            'stock' => $stock,
            'weight_gram' => 500,
            'condition' => $condition,
            'status' => 'active',
        ]);
    }
}
