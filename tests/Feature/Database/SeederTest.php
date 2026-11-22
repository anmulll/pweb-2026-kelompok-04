<?php

namespace Tests\Feature\Database;

use App\Enums\ConditionGrade;
use App\Enums\FunctionalStatus;
use App\Enums\ProductCondition;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\OrderDemoSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\PromotionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_creates_seller_and_buyers_with_correct_roles(): void
    {
        $this->seed(UserSeeder::class);

        $seller = User::where('email', 'seller@istore.test')->first();
        $this->assertNotNull($seller);
        $this->assertSame(UserRole::Seller, $seller->role);

        $buyer1 = User::where('email', 'buyer@istore.test')->first();
        $this->assertNotNull($buyer1);
        $this->assertSame(UserRole::Buyer, $buyer1->role);

        $buyer2 = User::where('email', 'buyer2@istore.test')->first();
        $this->assertNotNull($buyer2);
        $this->assertSame(UserRole::Buyer, $buyer2->role);
    }

    public function test_category_seeder_creates_six_active_categories(): void
    {
        $this->seed(CategorySeeder::class);

        $this->assertDatabaseCount('categories', 6);
        $this->assertDatabaseHas('categories', ['slug' => 'iphone', 'is_active' => true]);
        $this->assertDatabaseHas('categories', ['slug' => 'macbook', 'is_active' => true]);
        $this->assertDatabaseHas('categories', ['slug' => 'ipad', 'is_active' => true]);
        $this->assertDatabaseHas('categories', ['slug' => 'apple-watch', 'is_active' => true]);
        $this->assertDatabaseHas('categories', ['slug' => 'airpods', 'is_active' => true]);
        $this->assertDatabaseHas('categories', ['slug' => 'aksesori', 'is_active' => true]);
    }

    public function test_product_seeder_creates_new_and_used_products_with_inspection_data(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        $newProduct = Product::where('sku', 'IP15PM-256-NT')->first();
        $this->assertNotNull($newProduct);
        $this->assertSame(ProductCondition::NEW, $newProduct->condition);
        $this->assertTrue($newProduct->is_active);

        $usedProduct = Product::where('sku', 'IP14P-128-DP-SEC01')->first();
        $this->assertNotNull($usedProduct);
        $this->assertSame(ProductCondition::USED, $usedProduct->condition);
        $this->assertSame(ConditionGrade::A, $usedProduct->condition_grade);
        $this->assertEquals(91, $usedProduct->battery_health);
        $this->assertEquals(1, $usedProduct->stock);
        $this->assertNotNull($usedProduct->warranty_info);
        $this->assertNotNull($usedProduct->included_accessories);
    }

    public function test_promotion_seeder_creates_active_and_inactive_promotions(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(PromotionSeeder::class);

        $this->assertTrue(Promotion::where('is_active', true)->exists());
        $this->assertTrue(Promotion::where('is_active', false)->exists());
    }

    public function test_database_seeder_is_idempotent_and_does_not_duplicate_records(): void
    {
        $this->seed(DatabaseSeeder::class);
        $userCount = User::count();
        $categoryCount = Category::count();
        $productCount = Product::count();
        $orderCount = Order::count();

        // Run second time
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals($userCount, User::count());
        $this->assertEquals($categoryCount, Category::count());
        $this->assertEquals($productCount, Product::count());
        $this->assertEquals($orderCount, Order::count());
    }

    public function test_database_seeder_blocks_demo_seeder_on_production_environment(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', ['--force' => true]);

        $this->assertEquals(0, User::count());
        $this->assertEquals(0, Category::count());
        $this->assertEquals(0, Product::count());
    }
}
