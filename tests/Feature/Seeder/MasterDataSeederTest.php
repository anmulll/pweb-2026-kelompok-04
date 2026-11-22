<?php

namespace Tests\Feature\Seeder;

use App\Enums\ProductCondition;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_data_seeder_populates_required_domain_data(): void
    {
        $this->seed(MasterDataSeeder::class);

        // Verify users
        $this->assertDatabaseHas('users', ['email' => 'seller@istore.test', 'role' => 'seller']);
        $this->assertDatabaseHas('users', ['email' => 'buyer@istore.test', 'role' => 'buyer']);

        // Verify categories
        $this->assertGreaterThanOrEqual(6, Category::count());
        $this->assertDatabaseHas('categories', ['slug' => 'iphone']);
        $this->assertDatabaseHas('categories', ['slug' => 'macbook']);

        // Verify products
        $this->assertGreaterThanOrEqual(8, Product::count());

        // Verify brand new product
        $this->assertDatabaseHas('products', [
            'sku' => 'IP15PM-256-NT',
            'condition' => ProductCondition::NEW->value,
            'is_active' => true,
        ]);

        // Verify used product with inspection details
        $this->assertDatabaseHas('products', [
            'sku' => 'IP14P-128-DP-SEC01',
            'condition' => ProductCondition::USED->value,
            'condition_grade' => 'A',
            'stock' => 1,
            'is_active' => true,
        ]);

        // Verify promotions
        $this->assertGreaterThanOrEqual(2, Promotion::count());
        $this->assertDatabaseHas('promotions', ['is_active' => true]);
    }
}
