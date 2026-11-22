<?php

namespace Tests\Feature\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seller = User::factory()->seller()->create();
        $this->buyer = User::factory()->buyer()->create();
    }

    public function test_seller_can_mark_paid_order_as_processing(): void
    {
        $order = Order::factory()->paid()->create(['user_id' => $this->buyer->id]);

        $response = $this->actingAs($this->seller)->post(route('seller.orders.process', $order));

        $response->assertRedirect();
        $this->assertSame(OrderStatus::PROCESSING, $order->fresh()->status);
    }

    public function test_seller_cannot_ship_order_without_courier_and_tracking_number(): void
    {
        $order = Order::factory()->processing()->create(['user_id' => $this->buyer->id]);

        $response = $this->actingAs($this->seller)->post(route('seller.orders.ship', $order), [
            'courier_name' => '',
            'tracking_number' => '',
        ]);

        $response->assertSessionHasErrors(['courier_name', 'tracking_number']);
        $this->assertSame(OrderStatus::PROCESSING, $order->fresh()->status);
    }

    public function test_seller_can_ship_order_with_valid_courier_and_tracking(): void
    {
        $order = Order::factory()->processing()->create(['user_id' => $this->buyer->id]);

        $response = $this->actingAs($this->seller)->post(route('seller.orders.ship', $order), [
            'courier_name' => 'JNE Express',
            'tracking_number' => 'JNE123456789',
        ]);

        $response->assertRedirect();
        $this->assertSame(OrderStatus::SHIPPED, $order->fresh()->status);
        $this->assertSame('JNE Express', $order->fresh()->courier_name);
        $this->assertSame('JNE123456789', $order->fresh()->tracking_number);
    }

    public function test_only_buyer_owner_can_confirm_receipt_to_complete_order(): void
    {
        $order = Order::factory()->shipped()->create(['user_id' => $this->buyer->id]);
        $otherBuyer = User::factory()->buyer()->create();

        // 1. Other buyer is forbidden from confirming receipt
        $response = $this->actingAs($otherBuyer)->post(route('buyer.orders.confirm-receipt', $order));
        $response->assertStatus(403);
        $this->assertSame(OrderStatus::SHIPPED, $order->fresh()->status);

        // 2. Owner buyer successfully completes order
        $response = $this->actingAs($this->buyer)->post(route('buyer.orders.confirm-receipt', $order));
        $response->assertRedirect();
        $this->assertSame(OrderStatus::COMPLETED, $order->fresh()->status);
    }
}
