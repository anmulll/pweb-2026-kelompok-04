<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected Category $activeCategory;
    protected Category $inactiveCategory;
    protected Product $activeProductInActiveCategory;
    protected Product $inactiveProductInActiveCategory;
    protected Product $activeProductInInactiveCategory;
    protected Product $usedProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::factory()->create(['role' => 'seller']);

        // 1. Active Category
        $this->activeCategory = Category::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'is_active' => true,
        ]);

        // 2. Inactive Category
        $this->inactiveCategory = Category::create([
            'name' => 'Aksesoris Nonaktif',
            'slug' => 'aksesoris-nonaktif',
            'is_active' => false,
        ]);

        // 3. Active product in active category (Should be VISIBLE)
        $this->activeProductInActiveCategory = Product::create([
            'category_id' => $this->activeCategory->id,
            'name' => 'iPhone 15 Pro Max 256GB',
            'model' => 'iPhone 15 Pro Max',
            'sku' => 'IP15PM-256',
            'condition' => 'new',
            'price' => 22999000,
            'stock' => 10,
            'is_active' => true,
        ]);

        // 4. Inactive product in active category (Must be HIDDEN by SD-01)
        $this->inactiveProductInActiveCategory = Product::create([
            'category_id' => $this->activeCategory->id,
            'name' => 'iPhone 12 Nonaktif',
            'model' => 'iPhone 12',
            'sku' => 'IP12-NONAKTIF',
            'condition' => 'new',
            'price' => 7000000,
            'stock' => 5,
            'is_active' => false,
        ]);

        // 5. Active product in inactive category (Must be HIDDEN by SD-01)
        $this->activeProductInInactiveCategory = Product::create([
            'category_id' => $this->inactiveCategory->id,
            'name' => 'Kabel Nonaktif Tersembunyi',
            'model' => 'Kabel USB-C',
            'sku' => 'CABLE-HIDDEN',
            'condition' => 'new',
            'price' => 299000,
            'stock' => 50,
            'is_active' => true,
        ]);

        // 6. Used unit with inspection data
        $this->usedProduct = Product::create([
            'category_id' => $this->activeCategory->id,
            'name' => 'iPhone 14 128GB Blue Second',
            'model' => 'iPhone 14',
            'sku' => 'IP14-128-BLU-SEC',
            'condition' => 'used',
            'condition_grade' => 'A',
            'battery_health' => 91,
            'functional_status' => 'Semua Normal',
            'defect_notes' => 'Mulus 98%',
            'included_accessories' => 'Fullset Box',
            'warranty_info' => 'Garansi Toko 1 Bulan',
            'price' => 10500000,
            'stock' => 1,
            'is_active' => true,
        ]);

        // Promotion active today
        Promotion::create([
            'created_by' => $seller->id,
            'title' => 'Spesial Launching iStore',
            'description' => 'Promo resmi pembukaan',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'is_active' => true,
        ]);
    }

    public function test_homepage_shows_promotions_and_active_products(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Spesial Launching iStore');
        $response->assertSee('iPhone 15 Pro Max 256GB');
        // SD-01: Inactive products must never show on homepage
        $response->assertDontSee('iPhone 12 Nonaktif');
        $response->assertDontSee('Kabel Nonaktif Tersembunyi');
    }

    public function test_catalog_enforces_sd01_visibility_rule(): void
    {
        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro Max 256GB');
        $response->assertSee('iPhone 14 128GB Blue Second');

        // Hidden per SD-01
        $response->assertDontSee('iPhone 12 Nonaktif');
        $response->assertDontSee('Kabel Nonaktif Tersembunyi');
    }

    public function test_product_detail_returns_404_if_product_is_inactive(): void
    {
        // FR-CAT-03: inactive product must return 404
        $response = $this->get(route('products.show', $this->inactiveProductInActiveCategory->id));

        $response->assertStatus(404);
    }

    public function test_product_detail_returns_404_if_parent_category_is_inactive(): void
    {
        // FR-CAT-03: active product under inactive category must return 404
        $response = $this->get(route('products.show', $this->activeProductInInactiveCategory->id));

        $response->assertStatus(404);
    }

    public function test_product_detail_returns_200_for_active_product_in_active_category(): void
    {
        $response = $this->get(route('products.show', $this->activeProductInActiveCategory->id));

        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro Max 256GB');
        $response->assertSee('Perangkat Baru (Brand New)');
        // Second-hand inspection panel must not show for brand new product
        $response->assertDontSee('Hasil Inspeksi Unit Second');
    }

    public function test_used_product_detail_displays_second_hand_inspection_panel(): void
    {
        $response = $this->get(route('products.show', $this->usedProduct->id));

        $response->assertStatus(200);
        $response->assertSee('iPhone 14 128GB Blue Second');
        $response->assertSee('Unit Second Terinspeksi');
        // Must show inspection panel
        $response->assertSee('Hasil Inspeksi Unit Second');
        $response->assertSee('Grade A');
        $response->assertSee('91%');
        $response->assertSee('Semua Normal');
        $response->assertSee('Mulus 98%');
        $response->assertSee('Fullset Box');
    }

    public function test_catalog_search_by_name_or_model(): void
    {
        $response = $this->get(route('catalog.index', ['search' => 'Pro Max']));

        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro Max 256GB');
        $response->assertDontSee('iPhone 14 128GB Blue Second');
    }

    public function test_catalog_filter_by_condition(): void
    {
        $response = $this->get(route('catalog.index', ['condition' => 'used']));

        $response->assertStatus(200);
        $response->assertSee('iPhone 14 128GB Blue Second');
        $response->assertDontSee('iPhone 15 Pro Max 256GB');
    }

    public function test_catalog_filter_by_grade(): void
    {
        $response = $this->get(route('catalog.index', ['grade' => 'A']));

        $response->assertStatus(200);
        $response->assertSee('iPhone 14 128GB Blue Second');
    }
}
