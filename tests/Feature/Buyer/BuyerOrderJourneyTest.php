<?php

namespace Tests\Feature\Buyer;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerOrderJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $otherBuyer;
    protected User $seller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buyer = User::factory()->buyer()->create();
        $this->otherBuyer = User::factory()->buyer()->create();
        $this->seller = User::factory()->seller()->create();
    }

    public function test_buyer_can_view_dashboard_with_order_metrics(): void
    {
        // 1 pending order, 1 completed order
        Order::factory()->create([
            'user_id' => $this->buyer->id,
            'status' => OrderStatus::PENDING_PAYMENT,
        ]);
        Order::factory()->create([
            'user_id' => $this->buyer->id,
            'status' => OrderStatus::COMPLETED,
        ]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Pembeli');
        $response->assertSee($this->buyer->name);
    }

    public function test_buyer_can_view_orders_index_with_only_their_orders(): void
    {
        $buyerOrder = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'order_code' => 'ORD-BUYER-001',
        ]);

        $otherOrder = Order::factory()->create([
            'user_id' => $this->otherBuyer->id,
            'order_code' => 'ORD-OTHER-002',
        ]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('ORD-BUYER-001');
        $response->assertDontSee('ORD-OTHER-002');
    }

    public function test_buyer_can_view_order_detail_page(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'order_code' => 'ORD-DETAIL-123',
            'status' => OrderStatus::PENDING_PAYMENT,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'status' => PaymentStatus::PENDING,
            'provider' => 'doku_sandbox',
            'gateway_checkout_url' => 'https://sandbox.doku.com/checkout/123',
        ]);

        $product = Product::factory()->create([
            'name' => 'iPhone 15 Pro Max',
            'sku' => 'IPH15PM-256-BLK',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => 'iPhone 15 Pro Max',
            'sku_snapshot' => 'IPH15PM-256-BLK',
            'condition_snapshot' => 'new',
            'unit_price_snapshot' => 20000000,
            'quantity' => 1,
            'line_total' => 20000000,
        ]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('ORD-DETAIL-123');
        $response->assertSee('iPhone 15 Pro Max');
        $response->assertSee('Menunggu Pembayaran');
        $response->assertSee('Lanjutkan Pembayaran DOKU');
    }

    public function test_buyer_cannot_view_other_buyers_order(): void
    {
        $otherOrder = Order::factory()->create([
            'user_id' => $this->otherBuyer->id,
        ]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.orders.show', $otherOrder));

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login_from_orders(): void
    {
        $response = $this->get(route('buyer.orders.index'));

        $response->assertRedirect(route('login'));
    }
}
