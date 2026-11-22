<?php

namespace Tests\Feature\Checkout;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\PaymentGatewayResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoPhaseCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->buyer()->create();

        $this->category = Category::create([
            'name' => 'MacBook',
            'slug' => 'macbook',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'MacBook Air M2 256GB',
            'model' => 'MacBook Air M2',
            'sku' => 'MBA-M2-256',
            'condition' => 'new',
            'price' => 17000000,
            'stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_phase1_atomic_checkout_creates_order_items_payment_decrements_stock_and_clears_cart(): void
    {
        $mockGateway = \Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('createPayment')
            ->once()
            ->andReturn([
                'gateway_checkout_url' => 'https://sandbox.doku.com/checkout/test12345',
                'gateway_token' => 'TOKEN12345',
                'expired_at' => now()->addMinutes(60)->toDateTimeString(),
                'raw_response' => ['status' => 'SUCCESS'],
            ]);

        $resolverMock = \Mockery::mock(PaymentGatewayResolver::class);
        $resolverMock->shouldReceive('resolve')
            ->with('doku_sandbox')
            ->andReturn($mockGateway);

        $this->app->instance(PaymentGatewayResolver::class, $resolverMock);

        $cartService = app(CartService::class);
        $cartService->addItem($this->buyer, $this->product->id, 2);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.checkout.store'), [
                'recipient_name' => 'Budi Santoso',
                'recipient_phone' => '081234567890',
                'shipping_address' => 'Jl. Merdeka No. 45, Jakarta',
                'expected_total' => 34000000,
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'order' => ['id', 'order_code', 'status', 'total_amount'],
            'checkout_url',
        ]);

        // Assert Order created
        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::PENDING_PAYMENT, $order->status);
        $this->assertEquals(34000000, (float) $order->total_amount);

        // Assert OrderItems snapshot
        $this->assertCount(1, $order->items);
        $item = $order->items->first();
        $this->assertEquals($this->product->name, $item->product_name_snapshot);
        $this->assertEquals($this->product->sku, $item->sku_snapshot);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(34000000, (float) $item->line_total);

        // Assert stock decremented atomically from 5 to 3
        $this->assertEquals(3, $this->product->fresh()->stock);

        // Assert cart items cleared but cart row remains
        $this->assertDatabaseHas('carts', ['user_id' => $this->buyer->id]);
        $this->assertDatabaseMissing('cart_items', ['cart_id' => $order->user->cart->id]);

        // Assert Payment row
        $this->assertNotNull($order->payment);
        $this->assertEquals(PaymentStatus::PENDING, $order->payment->status);
        $this->assertEquals('https://sandbox.doku.com/checkout/test12345', $order->payment->gateway_checkout_url);
        $this->assertEquals('TOKEN12345', $order->payment->gateway_token);
    }

    public function test_checkout_fails_if_expected_total_mismatched(): void
    {
        $cartService = app(CartService::class);
        $cartService->addItem($this->buyer, $this->product->id, 1);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.checkout.store'), [
                'recipient_name' => 'Budi Santoso',
                'recipient_phone' => '081234567890',
                'shipping_address' => 'Jl. Merdeka No. 45, Jakarta',
                'expected_total' => 15000000, // actual is 17000000
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['expected_total']);

        // No order created, stock untouched
        $this->assertEquals(5, $this->product->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_fails_if_cart_contains_stale_or_insufficient_stock(): void
    {
        $cartService = app(CartService::class);
        $cartService->addItem($this->buyer, $this->product->id, 3);

        // Another buyer takes stock, leaving only 1 available
        $this->product->update(['stock' => 1]);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.checkout.store'), [
                'recipient_name' => 'Budi Santoso',
                'recipient_phone' => '081234567890',
                'shipping_address' => 'Jl. Merdeka No. 45, Jakarta',
                'expected_total' => 51000000,
            ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(1, $this->product->fresh()->stock);
    }

    public function test_phase2_gateway_failure_retains_phase1_order_and_stock(): void
    {
        $mockGateway = \Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('createPayment')
            ->once()
            ->andThrow(new \RuntimeException('Connection to DOKU timed out.'));

        $resolverMock = \Mockery::mock(PaymentGatewayResolver::class);
        $resolverMock->shouldReceive('resolve')
            ->with('doku_sandbox')
            ->andReturn($mockGateway);

        $this->app->instance(PaymentGatewayResolver::class, $resolverMock);

        $cartService = app(CartService::class);
        $cartService->addItem($this->buyer, $this->product->id, 1);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.checkout.store'), [
                'recipient_name' => 'Budi Santoso',
                'recipient_phone' => '081234567890',
                'shipping_address' => 'Jl. Merdeka No. 45, Jakarta',
            ]);

        $response->assertStatus(201);

        // Order and stock decrement are preserved for safe retry
        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::PENDING_PAYMENT, $order->status);
        $this->assertEquals(4, $this->product->fresh()->stock);
        $this->assertNull($order->payment->gateway_checkout_url);
    }
}
