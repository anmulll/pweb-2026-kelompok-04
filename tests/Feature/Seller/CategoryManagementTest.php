<?php

namespace Tests\Feature\Seller;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'role' => UserRole::SELLER,
        ]);

        $this->buyer = User::factory()->create([
            'role' => UserRole::BUYER,
        ]);
    }

    public function test_guest_is_redirected_when_accessing_seller_categories(): void
    {
        $response = $this->get(route('seller.categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_buyer_is_forbidden_from_accessing_seller_categories(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('seller.categories.index'));

        $response->assertStatus(403);
    }

    public function test_seller_can_view_category_list(): void
    {
        Category::factory()->create(['name' => 'iPhone', 'slug' => 'iphone']);

        $response = $this->actingAs($this->seller)->get(route('seller.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('iPhone');
    }

    public function test_seller_can_create_category_with_valid_data(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.categories.store'), [
            'name' => 'MacBook',
            'slug' => 'macbook',
            'description' => 'Laptop bertenaga Apple Silicon',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('seller.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'MacBook',
            'slug' => 'macbook',
            'is_active' => true,
        ]);
    }

    public function test_category_slug_must_be_unique(): void
    {
        Category::create([
            'name' => 'iPad Original',
            'slug' => 'ipad',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->post(route('seller.categories.store'), [
            'name' => 'iPad Baru',
            'slug' => 'ipad',
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_seller_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Apple Watch Series',
            'slug' => 'apple-watch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->put(route('seller.categories.update', $category), [
            'name' => 'Apple Watch & Ultra',
            'slug' => 'apple-watch',
            'description' => 'Jam tangan pintar Apple',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('seller.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Apple Watch & Ultra',
        ]);
    }

    public function test_seller_can_toggle_category_status(): void
    {
        $category = Category::create([
            'name' => 'AirPods',
            'slug' => 'airpods',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->post(route('seller.categories.toggle', $category));

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::create([
            'name' => 'Aksesoris',
            'slug' => 'aksesoris',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'MagSafe Charger',
            'slug' => 'magsafe-charger',
            'model' => 'MagSafe 15W',
            'sku' => 'MAG-15W-01',
            'condition' => 'new',
            'price' => 799000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->delete(route('seller.categories.destroy', $category));

        $response->assertSessionHasErrors('category');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_category_without_products_can_be_deleted(): void
    {
        $category = Category::create([
            'name' => 'Vision Pro',
            'slug' => 'vision-pro',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->delete(route('seller.categories.destroy', $category));

        $response->assertRedirect(route('seller.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
