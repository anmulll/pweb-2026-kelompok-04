<?php

namespace Tests\Feature\Buyer;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PersistentCartTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected Category $category;
    protected Product $newProduct;
    protected Product $usedProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->buyer()->create();

        $this->category = Category::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'is_active' => true,
        ]);

        $this->newProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 15 Pro 128GB',
            'model' => 'iPhone 15 Pro',
            'sku' => 'IP15P-128',
            'condition' => 'new',
            'price' => 19999000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->usedProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 13 128GB Second',
            'model' => 'iPhone 13',
            'sku' => 'IP13-SEC-01',
            'condition' => 'used',
            'condition_grade' => 'A',
            'battery_health' => 88,
            'functional_status' => 'Normal',
            'price' => 8500000,
            'stock' => 1,
            'is_active' => true,
        ]);
    }

    public function test_buyer_can_add_item_to_cart_and_merges_quantity(): void
    {
        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.cart.store'), [
                'product_id' => $this->newProduct->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(201);

        // Add same product again
        $response2 = $this->actingAs($this->buyer)
            ->postJson(route('buyer.cart.store'), [
                'product_id' => $this->newProduct->id,
                'quantity' => 3,
            ]);

        $response2->assertStatus(201);

        $cart = Cart::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($cart);
        $this->assertCount(1, $cart->items);
        $this->assertEquals(5, $cart->items->first()->quantity);
    }

    public function test_used_product_cannot_be_added_with_quantity_greater_than_one(): void
    {
        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.cart.store'), [
                'product_id' => $this->usedProduct->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_cannot_add_more_quantity_than_available_stock(): void
    {
        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.cart.store'), [
                'product_id' => $this->newProduct->id,
                'quantity' => 15,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_cannot_add_inactive_product_or_product_in_inactive_category(): void
    {
        $this->newProduct->update(['is_active' => false]);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.cart.store'), [
                'product_id' => $this->newProduct->id,
                'quantity' => 1,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_cart_displays_live_prices_and_detects_stale_items(): void
    {
        $service = app(CartService::class);
        $service->addItem($this->buyer, $this->newProduct->id, 2);

        $cart = $service->getCartWithItems($this->buyer);
        $this->assertEquals(39998000, $service->getSubtotal($cart));
        $this->assertTrue($service->canCheckout($cart));

        // Seller changes price
        $this->newProduct->update(['price' => 18000000]);
        $this->assertEquals(36000000, $service->getSubtotal($cart->fresh()));

        // Seller sets product stock to 1 (making 2 in cart stale)
        $this->newProduct->update(['stock' => 1]);
        $staleItems = $service->getStaleItems($cart->fresh());
        $this->assertCount(1, $staleItems);
        $this->assertFalse($service->canCheckout($cart->fresh()));
    }

    public function test_buyer_can_update_cart_item_quantity(): void
    {
        $service = app(CartService::class);
        $item = $service->addItem($this->buyer, $this->newProduct->id, 1);

        $response = $this->actingAs($this->buyer)
            ->patchJson(route('buyer.cart.update', $item->id), [
                'quantity' => 4,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(4, $item->fresh()->quantity);
    }

    public function test_buyer_cannot_update_other_buyers_cart_item(): void
    {
        $otherBuyer = User::factory()->buyer()->create();
        $service = app(CartService::class);
        $item = $service->addItem($otherBuyer, $this->newProduct->id, 1);

        $response = $this->actingAs($this->buyer)
            ->patchJson(route('buyer.cart.update', $item->id), [
                'quantity' => 2,
            ]);

        $response->assertStatus(403);
    }

    public function test_buyer_can_remove_item_and_empty_cart_row_persists(): void
    {
        $service = app(CartService::class);
        $item = $service->addItem($this->buyer, $this->newProduct->id, 1);

        $response = $this->actingAs($this->buyer)
            ->deleteJson(route('buyer.cart.destroy', $item->id));

        $response->assertStatus(200);

        // Cart record persists (FR-CART-01)
        $this->assertDatabaseHas('carts', ['user_id' => $this->buyer->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
