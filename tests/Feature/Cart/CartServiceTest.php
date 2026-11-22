<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buyer without a cart should receive a newly created cart.
     */
    public function test_buyer_can_create_a_cart(): void
    {
        $buyer = User::factory()->buyer()->create();

        $cart = app(CartService::class)->getCart($buyer);

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($buyer->id, $cart->user_id);

        $this->assertDatabaseHas('carts', [
            'user_id' => $buyer->id,
        ]);

        $this->assertDatabaseCount('carts', 1);
    }

    /**
     * Buyer with an existing cart should receive the same cart.
     */
    public function test_buyer_reuses_existing_cart(): void
    {
        $buyer = User::factory()->buyer()->create();

        $existingCart = $buyer->cart()->create();

        $cart = app(CartService::class)->getCart($buyer);

        $this->assertEquals($existingCart->id, $cart->id);

        $this->assertDatabaseCount('carts', 1);
    }

    /**
     * Seller should not be allowed to access a shopping cart.
     */
    public function test_seller_cannot_access_a_cart(): void
    {
        $seller = User::factory()->seller()->create();

        $this->expectException(AuthorizationException::class);

        app(CartService::class)->getCart($seller);
    }

    /**
     * Different buyers should have different carts.
     */
    public function test_each_buyer_has_a_separate_cart(): void
    {
        $buyerOne = User::factory()->buyer()->create();
        $buyerTwo = User::factory()->buyer()->create();

        $service = app(CartService::class);

        $cartOne = $service->getCart($buyerOne);
        $cartTwo = $service->getCart($buyerTwo);

        $this->assertNotEquals($cartOne->id, $cartTwo->id);

        $this->assertEquals($buyerOne->id, $cartOne->user_id);
        $this->assertEquals($buyerTwo->id, $cartTwo->user_id);

        $this->assertDatabaseCount('carts', 2);
    }
}
