<?php

namespace Tests\Feature\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLifecycleAndRestockTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $otherBuyer;
    protected User $seller;
    protected Category $category;
    protected Product $product;
    protected Order $order;
    protected Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->buyer()->create();
        $this->otherBuyer = User::factory()->buyer()->create();
        $this->seller = User::factory()->seller()->create();

        $this->category = Category::create([
            'name' => 'Apple Watch',
            'slug' => 'apple-watch',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Apple Watch Ultra 2',
            'model' => 'Apple Watch Ultra 2',
            'sku' => 'AWU2-TITANIUM',
            'condition' => 'new',
            'price' => 15999000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'user_id' => $this->buyer->id,
            'order_code' => 'ORD-LIFECYCLE-001',
            'status' => OrderStatus::PAID,
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Merdeka No. 45',
            'subtotal' => 15999000,
            'shipping_cost' => 0,
            'total_amount' => 15999000,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'product_name_snapshot' => $this->product->name,
            'sku_snapshot' => $this->product->sku,
            'condition_snapshot' => 'new',
            'unit_price_snapshot' => 15999000,
            'quantity' => 2,
            'line_total' => 31998000,
        ]);

        $this->payment = Payment::create([
            'order_id' => $this->order->id,
            'gateway_order_id' => $this->order->order_code,
            'provider' => 'doku_sandbox',
            'status' => PaymentStatus::PAID,
            'amount' => 15999000,
        ]);
    }

    public function test_full_order_lifecycle_paid_to_processing_to_shipped_to_completed(): void
    {
        // 1. Seller marks as processing
        $processResponse = $this->actingAs($this->seller)
            ->postJson(route('seller.orders.process', $this->order->id));

        $processResponse->assertStatus(200);
        $this->assertEquals(OrderStatus::PROCESSING, $this->order->fresh()->status);

        // 2. Seller ships order with courier and tracking number
        $shipResponse = $this->actingAs($this->seller)
            ->postJson(route('seller.orders.ship', $this->order->id), [
                'courier_name' => 'JNE Express',
                'tracking_number' => 'JNE123456789',
            ]);

        $shipResponse->assertStatus(200);
        $this->assertEquals(OrderStatus::SHIPPED, $this->order->fresh()->status);
        $this->assertEquals('JNE Express', $this->order->fresh()->courier_name);
        $this->assertEquals('JNE123456789', $this->order->fresh()->tracking_number);

        // 3. Buyer confirms receipt (completed)
        $confirmResponse = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.confirm-receipt', $this->order->id));

        $confirmResponse->assertStatus(200);
        $this->assertEquals(OrderStatus::COMPLETED, $this->order->fresh()->status);
    }

    public function test_shipping_without_courier_or_tracking_number_is_rejected(): void
    {
        $this->order->update(['status' => OrderStatus::PROCESSING]);

        $response = $this->actingAs($this->seller)
            ->postJson(route('seller.orders.ship', $this->order->id), [
                'courier_name' => '',
                'tracking_number' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['courier_name', 'tracking_number']);
    }

    public function test_only_buyer_owner_can_confirm_receipt(): void
    {
        $this->order->update(['status' => OrderStatus::SHIPPED]);

        // Seller cannot complete order
        $sellerAttempt = $this->actingAs($this->seller)
            ->postJson(route('buyer.orders.confirm-receipt', $this->order->id));
        $sellerAttempt->assertStatus(403);

        // Another buyer cannot complete order
        $otherBuyerAttempt = $this->actingAs($this->otherBuyer)
            ->postJson(route('buyer.orders.confirm-receipt', $this->order->id));
        $otherBuyerAttempt->assertStatus(403);
    }

    public function test_idempotent_restock_restores_stock_exactly_once(): void
    {
        $initialStock = $this->product->stock; // 10

        // Cancel order via seller
        $cancelResponse = $this->actingAs($this->seller)
            ->postJson(route('seller.orders.cancel', $this->order->id));

        $cancelResponse->assertStatus(200);
        $this->assertEquals(OrderStatus::CANCELLED, $this->order->fresh()->status);
        $this->assertNotNull($this->order->fresh()->stock_restored_at);

        // Stock restored by quantity 2: 10 + 2 = 12
        $this->assertEquals($initialStock + 2, $this->product->fresh()->stock);

        // Simulate duplicate cancel / restock attempt directly via service
        $restoredAgain = app(\App\Services\OrderStatusService::class)->restoreStock($this->order->fresh());
        $this->assertFalse($restoredAgain);

        // Stock must still be 12, not 14
        $this->assertEquals($initialStock + 2, $this->product->fresh()->stock);
    }
}
