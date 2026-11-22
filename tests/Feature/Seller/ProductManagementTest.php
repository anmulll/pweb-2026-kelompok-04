<?php

namespace Tests\Feature\Seller;

use App\Enums\ConditionGrade;
use App\Enums\ProductCondition;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'role' => UserRole::Seller,
        ]);

        $this->buyer = User::factory()->create([
            'role' => UserRole::Buyer,
        ]);

        $this->category = Category::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_from_seller_products(): void
    {
        $response = $this->get(route('seller.products.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_buyer_is_forbidden_from_seller_products(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('seller.products.index'));

        $response->assertStatus(403);
    }

    public function test_seller_can_create_brand_new_product(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->seller)->post(route('seller.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'iPhone 15 Pro 128GB Black Titanium',
            'model' => 'iPhone 15 Pro',
            'sku' => 'IP15P-128-BT',
            'storage' => '128GB',
            'color' => 'Black Titanium',
            'condition' => 'new',
            'price' => 18999000,
            'stock' => 15,
            'description' => 'Brand new original Apple device',
            'is_active' => 1,
            'image' => UploadedFile::fake()->image('iphone15pro.jpg'),
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'category_id' => $this->category->id,
            'name' => 'iPhone 15 Pro 128GB Black Titanium',
            'model' => 'iPhone 15 Pro',
            'sku' => 'IP15P-128-BT',
            'condition' => ProductCondition::NEW->value,
            'price' => '18999000.00',
            'stock' => 15,
            'is_active' => true,
        ]);
    }

    public function test_seller_can_create_second_hand_unit_with_inspection_data(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'iPhone 13 128GB Midnight Second',
            'model' => 'iPhone 13',
            'sku' => 'IP13-128-MID-SEC01',
            'storage' => '128GB',
            'color' => 'Midnight',
            'condition' => 'used',
            'price' => 8500000,
            'stock' => 1, // Second-hand stock must be 0 or 1
            'condition_grade' => 'A',
            'battery_health' => 89,
            'functional_status' => 'Semua Normal, Face ID Aktif',
            'defect_notes' => 'Lecet halus tidak kasat mata pada bezel bawah',
            'included_accessories' => 'Fullset Box + Kabel Lightning Original',
            'warranty_info' => 'Garansi Toko 1 Bulan',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'sku' => 'IP13-128-MID-SEC01',
            'condition' => ProductCondition::USED->value,
            'condition_grade' => ConditionGrade::A->value,
            'battery_health' => 89,
            'stock' => 1,
        ]);
    }

    public function test_second_hand_unit_stock_cannot_exceed_one(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'iPhone 12 64GB',
            'model' => 'iPhone 12',
            'sku' => 'IP12-64-SEC01',
            'condition' => 'used',
            'price' => 6000000,
            'stock' => 5, // Invalid for used unit: must be 0 or 1
            'condition_grade' => 'B',
            'functional_status' => 'Normal',
        ]);

        $response->assertSessionHasErrors('stock');
    }

    public function test_price_must_be_strictly_positive(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'iPhone Gratis',
            'model' => 'iPhone',
            'sku' => 'IP-FREE',
            'condition' => 'new',
            'price' => 0, // Invalid: price > 0
            'stock' => 5,
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_battery_health_cannot_exceed_100(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'iPhone Baterai Super',
            'model' => 'iPhone',
            'sku' => 'IP-BAT-150',
            'condition' => 'used',
            'price' => 5000000,
            'stock' => 1,
            'condition_grade' => 'A',
            'functional_status' => 'Normal',
            'battery_health' => 150, // Invalid: cannot exceed 100
        ]);

        $response->assertSessionHasErrors('battery_health');
    }

    public function test_seller_can_update_product(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 14 128GB Starlight',
            'model' => 'iPhone 14',
            'sku' => 'IP14-128-SL',
            'condition' => 'new',
            'price' => 12500000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->put(route('seller.products.update', $product), [
            'category_id' => $this->category->id,
            'name' => 'iPhone 14 128GB Starlight (Promo)',
            'model' => 'iPhone 14',
            'sku' => 'IP14-128-SL',
            'condition' => 'new',
            'price' => 11999000,
            'stock' => 4,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => '11999000.00',
            'stock' => 4,
        ]);
    }

    public function test_seller_can_toggle_product_status(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPad Air M2',
            'model' => 'iPad Air',
            'sku' => 'IPADAIR-M2',
            'condition' => 'new',
            'price' => 11000000,
            'stock' => 8,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->post(route('seller.products.toggle', $product));

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }

    public function test_activating_used_unit_requires_inspection_fields(): void
    {
        $usedProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 11 Second Inactive',
            'model' => 'iPhone 11',
            'sku' => 'IP11-SEC',
            'condition' => 'used',
            'price' => 4500000,
            'stock' => 1,
            'is_active' => false,
            // Missing image, condition_grade, functional_status
        ]);

        $response = $this->actingAs($this->seller)->post(route('seller.products.toggle', $usedProduct));

        $response->assertSessionHasErrors('product');
        $this->assertDatabaseHas('products', [
            'id' => $usedProduct->id,
            'is_active' => false,
        ]);
    }
}
